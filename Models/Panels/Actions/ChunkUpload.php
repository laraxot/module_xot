<?php

namespace Modules\Xot\Models\Panels\Actions;

//-------- models -----------

//-------- services --------

//-------- bases -----------

class ChunkUpload extends XotBasePanelAction {
    public $name = 'chunk_upload'; //name for calling Action
    public $onContainer = true; //onlyContainer
    public $icon = '<i class="fas fa-puzzle-piece"></i>';

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
