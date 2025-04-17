<?php

declare(strict_types=1);

namespace Modules\Xot\Services;

// use Maatwebsite\Excel\Facades\Excel;
// use PHPExcel;
// use PhpOffice\PhpSpreadsheet\Spreadsheet;
// use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// use Mpdf\Mpdf;
use Exception;
use Illuminate\Support\Facades\Storage;
use Spipu\Html2Pdf\Exception\ExceptionFormatter;
use Spipu\Html2Pdf\Exception\Html2PdfException;
use Spipu\Html2Pdf\Html2Pdf;

/*
ExceptionFormatter
HtmlParsingException
ImageException
LocaleException
LongSentenceException
TableException
*/

/**
 * Class HtmlService.
 */
class HtmlService
{
    /**
     * @return string
     */
    public static function toPdf(array $params)
    {
        // dddx($params);

        include_once __DIR__.'/vendor/autoload.php';
        $pdforientation = 'L'; // default;
        $out = 'show';
        $filename = Storage::disk('local')->path('test.pdf');
        extract($params);
        if (! isset($html)) {
            throw new \Exception('err html is missing');
        }

        if (request('debug', false)) {
            return $html;
        }
        try {
            $html2pdf = new Html2Pdf($pdforientation, 'A4', 'it');
            $html2pdf->setTestTdInOnePage(false);
            $html2pdf->WriteHTML($html);

            switch ($out) {
                case 'content_PDF':
                    return $html2pdf->Output($filename.'.pdf', 'S');

                case 'file':
                    $html2pdf->Output($filename, 'F');

                    return $filename;
            }

            return $html2pdf->Output();
        } catch (Html2PdfException $e) {
            $html2pdf->clean();

            $formatter = new ExceptionFormatter($e);
            dddx($formatter->getHtmlMessage());
            echo $formatter->getHtmlMessage();
        }

        // } catch (HTML2PDF_exception $e) {
        // } catch (Html2PdfException $e) {
        //    echo '<pre>';
        //    \print_r($e);
        //    echo '</pre>';
        // }
        return $filename;
    }

    /*
    public static function toMpdf($html): string {
        require_once __DIR__.'/vendor/autoload.php';

        $mpdf = new Mpdf();
        $mpdf->WriteHTML($html);

        return $mpdf->Output();
    }
    */
}
