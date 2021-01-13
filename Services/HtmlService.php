<?php

namespace Modules\Xot\Services;

//use Maatwebsite\Excel\Facades\Excel;
//use PHPExcel;
//use PhpOffice\PhpSpreadsheet\Spreadsheet;
//use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Spipu\Html2Pdf\Exception\Html2PdfException;
/*
ExceptionFormatter
HtmlParsingException
ImageException
LocaleException
LongSentenceException
TableException
*/
use Spipu\Html2Pdf\Html2Pdf;

/**
 * Class HtmlService
 * @package Modules\Xot\Services
 */
class HtmlService {
    /**
     * @param array $params
     * @return string|void
     */
    public static function toPdf($params) {
        require_once __DIR__.'/vendor/autoload.php';
        $pdforientation = 'L'; //default;
        $out = 'show';
        $filename = 'test';
        extract($params);
        if (! isset($html)) {
            dddx(['err' => 'html is missing']);

            return;
        }

        if (request('debug', false)) {
            return $html;
        }
        try {
            $html2pdf = new Html2Pdf($pdforientation, 'A4', 'it');
            $html2pdf->setTestTdInOnePage(false);
            $html2pdf->WriteHTML($html);

            if ('content_PDF' == $out) {
                return $html2pdf->Output($filename.'.pdf', 'S');
            }

            return $html2pdf->Output();
            //} catch (HTML2PDF_exception $e) {
        } catch (Html2PdfException $e) {
            echo '<pre>';
            \print_r($e);
            echo '</pre>';
        }
    }
}
