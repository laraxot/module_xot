<?php

/**
 * https://pear.php.net/package/HTML_Template_Flexy/docs/latest/__filesource/fsource_HTML_Template_Flexy__HTML_Template_Flexy-1.3.13HTMLTemplateFlexyCompilerSmartyConvertor.php.html.
 */

// https://github.com/OXID-eSales/smarty-to-twig-converter

/* non funzionanti
{if $page.element|@count>0} rilascia
@if($page->element|@count>0)


{if $smarty.foreach.frp.iteration!=$page.element|@count} rilascia
@if($smarty->foreach->frp->iteration!=$page->element|@count)

{if $smarty.foreach.obj_form.iteration > 6}
<!--   UNSUPPORTED TAG: [if $smarty.foreach.obj_form.iteration > 6] FOUND -->

{$element.title|escape}
{{ $element->title|escape }}

{$option.label|truncate:77:"..."}
{{ $option->label|truncate:77:"->->->" }}

{if isset($input.type)}
<!--   UNSUPPORTED TAG: [if isset($input.type)] FOUND -->

{if isset($s.subchilds)}
<!--   UNSUPPORTED TAG: [if isset($s.subchilds)] FOUND -->

{if $input.type == "text"}
<!--   UNSUPPORTED TAG: [if $input.type == "text"] FOUND -->

{if $input.required == 1}
<!--   UNSUPPORTED TAG: [if $input.required == 1] FOUND -->

pattern="[0-9]{literal}{4}{/literal}"
pattern="[0-9]@verbatim<!--   UNSUPPORTED TAG: [4] FOUND -->@endverbatim"



*/

declare(strict_types=1);

namespace Modules\Xot\Services;

use Illuminate\Support\Facades\File;

class SmartyService
{
    /**
     * stack for conditional and closers.
     */
    public array $stack = ['if' => 0, 'foreach' => 0];

    /**
     * The core work of parsing a smarty template and converting it into flexy.
     * the contents of the smarty template.
     */
    public function convert(string $file): string
    {
        if (! File::exists($file)) {
            dddx(['message' => $file.' not exists']);
        }
        $content = file_get_contents($file);
        if (false === $content) {
            throw new \Exception('cannot get content of file ['.$file.']');
        }
        /* solo per test
        $content = '\t{if $isMobile}
        \t{include file="../templates/interno/header_mobile.tpl"}
        \t{/if}';
        //*/

        /* solo per test 2
        // https://stackoverflow.com/questions/17097499/convert-smarty-to-normal-php

        $content = '{if $error != ""}
        <div class="short_error">{$error}</div>
        {/if}';
        //*/

        $leftq = preg_quote('{', '!');
        $rightq = preg_quote('}', '!');

        preg_match_all('!'.$leftq."\s*(.*?)\s*".$rightq.'!s', $content, $matches);
        $tags = $matches[1];
        // find all the tags/text...
        $text = preg_split('!'.$leftq.'.*?'.$rightq.'!s', $content);
        if (false === $text) {
            throw new \Exception('text is false');
        }
        // $max_text = count($text);
        $max_tags = \count($tags);
        $compiled_tags = [];
        for ($i = 0; $i < $max_tags; ++$i) {
            $compiled_tags[] = $this->compileTag($tags[$i]);
        }
        // error handling for closing tags.
        $data = '';
        for ($i = 0; $i < $max_tags; ++$i) {
            $data .= $text[$i].$compiled_tags[$i];
        }
        $data .= $text[$i];
        /*
        //dddx(['original' => $content, 'converted' => $data]);
        echo '<table border="1"><tr><td><pre>' . htmlspecialchars($content) . '</pre></td>
            <td><pre>' . htmlspecialchars($data) . '</pre></td></tr>
            </table>';
        */
        return $data;
    }

    public function compileTag(string $str): string
    {
        // skip comments
        if (('*' === $str[0]) && ('*' === substr($str, -1, 1))) {
            return '';
        }
        switch ($str[0]) {
            case '$':
                return '{{ '.$this->convertVarToObject($str).' }}'; // .'['.__LINE__.']'; // its a var
            case '#':
                return $this->convertConfigVar($str); // its a config var
            case '%':
                return "<!-- what is this? $str -->"; // wtf does this do
        }

        // this is where it gets messy
        // this is very slow - but what the hell
        //   - its only done once
        //   - its alot more readable than a long regext.
        //   - it doesnt infringe on copyright...

        switch (true) {
            case preg_match('/^config_load\s/', $str):
                // convert to $t->TemplateConfigLoad()

                $args = $this->convertAttributesToKeyVal(substr($str, (int) strpos($str, ' ')));

                return '{plugin(#smartyConfigLoad#,#'.$args['file'].'#,#'.$args['section'].'#)}';

            case preg_match('/^include\s/', $str):
                // convert to $t->TemplateConfigLoad()

                $args = $this->convertAttributesToKeyVal(substr($str, (int) strpos($str, ' ')));

                // return '{plugin(#smartyInclude#,#'.$args['file'].'#)}';
                $blade_file = str_replace('.tpl', '', $args['file']);
                $blade_file = str_replace("'", '', $blade_file);
                if (! \is_string($blade_file)) {
                    throw new \Exception('blade_file not a string');
                }

                return '@include(\''.$blade_file.'\')';

            case 'ldelim' === $str:
                return '{';

            case 'rdelim' === $str:
                return '}';

            case preg_match('/^if \$(\S+)$/', $str, $matches):
            case preg_match('/^if \$(\S+)\seq\s""$/', $str, $matches):
                // simple if variable..

                // convert to : {if:sssssss}

                $this->stack['if']++;

                $var = $this->convertVarToObject('$'.$matches[1]);

                return '@if('.$var.')';
                // return '@if('.substr($var, 1, -1).')'; //'['.__LINE__.']';

            case preg_match('/^if #(\S+)#$/', $str, $matches):
            case preg_match('/^if #(\S+)#\sne\s""$/', $str, $matches):
                // simple if variable..

                // convert to : {if:sssssss}

                $this->stack['if']++;

                $var = $this->convertConfigVar('#'.$matches[1].'#');

                // return '{if:'.substr($var, 1);
                return '@if('.substr($var, 1).')'; // .'['.__LINE__.']';//4 debug

                // negative matches

            case preg_match('/^if\s!\s\$(\S+)$/', $str, $matches):
            case preg_match('/^if \$(\S+)\seq\s""$/', $str, $matches):
                // simple if variable..

                // convert to : {if:sssssss}

                $this->stack['if']++;

                $var = $this->convertVar('$'.$matches[1]);

                // return '{if:!'.substr($var, 1);
                return '@if(!'.substr($var, 1).')';

            case preg_match('/^elseif (\S+)(==|!=)(\S+)$/', $str, $matches):
                ++$this->stack['if'];
                $var1 = $this->convertVarToObject('$'.$matches[1]);
                $op = $matches[2];
                $var2 = $this->convertVarToObject('$'.$matches[3]);

                return '@elseif('.substr($var1, 1, -1).' '.$op.' '.substr($var2, 1, -1).')';

            case 'else' === $str:
                if (! $this->stack['if']) {
                    break;
                }

                // return '{else:}';
                return '@else';

            case '/if' === $str:
                if (! $this->stack['if']) {
                    break;
                }

                --$this->stack['if'];

                // return '{end:}';
                return '@endif';
            case preg_match('/^if \((\S+)\)$/', $str, $matches):
                $this->stack['if']++;

                $var = $this->convertVar('$'.$matches[1]);

                return '@if('.substr($var, 1, -1).')';

            case preg_match('/^if \((\S+) (\S+) (\S+)\)$/', $str, $matches):
                ++$this->stack['if'];
                $var1 = $this->convertVar('$'.$matches[1]);
                $op = $matches[2];

                $var2 = $this->convertVar('$'.$matches[3]);

                return '@if('.substr($var1, 1, -1).' '.$op.' '.substr($var2, 1, -1).')';

            case preg_match('/^foreach from=(\S+) item=(\S+)$/', $str, $matches):
                $this->stack['foreach']++;

                // dddx($matches);

                $var = $this->convertVarToObject($matches[1]);

                $var2 = '$'.$this->convertVarToObject($matches[2]);

                return '@foreach('.$var.' as '.$var2.')';

            case preg_match('/^foreach from=(\S+) item=(\S+) name=(\S+)$/', $str, $matches):
                $this->stack['foreach']++;

                // dddx($matches);

                $from = $this->convertVarToObject($matches[1]);

                $item = '$'.$this->convertVarToObject($matches[2]);

                $name = '$'.$this->convertVarToObject($matches[3]);

                return '@foreach('.$from.' as '.$item.')';

            case preg_match('/^foreach name=(\S+) from=(\S+) item=(\S+)$/', $str, $matches):
                $this->stack['foreach']++;

                // dddx($matches);

                $from = $this->convertVarToObject($matches[2]);

                $item = '$'.$this->convertVarToObject($matches[3]);

                $name = '$'.$this->convertVarToObject($matches[1]);

                return '@foreach('.$from.' as '.$item.')';

            case preg_match('/^foreach from=(\S+) item=(\S+) key=(\S+) name=(\S+)$/', $str, $matches):
                $this->stack['foreach']++;

                // dddx($matches);

                $from = $this->convertVarToObject($matches[1]);

                $item = '$'.$this->convertVarToObject($matches[2]);

                $key = '$'.$this->convertVarToObject($matches[3]);

                $name = '$'.$this->convertVarToObject($matches[4]);

                return '@foreach('.$from.' as '.$key.'=>'.$item.')';

            case '/foreach' === $str:
                if (! $this->stack['foreach']) {
                    break;
                }

                --$this->stack['foreach'];

                // return '{end:}';
                return '@endforeach';

                // case preg_match($this->getOpeningTagPattern('if'), $str, $matches):
                //    dddx($matches);
            case 'php' === $str:
                return '@php ';
            case '/php' === $str:
                return '@endphp';
            case 'literal' === $str:
                return '@verbatim';
            case '/literal' === $str:
                return '@endverbatim';
            case 'strip' === $str:
                return '';
            case '/strip' === $str:
                return '';
        }

        return "<!--   UNSUPPORTED TAG: [$str] FOUND -->";
    }

    /**
     * Get opening tag patterns like:
     *   [{tagName other stuff}]
     *   [{foreach $myColors as $color}].
     *
     * Matching this pattern will give these results:
     *   $matches[0] contains a string with full matched tag i.e.'[{tagName foo="bar" something="somevalue"}]'
     *   $matches[1] should contain a string with all other configuration coming with a tag i.e.
     *   'foo = "bar" something="somevalue"'
     */
    public function getOpeningTagPattern(string $tagName): string
    {
        return sprintf("#\[\{\s*%s\b\s*((?:(?!\[\{|\}\]).(?<!\[\{)(?<!\}\]))+)?\}\]#is", preg_quote($tagName, '#'));
    }

    /**
     * convert a smarty var into a flexy one.
     * str      the inside of the smart tag
     * return a string a flexy version of it.
     */
    public function convertVar(string $str): string
    {
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
            // $var .= '['.$k.']';
            $var .= '->'.$k;
        }
        /*
        $bits = explode('->', $var);

        $var = implode('.', $bits);
        */
        $mods = implode('|', $mods);

        if (\strlen($mods)) {
            return '{plugin(#smartyModifiers#,'.$var.',#'.$mods.'#):h}';
        }

        return '{'.$var.'}'.$mods; // .'['.__LINE__.']';
    }

    /**
     * convert a smarty var into a $index->value type.
     * str       the inside of the smart tag
     * return string a flexy version of it.
     */
    public function convertVarToObject(string $str): string
    {
        $var = $str; // strip $

        $bits = explode('.', $var);

        $var = array_shift($bits);

        foreach ($bits as $k) {
            $var .= '->'.$k;
        }

        return $var;
    }

    /**
     * convert a smarty key="value" string into a key value array
     * cheap and cheerfull - doesnt handle spaces inside the strings...
     * str     the key value part of the tag..
     * return array key value array.
     */
    public function convertAttributesToKeyVal(string $str): array
    {
        $atts = explode(' ', $str);
        $ret = [];
        foreach ($atts as $bit) {
            $bits = explode('=', $bit);
            // loose stuff!!!
            if (2 !== \count($bits)) {
                continue;
            }
            $ret[$bits[0]] = ('"' === $bits[1][0]) ? substr($bits[1], 1, -1) : $bits[1];
        }

        return $ret;
    }

    /**
     * convert a smarty config var into a flexy one.
     * str       the inside of the smart tag
     * return string a flexy version of it.
     */
    public function convertConfigVar(string $str): string
    {
        $mods = explode('|', $str);
        $var = array_shift($mods);
        $var = substr($var, 1, -1); // strip #'s
        $mods = implode('|', $mods);
        if (\strlen($mods)) {
            $mods = "<!-- UNSUPPORTED MODIFIERS: $mods -->";
        }

        return '{configVars.'.$var.'}'.$mods;
    }
}
