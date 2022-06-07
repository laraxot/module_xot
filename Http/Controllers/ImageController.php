<?php

declare(strict_types=1);

namespace Modules\Xot\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

// use File;
// ---- services --

/**
 * Class ImageController.
 */
class ImageController extends Controller {
    public function index(Request $request): void {
        dddx('index');
    }

    /**
     * @return mixed
     */
    public function show(Request $request) {
        $params = optional(\Route::current())->parameters();
        list($containers, $items) = params2ContainerItem($params);
        $last_item = last($items);

        return $this->$last_item($request);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request) {
        return $this->canvas($request);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function canvas(Request $request) {
        $data = $request->all();
        $path_parts = pathinfo($data['name']);
        $error = false;

        $absolutedir = __DIR__;
        $dir = '/tmp/';
        $serverdir = $absolutedir.$dir;

        $tmp = explode(',', $data['data']);
        $imgdata = base64_decode($tmp[1], true);
        if (false == $imgdata) {
            throw new \Exception('cannot decode imgdata base64');
        }

        // $extension              = strtolower(end(explode('.',$data['name'])));
        $extension = $path_parts['extension'] ?? '';
        $filename = mb_substr($data['name'], 0, -(mb_strlen($extension) + 1)).'.'.mb_substr(sha1(time().''), 0, 6).'.'.$extension;
        /*
        $handle                 = fopen($serverdir.$filename,'w');

        fwrite($handle, $imgdata);
        fclose($handle);
        */
        $path = 'photos/'.\Auth::id().'/'.$filename;
        \Storage::disk('public_html')->put($path, $imgdata);
        $url = \Storage::disk('public_html')->url($path);
        $url = str_replace(url('/'), '', $url); // per risparmiare spazio

        $str = '/storage';
        if (Str::startsWith($url, $str)) {
            $url = substr($url, \strlen($str));
        }

        $response = [
            'status' => 'success',
            'url' => $url.'?'.time(), // added the time to force update when editting multiple times
            'filename' => $filename,
        ];

        if (! empty($data['original'])) {
            $tmp = explode(',', $data['original']);
            $originaldata = base64_decode($tmp[1], true);
            $original = mb_substr($data['name'], 0, -(mb_strlen($extension) + 1)).'.'.mb_substr(sha1(time().''), 0, 6).'.original.'.$extension;

            $handle = fopen($serverdir.$original, 'w');
            if (false !== $handle && false !== $originaldata) {
                fwrite($handle, $originaldata);
                fclose($handle);
            }

            $response['original'] = $original;
        }

        return response()->json($response);
        // print json_encode($response);
    }
}
