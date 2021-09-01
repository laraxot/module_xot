<?php

declare(strict_types=1);

namespace Modules\Xot\Services;

use Exception;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Cookie\FileCookieJar;
use GuzzleHttp\Cookie\SetCookie;
//use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Stream;
//https://www.sitepoint.com/guzzle-php-http-client/
///*
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use Symfony\Component\DomCrawler\Crawler;

//*/

/**
 * Class ImportService.
 */
class ImportService {
    /**
     * Undocumented variable.
     *
     * @var mixed
     */
    protected static $client = null;

    protected static array $client_options = [];

    /**
     * @var mixed
     */
    protected static $res = null;

    /**
     * Undocumented variable.
     *
     * @var mixed
     */
    protected static $cookieJar = null;

    public static function setClientOptions(array $data = []): void {
        self::$client_options = \array_merge(self::$client_options, $data);
        //ddd(self::$client_options);
    }

    public static function initCookieJar(): void {
        $cookieFile = base_path('../jar.txt');
        self::$cookieJar = new FileCookieJar($cookieFile, true);
    }

    public static function importInit(): void {
        \ini_set('max_execution_time', '3000');

        $route_current = \Route::current();
        $params = [];
        if (null != $route_current) {
            $params = $route_current->parameters();
        }

        //$cookieJar = new CookieJar();

        if (null == self::$cookieJar) {
            self::initCookieJar();
        }

        $headers = [];
        $fields = ['User-Agent', 'Accept', 'Accept-Language', 'Accept-Encoding', 'Connection', 'Cookie', 'Upgrade-Insecure-Requests', 'Cache-Control'];
        foreach ($fields as $field) {
            $headers[$field] = \Request::header($field);
        }
        self::enableRedirect();
        self::$client_options['headers'] = $headers;
        self::$client_options['headers']['Referer'] = 'http://www.google.com';
        self::$client_options['cookies'] = self::$cookieJar;
        self::$client = new GuzzleClient(self::$client_options);
    }

    //end __construct

    //https://hotexamples.com/examples/guzzlehttp.cookie/CookieJar/-/php-cookiejar-class-examples.html
    // google trend example
    //https://hotexamples.com/examples/guzzlehttp.cookie/CookieJar/setCookie/php-cookiejar-setcookie-method-examples.html

    public static function enableCharles(): void {
        $proxy = [
            //'http'  => 'tcp://127.0.0.1:8888', // Use this proxy with "http"
            //'https' => 'tcp://127.0.0.1:8888', // Use this proxy with "https",
            'http' => 'http://127.0.0.1:8888', // Use this proxy with "http"
            'https' => 'https://127.0.0.1:8888', // Use this proxy with "https",

            //'no' => ['.mit.edu', 'foo.com']    // Don't use a proxy with these
        ];
        self::setClientOptions([
            'proxy' => $proxy,
            'verify' => false,
        ]);
        //senza verify false errore = #message: "cURL error 60: SSL certificate problem: self signed certificate in certificate chain (see http://curl.haxx.se/libcurl/c/libcurl-errors.html)"
    }

    public static function enableCookie(array $cookies): void {
        //$cookieJar->setCookie(SetCookie::fromString('SID="AuthKey 23ec5d03-86db-4d80-a378-6059139a7ead"; expires=Thu, 24 Nov 2016 13:52:20 GMT; path=/; domain=.sketchup.com'));
        if (null == self::$cookieJar) {
            self::initCookieJar();
        }

        $url_info = \parse_url(self::$client_options['base_uri']);

        //$domain = $url_info['host'];
        $domain = collect($url_info)->get('host');
        foreach ($cookies as $name => $value) {
            $cookieData = [
                'Domain' => $domain,
                'Name' => $name,
                'Value' => $value,
                'Discard' => true,
            ];
            self::$cookieJar->setCookie(new SetCookie($cookieData));
        }
        self::$client_options['cookies'] = self::$cookieJar;
    }

    public static function enableRedirect(): void {
        $onRedirect = function (RequestInterface $request, ResponseInterface $response, UriInterface $uri) {
            echo '<hr/>Redirecting! '.$request->getUri().' to '.$uri."\n";
        };
        $redirect_params = [
            'max' => 10,        // allow at most 10 redirects.
            'strict' => true,      // use "strict" RFC compliant redirects.
            'referer' => true,      // add a Referer header
            //'protocols'       => ['https'], // only allow https URLs
            'on_redirect' => $onRedirect,
            'track_redirects' => true,
        ];
        self::setClientOptions(['allow_redirects' => $redirect_params]);
        //$client->followRedirects(true);
    }

    public static function disableRedirect(): void {
        self::setClientOptions(['allow_redirects' => false]);
    }

    /**
     * @param mixed $x
     *
     * @return mixed
     */
    public static function getConfig($x) {
        //$cookieJar = $client->getConfig('cookies');
        //$cookieJar->toArray();
        return self::$client->getConfig($x);
    }

    /**
     * @return mixed
     */
    public function getEffectiveUrl(string $method, string $url, array $attrs = []) {
        $attrs['allow_redirects'] = [
            'max' => 10,        // allow at most 10 redirects.
            'strict' => true,      // use "strict" RFC compliant redirects.
            'referer' => true,      // add a Referer header
            //'protocols'       => ['https'], // only allow https URLs
            //'on_redirect'     => $onRedirect,
            'track_redirects' => true,
        ];
        $res = $this->client->request($method, $url, \array_merge($this->client_options, $attrs));

        return $res->getHeaderLine('X-Guzzle-Redirect-History');
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public static function jqueryRequest(string $method, string $url, array $attrs = []) {
        return view('theme::jquery_request');
    }

    //ret \Exception|GuzzleException|string|Crawler

    public static function gRequest(string $method, string $url, array $attrs = [], string $out = 'res'): ?string {
        if (null == self::$client) {
            self::importInit();
        }
        if (! isset(self::$client_options['base_uri'])) {
            $url_info = \parse_url($url);
            self::$client_options['base_uri'] = collect($url_info)->get('scheme').'://'.collect($url_info)->get('host');

            //$url = isset($url_info['path']) ? $url_info['path'] : '';
            $url = collect($url_info)->get('path');
            //if (isset($url_info['query'])) {
            //    $url .= '?'.$url_info['query'];
            //}
            $query = collect($url_info)->get('query');
            if ('' != $query) {
                $url .= '?'.$query;
            }
        }

        $base_uri = self::$client_options['base_uri'];
        if (Str::startsWith($url, $base_uri)) {
            $url = substr($url, strlen($base_uri));
        }
        try {
            $res = self::$client->request($method, $url, \array_merge(self::$client_options, $attrs));
            self::$res = $res;

            self::$client_options['headers']['Referer'] = self::$client_options['base_uri'].$url;
            $html = (string) $res->getBody();
        } catch (GuzzleException $e) {
            $html = null;
        }

        return $html;
        //echo $res->getStatusCode(); // 200
        //echo $res->getHeaderLine('X-Guzzle-Redirect-History');// http://first-redirect, http://second-redirect, etc...
        //echo $res->getHeaderLine('X-Guzzle-Redirect-Status-History');// 301, 302, etc...
        /*
        self::$res = $res;

        self::$client_options['headers']['Referer'] = self::$client_options['base_uri'].$url;
        switch ($out) {
            case 'res': return $res;
            case 'html':
                $html = (string) $res->getBody();

                return $html;
            case 'crawler':
                $html = (string) $res->getBody();
                $crawler = new Crawler((string) $html, self::$client_options['base_uri']);

                return $crawler;
        }

        return $res;
        */
    }

    public static function getStatusCode(): string {
        return self::$res->getStatusCode();
    }

    /**
     * @return mixed
     */
    public static function getRedirectHistory() {
        return self::$res->getHeaderLine('X-Guzzle-Redirect-History'); // http://first-redirect, http://second-redirect, etc...
        //echo $res->getHeaderLine('X-Guzzle-Redirect-Status-History');// 301, 302, etc...
    }

    // ret \Exception|GuzzleException|string|Crawler

    /**
     * @param mixed $form
     * @param mixed $out
     */
    public static function submit($form, array $vars, $out): ?string {
        $vars = \array_merge($form->getValues(), $vars);

        return self::gRequest($form->getMethod(), $form->getUri(), ['form_params' => $vars], $out);
    }

    /**
     * @param string $method
     * @param string $url
     * @param array  $attrs
     *
     * @return string
     */
    public static function getCacheKey($method, $url, $attrs = []) {
        $key = \json_encode(['method' => $method, 'url' => $url, 'attrs' => $attrs]);
        $key .= '_1';

        return $key;
    }

    /**
     * @param string $method
     * @param string $url
     * @param array  $attrs
     *
     * @return mixed
     */
    public static function cacheRequest($method, $url, $attrs = []) {
        $key = self::getCacheKey($method, $url, $attrs = []);
        $value = Cache::store('file')->rememberForever(
            $key,
            function () use ($method, $url, $attrs) {
                $body = self::gRequest($method, $url, $attrs);

                return (string) $body;
            }
        );
        self::$client_options['headers']['referer'] = $url;

        return $value;
    }

    /**
     * @param string $method
     * @param string $url
     * @param array  $attrs
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     *
     * @return string
     */
    public static function cacheRequestFile($method, $url, $attrs = []) { //--- uguale ma al posto di usare il sistema cache usa i file
        if (! isset(self::$client_options['base_uri'])) {
            $url_info = collect(\parse_url($url));
            if (null !== $url_info->get('scheme') && null !== $url_info->get('host')) {
                self::$client_options['base_uri'] = $url_info->get('scheme').'://'.$url_info->get('host');
            } else {
                self::$client_options['base_uri'] = '';
            }
            $url = $url_info->get('path');
            if (null !== $url_info->get('query')) {
                $url .= '?'.$url_info->get('query');
            }
        }

        $file_path = (Str::slug(self::$client_options['base_uri'], '_').'/'.Str::slug($url, '_').'.json');
        //$params=['method'=>$method,'url'=>$url,'attrs'=>$attrs];
        //$key=json_encode(array_values($params));
        //$key=str_slug
        if (\Storage::disk('cache')->exists($file_path)) {
            $content = \Storage::disk('cache')->get($file_path);
            self::$client_options['headers']['referer'] = $url;

            return $content;
        }
        $body = self::gRequest($method, $url, $attrs);
        /*
        if (isset($gres->is_error)) {
            $body = '';
        } else {
            $body = $gres->getBody();
        }
        */

        $res = \Storage::disk('cache')->put($file_path, (string) $body);
        self::$client_options['headers']['referer'] = $url;
        //echo '<br/>da sito ['.$url.']';
        return (string) $body;
    }

    /**
     * @throws \Exception
     */
    public static function getAddressFields(array $params): array {
        \extract($params);
        if (! isset($address)) {
            dddx(['err' => 'address is missing']);

            return [];
        }
        $linked = new \stdClass();
        $location_url = config('services.bing.url_location_api').'?query='.\urlencode($address).'&maxResults=5&key='.config('services.bing.maps_key');
        $location_url = config('services.google.url_location_api').'?address='.\urlencode($address).'&key='.config('services.google.maps_key');
        $loc_json = self::cacheRequest('GET', $location_url);
        $loc_obj = \json_decode($loc_json);
        if (isset($loc_obj->results[0])) {
            $loc_obj = $loc_obj->results[0];
            //ddd($loc_obj->address_components);
            foreach ($loc_obj->address_components as $addr) {
                //echo '<br/>'.$addr->post_types[0].'  '.$addr->long_name.'  '.$addr->short_name;
                $sk = $addr->types[0];
                $linked->$sk = $addr->long_name;
                $sk .= '_short';
                $linked->$sk = $addr->short_name;
            }
            $linked->latitude = $loc_obj->geometry->location->lat;
            $linked->longitude = $loc_obj->geometry->location->lng;
        } else {
            $msg = [
                'id' => isset($id) ? $id : '',
                'address' => $address,
                'obj' => $loc_obj,
            ];
            throw new \Exception('address not valide');
            //ddd($msg);
        }

        return \get_object_vars($linked);
    }

    /*
    public function download($url, $name, $extensions){
       $path = __DIR__.'/download/' . $name . $extensions;
       $file_path = fopen($path,'w');
       $client = new \GuzzleHttp\Client();
       $response = $client->get($url, ['save_to' => $file_path]);
       return ['response_code'=>$response->getStatusCode(), 'name' => $name];
    }
    */
    /*
    $resource = fopen('/path/to/file', 'w');
    $stream = GuzzleHttp\Psr7\stream_for($resource);
    $client->request('GET', '/stream/20', ['save_to' => $stream]);
    */
    /*
    $resource = fopen('/path/to/file', 'w');
    $client->request('GET', '/stream/20', ['sink' => $resource]);
    */
    //new guzzle client setup
    /*
    $client = new GuzzleHttp\Client([ 'base_uri' => 'http://whatever' ]);
    //create a php temp file (returns a resource)
    $putStream = tmpfile();
    //guzzle get() and sink into resource
    $client->get('/stream/20', ['sink' => $putStream]);
    rewind($putStream);
    //store
    Storage::disk('local')->put('somewhere/here.txt', $putStream);
    //release tempfile
    fclose($putStream);
    */

    //https://phpnews.io/feeditem/chunked-transfer-encoding-in-php-with-guzzle

    public static function download(array $params): void {
        //$url
        //$filename
        extract($params);
        if (! isset($filename)) {
            dddx(['err' => 'filename is missing']);

            return;
        }
        if (! isset($url)) {
            dddx(['err' => 'url is missing']);

            return;
        }
        $resource = fopen($filename, 'w');
        if (false === $resource) {
            throw new Exception('can open '.$filename);
        }
        $stream = \GuzzleHttp\Psr7\stream_for($resource);
        self::gRequest(
            'get',
            $url,
            [
                'sink' => $stream,
                'progress' => function ($download_size, $downloaded, $upload_size, $uploaded) {
                    //$this->downloadProgress($download_size, $downloaded, $upload_size, $uploaded);
                    echo '<br>['.$download_size.']['.$downloaded.']['.$upload_size.']['.$uploaded.']';
                },
            ]
        );
        fclose($resource);
        /*
        Call to undefined function GuzzleHttp\Stream\create
        $resource_in=fopen($url, 'r');
        $resource_out=fopen($filename, 'w');
        $original = Stream\create($resource_in);
        $local = Stream\create($resource_out);
        $local->write($original->getContents());
        fclose($resource_out);
        fclose($resource_in);
        */

        /*
        $fp = fopen($path, "wb");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            // curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        //$headerArr = array("HTTP/1.1","Accept-Encoding: gzip;q=1.0,compress;q=0.5,identity;q=0");
        //curl_setopt($ch,CURLOPT_HTTPHEADER,$headerArr);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        //'choice1'=>'Scarica',
        //'choice1001'=>'ZIP'
        $result =curl_exec($ch);
            // 'param0'=>'ced03f',
            // 'choice1'=>"  Scarica  ",
        curl_close($ch);
        fclose($fp);
        */
    }

    /*
    public function upload_to_test($params){
        // Open a stream so that we stream the image download
        $stream = fopen($url, 'r');

        // Create a curl handle to upload to the file server
        $ch = curl_init($fileServer);
        // Send a PUT request
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        // Let curl know that we are sending an entity body
        curl_setopt($ch, CURLOPT_UPLOAD, true);
        // Let curl know that we are using a chunked transfer encoding
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Transfer-Encoding: chunked'));
        // Use a callback to provide curl with data to transmit from the stream
        curl_setopt($ch, CURLOPT_READFUNCTION, function($ch, $fd, $length) use ($stream) {
            return fread($stream, $length) ? '';
        });

        curl_exec($ch);
    }//end upload
    */

    //end function

    /**
     * @param array $params
     *
     * @return mixed|null
     */
    public static function pixabay($params) {
        $lang = app()->getLocale();
        $image_type = 'photo';
        $q = 'necessary';
        \extract($params);
        $pixabay_url = 'https://pixabay.com/api/?key=7945761-cdc8fef41b0600630fdabe778';
        $pixabay_url .= '&lang='.$lang;
        $pixabay_url .= '&image_type='.$image_type;
        $pixabay_url .= '&q='.$q;
        $pixabay_url = \str_replace(' ', '%20', $pixabay_url);
        $json = self::cacheRequest('GET', $pixabay_url);
        $json = \json_decode($json);
        if (! isset($json->hits)) {
            return null;
        }
        $ris = collect($json->hits)->shuffle()->first();

        return $ris;
    }

    /**
     * @param array $params
     *
     * @return mixed|null
     */
    public static function pexels($params) {
        $lang = app()->getLocale();
        $q = 'necessary';
        \extract($params);
        //--- devono mandare via mail api key ..
        //dd($this->client);
        $url = 'https://api.pexels.com/v1/search?query='.$q.'&per_page=15&page=1';
    }

    //-------------------------------------------------------------------------

    /**
     * @param array $params
     *
     * @return mixed|string|void
     */
    public static function trans($params) {
        $i = \rand(0, 20);
        if ($i > 0 && $i < 10) {
            return self::googleTrans($params);
        }

        return self::mymemoryTrans($params);
    }

    /**
     * @param array $params
     *
     * @return mixed|null
     */
    public static function apertiumTrans($params) {
        //https://github.com/24aitor/Laralang/blob/master/src/Builder/ApertiumTrans.php
        //$host = 'api.apertium.org';
        //$urldata = file_get_contents("http://$host/json/translate?q=$urlString&langpair=$this->from|$this->to");
        //$data = json_decode($urldata, true);
    }

    /**
     * @param array $params
     *
     * @return string
     */
    public static function googleTrans($params) {
        $host = 'translate.googleapis.com';
        $q = 'necessary';
        $from = 'en';
        $to = 'it';
        \extract($params);
        $q = \urlencode($q);
        $urldata = \file_get_contents("https://translate.googleapis.com/translate_a/single?client=gtx&sl=$from&tl=$to&dt=t&q=$q");
        $tr = (string) $urldata;
        $tr = \mb_substr($tr, 3, -6);
        $tr = $tr.'["';
        $tr = \explode('],[', $tr);
        $trans = [];
        foreach ($tr as $tran) {
            $transl = \explode('","', $tran);
            \array_push($trans, \str_replace('\"', '"', \ucfirst(\mb_substr($transl[0], 1))));
        }

        return \trim(\implode(' ', $trans));
    }

    /**
     * @param array $params
     *
     * @return mixed|void
     */
    public static function mymemoryTrans($params) {
        $host = 'api.mymemory.translated.net';
        $q = 'necessary';
        $from = 'en';
        $to = 'it';
        \extract($params);
        $q = \urlencode($q);
        $url = 'http://'.$host.'/get?q='.$q.'&langpair='.$from.'|'.$to.'';
        $urldata = \file_get_contents($url);
        if (false === $urldata) {
            throw new Exception('can not get '.$urldata);
        }
        $data = \json_decode($urldata, true);
        if (200 != $data['responseStatus']) {
            /* if (true == self::$debug) {
                 if (403 == $data['responseStatus']) {
                     $details = ($data['responseDetails']);
                 } else {
                     $details = $data['responseDetails'];
                 }
                 $translation = "<font style='color:red;'>Error ".$data->responseStatus.': '.$details.'</font>';
             }*/

            return;
        }

        return $data['responseData']['translatedText'];
    }

    //end mymemoryTrans;

    /**
     * @param array $params
     *
     * @return array
     */
    public static function getForms($params) {
        $html = '';
        $node_tag = '';
        extract($params);
        $crawler = new Crawler((string) $html);
        $forms = $crawler->filter($node_tag)->each(function (Crawler $node) {
            return [
                'action' => $node->attr('action'),
                'method' => $node->attr('method'),
                'fields' => (
                    $node->filter('input')->each(function (Crawler $node1) {
                        return [$node1->attr('name') => $node1->attr('value')];
                    })
                ),
            ];
        });
        foreach ($forms as $k => $v) {
            $forms[$k]['fields'] = collect($v['fields'])->collapse()->all();
        }

        return $forms;
    }

    //ret \Exception|GuzzleException|string|Crawler

    /**
     * @param array $params
     */
    public static function formRequest($params): ?string {
        $form = ['method' => '?', 'action' => '?', 'fields' => '?'];
        extract($params);

        return self::gRequest($form['method'], $form['action'], ['form_params' => $form['fields']]);
    }
}//end class
