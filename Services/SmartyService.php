<?php
/**
 * https://pear.php.net/package/HTML_Template_Flexy/docs/latest/__filesource/fsource_HTML_Template_Flexy__HTML_Template_Flexy-1.3.13HTMLTemplateFlexyCompilerSmartyConvertor.php.html.
 */
declare(strict_types=1);

namespace Modules\Xot\Services;

use Illuminate\Support\Facades\File;

class SmartyService {
    /**
     * stack for conditional and closers.
     */
    public array $stack = ['if' => 0];

    /**
     * The core work of parsing a smarty template and converting it into flexy.
     *
     * @param   string       the contents of the smarty template
     *
     * @return string the flexy version of the template
     *
     * @see      see also methods.....
     */
    public function convert(string $file) {
        if (! File::exists($file)) {
            dddx(['message' => $file.' not exists']);
        }
        $content = file_get_contents($file);
        /* solo per test
        $content = '\t{if $isMobile}
      \t{include file="../templates/interno/header_mobile.tpl"}
      \t{/if}';
      */

        $leftq = preg_quote('{', '!');
        $rightq = preg_quote('}', '!');
        preg_match_all('!'.$leftq."\s*(.*?)\s*".$rightq.'!s', $content, $matches);
        $tags = $matches[1];
        // find all the tags/text...
        $text = preg_split('!'.$leftq.'.*?'.$rightq.'!s', $content);
        $max_text = count($text);
        $max_tags = count($tags);
        for ($i = 0; $i < $max_tags; ++$i) {
            $compiled_tags[] = $this->compileTag($tags[$i]);
        }
        // error handling for closing tags.
        $data = '';
        for ($i = 0; $i < $max_tags; ++$i) {
            $data .= $text[$i].$compiled_tags[$i];
        }
        $data .= $text[$i];

        dddx(['original' => $content, 'converted' => $data]);
    }

    public function compileTag($str) {
        // skip comments
        if (('*' == $str[0]) && ('*' == substr($str, -1, 1))) {
            return '';
        }
        switch ($str[0]) {
            case '$': return $this->convertVar($str); // its a var
            case '#': return $this->convertConfigVar($str); // its a config var
            case '%': return "<!-- what is this? $str -->"; // wtf does this do
        }

        // this is where it gets messy
        // this is very slow - but what the hell
        //   - its only done once
        //   - its alot more readable than a long regext.
        //   - it doesnt infringe on copyright...

        switch (true) {
            case preg_match('/^config_load\s/', $str):
                // convert to $t->TemplateConfigLoad()

                $args = $this->convertAttributesToKeyVal(substr($str, strpos($str, ' ')));

                return '{plugin(#smartyConfigLoad#,#'.$args['file'].'#,#'.$args['section'].'#)}';

            case preg_match('/^include\s/', $str):
                // convert to $t->TemplateConfigLoad()

                $args = $this->convertAttributesToKeyVal(substr($str, strpos($str, ' ')));

                //return '{plugin(#smartyInclude#,#'.$args['file'].'#)}';
                $blade_file = str_replace('.tpl', '', $args['file']);

                return '@include('.$blade_file.')';

            case 'ldelim' == $str:
                return '{';

            case 'rdelim' == $str:
                return '}';

            case preg_match('/^if \$(\S+)$/', $str, $matches):
            case preg_match('/^if \$(\S+)\seq\s""$/', $str, $matches):
                // simple if variable..

                // convert to : {if:sssssss}

                $this->stack['if']++;

                $var = $this->convertVar('$'.$matches[1]);

                //return '{if:'.substr($var, 1);
                return '@if('.substr($var, 1, -1).')'.'['.__LINE__.']';

            case preg_match('/^if #(\S+)#$/', $str, $matches):
            case preg_match('/^if #(\S+)#\sne\s""$/', $str, $matches):
                // simple if variable..

                // convert to : {if:sssssss}

                $this->stack['if']++;

                $var = $this->convertConfigVar('#'.$matches[1].'#');

                //return '{if:'.substr($var, 1);
                return '@if('.substr($var, 1).')'.'['.__LINE__.']';

            // negative matches

            case preg_match('/^if\s!\s\$(\S+)$/', $str, $matches):
            case preg_match('/^if \$(\S+)\seq\s""$/', $str, $matches):
                // simple if variable..

                // convert to : {if:sssssss}

                $this->stack['if']++;

                $var = $this->convertVar('$'.$matches[1]);

                //return '{if:!'.substr($var, 1);
                return '@if(!'.substr($var, 1).')';

             case 'else' == $str:
                if (! $this->stack['if']) {
                    break;
                }

                //return '{else:}';
                return '@else';

            case '/if' == $str:
                if (! $this->stack['if']) {
                    break;
                }

                --$this->stack['if'];

                //return '{end:}';
                return '@endif';
            case 'php' == $str:
                return '@php';
            case '/php' == $str:
                return '@endphp';
            case 'literal' == $str:
                return '@verbatim';
            case '/literal' == $str:
                return '@endverbatim';
        }

        return "<!--   UNSUPPORTED TAG: $str FOUND -->";
    }

    /**
     * convert a smarty var into a flexy one.
     *
     * @param   string       the inside of the smart tag
     *
     * @return string a flexy version of it
     */
    public function convertVar($str) {
        // look for modfiers first.

        $mods = explode('|', $str);

        $var = array_shift($mods);

        $var = substr($var, 1); // strip $

        // various formats :

        // aaaa.bbbb.cccc => aaaa[bbbb][cccc]

        // aaaa[bbbb] => aaa[bbbb]

        // aaaa->bbbb => aaaa.bbbb

        $bits = explode('.', $var);

        $var = array_shift($bits);

        foreach ($bits as $k) {
            $var .= '['.$k.']';
        }

        $bits = explode('->', $var);

        $var = implode('.', $bits);

        $mods = implode('|', $mods);

        if (strlen($mods)) {
            return '{plugin(#smartyModifiers#,'.$var.',#'.$mods.'#):h}';
        }

        return '{'.$var.'}'.$mods;
    }

    /**
     * convert a smarty key="value" string into a key value array
     * cheap and cheerfull - doesnt handle spaces inside the strings...
     *
     * @param   string       the key value part of the tag..
     *
     * @return array key value array
     */
    public function convertAttributesToKeyVal($str) {
        $atts = explode(' ', $str);
        $ret = [];
        foreach ($atts as $bit) {
            $bits = explode('=', $bit);
            // loose stuff!!!
            if (2 != count($bits)) {
                continue;
            }
            $ret[$bits[0]] = ('"' == $bits[1][0]) ? substr($bits[1], 1, -1) : $bits[1];
        }

        return $ret;
    }

    /**
     * convert a smarty config var into a flexy one.
     *
     * @param   string       the inside of the smart tag
     *
     * @return string a flexy version of it
     */
    public function convertConfigVar($str) {
        $mods = explode('|', $str);
        $var = array_shift($mods);
        $var = substr($var, 1, -1); // strip #'s
        $mods = implode('|', $mods);
        if (strlen($mods)) {
            $mods = "<!-- UNSUPPORTED MODIFIERS: $mods -->";
        }

        return '{configVars.'.$var.'}'.$mods;
    }
}