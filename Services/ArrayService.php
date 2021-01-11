<?php

namespace Modules\Xot\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * Class ArrayService
 * @package Modules\Xot\Services
 */
class ArrayService {
    /**
     * @var int
     */
    protected static int $export_processor = 1;

    /**
     * @param array $params
     * @return array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|string|\Symfony\Component\HttpFoundation\BinaryFileResponse
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public static function toXLS($params) {
        require_once __DIR__.'/vendor/autoload.php';
        $data = $params['data'];
        $res = [];
        foreach ($data as $k => $v) {
            foreach ($v as $k0 => $v0) {
                if (! is_array($v0)) {
                    $res[$k][$k0] = $v0;
                }
            }
        }
        $params['data'] = $res;

        switch (self::$export_processor) {
            case 1:return self::toXLS_phpoffice($params); //break;
            //case 2:return self::toXLS_Maatwebsite($params); //break;
            //case 3:return self::toXLS_phpexcel($params); //break;
            default:
                dddx('unknown export_processor ['.self::$export_processor.']');
            break;
        }
    }

    /**
     * @param array $params
     * @return array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|string|\Symfony\Component\HttpFoundation\BinaryFileResponse
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public static function toXLS_phpoffice($params) {
        $filename = 'test';
        \extract($params);
        if (! isset($data)) {
            $data = [];
        }
        if (! is_array($data)) {
            $data = [];
        }
        $spreadsheet = new Spreadsheet();
        //----
        $ltr = 'A1';
        //$res=$spreadsheet->getActiveSheet()->getStyle($ltr)->getAlignment()->setWrapText(true);
        //----
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getStyle($ltr)->getAlignment()->setWrapText(true);
        $firstrow = collect($data)->first();
        if (! is_array($firstrow)) {
            $firstrow = [];
        }
        $sheet->fromArray(\array_keys($firstrow), null, 'A1');
        $sheet->fromArray(
            $data,  	// The data to set
            null,        // Array values with this value will not be set
            'A2'         // Top left coordinate of the worksheet range where
                         //    we want to set these values (default is A1)
        );
        //$sheet->setCellValue('A1', 'Hello World !');
        $writer = new Xlsx($spreadsheet);
        //$pathToFile = 'c:\\download\\xls\\'.$filename.'.xlsx';
        $pathToFile = Storage::disk('local')->path($filename.'.xlsx');
        $writer->save($pathToFile); //$writer->save('php://output'); // per out diretto ?
        if (! isset($out)) {
            return response()->download($pathToFile);
        }
        if (! isset($text)) {
            $text = 'text';
        }
        switch ($out) {
            case 'link': return view('theme::download_icon')->with('file', $pathToFile)->with('ext', 'xls')->with('text', $text);
            case 'download': response()->download($pathToFile);
            // no break
            case 'file':  return $pathToFile;
            case 'link_file':
                $link = view('theme::download_icon')->with('file', $pathToFile)->with('ext', 'xls')->with('text', $text);

                return [$link, $pathToFile];
        }
    }

    /**
     * @param array $params
     */
    public static function save($params) {
        extract($params);
        if (! isset($data)) {
            dddx(['err' => 'data is missing']);

            return;
        }
        if (! isset($filename)) {
            dddx(['filename' => 'filename is missing']);

            return;
        }
        $content = '<'.'?php '.chr(13).'return '.var_export($data, true).';'.chr(13);
        $content = str_replace('stdClass::__set_state', '(object)', $content);
        File::makeDirectory(dirname($filename), 0775, true, true);
        File::put($filename, $content);
    }
}
