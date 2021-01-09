<?php

namespace Modules\Xot\Models\Panels\Actions;

//-------- models -----------

//-------- services --------

//-------- bases -----------

/**
 * Class ChunkUpload
 * @package Modules\Xot\Models\Panels\Actions
 */
class ChunkUpload extends XotBasePanelAction {
    /**
     * @var string
     */
    public ?string $name = 'chunk_upload'; //name for calling Action
    /**
     * @var bool
     */
    public bool $onContainer = true; //onlyContainer
    /**
     * @var string
     */
    public string $icon = '<i class="fas fa-puzzle-piece"></i>';

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function handle() {
        $filename = $_POST['dir'].\DIRECTORY_SEPARATOR.$_POST['name'];

        if (0 == $_POST['seek']) {
            $fp = \fopen($filename, 'w');
        } else {
            $fp = \fopen($filename, 'a');
            \fseek($fp, $_POST['seek']);
        }
        \fwrite($fp, $_POST['chunk']);
        \fclose($fp);
        $ris = [
            'filename' => $filename,
            'path' => \realpath($filename),
            'seek' => $_POST['seek'],
            //'chunck' =>  $_POST['chunk'],
        ];

        return response()->json($ris);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function postHandle() {
        $filename = $_POST['dir'].\DIRECTORY_SEPARATOR.$_POST['name'];
        if (0 == $_POST['seek']) {
            $fp = \fopen($filename, 'w');
        } else {
            $fp = \fopen($filename, 'a');
            \fseek($fp, $_POST['seek']);
        }
        \fwrite($fp, $_POST['chunk']);
        \fclose($fp);
        $ris = [
            'filename' => $filename,
            'path' => \realpath($filename),
            'seek' => $_POST['seek'],
            //'chunck' =>  $_POST['chunk'],
        ];

        return response()->json($ris);
    }
}
