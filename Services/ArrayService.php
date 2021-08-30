<?php

declare(strict_types=1);

namespace Modules\Xot\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * Class ArrayService.
 */
class ArrayService {
    protected static int $export_processor = 1;

    //ret array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|string|\Symfony\Component\HttpFoundation\BinaryFileResponse

    /**
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     *
     * @return mixed
     */
    public static function toXLS(array $params) {
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
                dddx(['unknown export_processor ['.self::$export_processor.']']);
            break;
        }
    }

    //ret array|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|string|\Symfony\Component\HttpFoundation\BinaryFileResponse

    /**
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     *
     * @return mixed
     */
    public static function toXLS_phpoffice(array $params) {
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

    public static function save(array $params): void {
        extract($params);
        if (! isset($data)) {
            dddx(['err' => 'data is missing']);

            return;
        }
        if (! isset($filename)) {
            dddx(['filename' => 'filename is missing']);

            return;
        }
        $content = var_export($data, true);

        // HHVM fails at __set_state, so just use object cast for now
        $content = str_replace('stdClass::__set_state', '(object)', $content);

        $content = '<'.'?php '.chr(13).'return '.$content.';'.chr(13);
        //$content = str_replace('stdClass::__set_state', '(object)', $content);
        File::makeDirectory(dirname($filename), 0775, true, true);
        File::put($filename, $content);
    }

    /**
     * Undocumented function.
     *
     * @param array|object $arrObjData
     * @param array        $arrSkipIndices
     *
     * @return array
     */
    public static function fromObjects($arrObjData, $arrSkipIndices = []) {
        $arrData = [];

        // if input is object, convert into array
        if (is_object($arrObjData)) {
            $arrObjData = get_object_vars($arrObjData);
        }

        if (is_array($arrObjData)) {
            foreach ($arrObjData as $index => $value) {
                if (is_object($value) || is_array($value)) {
                    $value = ArrayService::fromObjects($value, $arrSkipIndices); // recursive call
                }
                if (in_array($index, $arrSkipIndices)) {
                    continue;
                }
                $arrData[$index] = $value;
            }
        }

        return $arrData;
    }

    /**
     * Undocumented function.
     *
     * @param int $a0
     * @param int $b0
     * @param int $a1
     * @param int $b1
     *
     * @return array|bool
     */
    public static function rangeIntersect($a0, $b0, $a1, $b1) {
        if ($a1 >= $a0 && $a1 <= $b0 && $b0 <= $b1) {
            return [$a1, $b0];
        }
        if ($a0 >= $a1 && $a0 <= $b0 && $b0 <= $b1) {
            return [$a0, $b0];
        }
        if ($a1 >= $a0 && $a1 <= $b1 && $b1 <= $b0) {
            return [$a1, $b1];
        }
        if ($a0 >= $a1 && $a0 <= $b1 && $b1 <= $b0) {
            return [$a0, $b1];
        }

        return false;
    }

    /**
     * Undocumented function.
     *
     * @param array $arr_1
     * @param array $arr_2
     */
    public static function diff_assoc_recursive($arr_1, $arr_2): array {
        $coll_1 = collect($arr_1);
        $coll_2 = collect($arr_2);
        $ris = $coll_1->filter(function ($value, $key) use ($arr_2) {
            try {
                return ! in_array($value, $arr_2);
            } catch (\Exception $e) {
                dddx(['err' => $e->getMessage(), 'value' => $value, 'key' => $key, 'arr_2' => $arr_2]);
            }
        });

        return $ris->all();
    }
}