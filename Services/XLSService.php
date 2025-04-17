<?php
/**
 * @see https://www.webslesson.info/2019/02/import-excel-file-in-laravel.html
 * @see https://sweetcode.io/import-and-export-excel-files-data-using-in-laravel/
 */

declare(strict_types=1);

namespace Modules\Xot\Services;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Undocumented class.
 */
class XLSService
{
    private static ?self $instance = null;
    protected Collection $data;

    public function __construct()
    {
        // ---
        require_once __DIR__.'/vendor/autoload.php';
    }

    /**
     * Undocumented function.
     */
    public static function getInstance(): self
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Undocumented function.
     */
    public static function make(): self
    {
        return static::getInstance();
    }

    /**
     * Converts column number to letter.
     */
    public function getNameFromNumber(int $num): string
    {
        $numeric = $num % 26;
        $letter = chr(65 + $numeric);
        $num2 = intval($num / 26);
        if ($num2 > 0) {
            return $this->getNameFromNumber($num2 - 1).$letter;
        } else {
            return $letter;
        }
    }

    /**
     * Checks valid urls from XLS import array.
     */
    public function checkValidUrls(array $rows): array
    {
        $col_row = [];
        foreach ($rows as $row_key => $row) {
            $int_col_key = 0;
            foreach ($row as $col_key => $column) {
                if (UrlService::make()->checkValidUrl((string) $column)) {
                    $col_row[] = ['col' => $col_key, 'int_col' => $int_col_key, 'row' => $row_key, 'url' => $column];
                }
                ++$int_col_key;
            }
        }

        return $col_row;
    }

    /**
     * Undocumented function.
     */
    public function fromInputFileName(string $name): self
    {
        $file = request()->file('file');
        if (null === $file) {
            throw new \Exception('[.__LINE__.]['.class_basename(__CLASS__).']');
        }

        return $this->fromRequestFile($file);
    }

    /**
     * Undocumented function.
     *
     * @param array<int,\Illuminate\Http\UploadedFile>|\Illuminate\Http\UploadedFile $file
     *
     * @throws \Illuminate\Validation\ValidationException
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function fromRequestFile($file): self
    {
        if (! \is_object($file)) {
            throw new \Exception('[.__LINE__.]['.class_basename(__CLASS__).']');
        }

        if (! method_exists($file, 'getRealPath')) {
            throw new \Exception('[.__LINE__.]['.class_basename(__CLASS__).']');
        }
        $path = $file->getRealPath();

        if (false === $path) {
            throw new \Exception('[.__LINE__.]['.class_basename(__CLASS__).']');
        }

        return $this->fromFilePath($path);
    }

    /**
     * Undocumented function.
     */
    public function fromFilePath(string $path): self
    {
        // $reader = \Maatwebsite\Excel\Facades\Excel::load($path);
        /*
         * Excel::load() is removed and replaced by Excel::import($yourImport)
         * Excel::create() is removed and replaced by Excel::download/Excel::store($yourExport)
         * Excel::create()->string('xlsx') is removed an replaced by Excel::raw($yourExport, Excel::XLSX)
         */
        // $reader = Excel::import($path);

        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($path);
        $sheet = $spreadsheet->getActiveSheet();
        $row_limit = $sheet->getHighestDataRow();
        $column_limit = $sheet->getHighestDataColumn();
        $row_range = range(1, $row_limit);
        $column_range = range('A', $column_limit);

        $data = collect([]);
        foreach ($row_range as $row) {
            $tmp = [];
            foreach ($column_range as $col) {
                $cell = $col.$row;
                $tmp[$col] = $sheet->getCell($cell)->getValue();
            }
            $data->push(collect($tmp));
        }

        $this->data = $data;

        return $this;
    }

    public function getData(): Collection
    {
        return $this->data;
    }

    /*
    public static function toArray($filename) {
        require_once __DIR__.'/vendor/autoload.php';

        $reader = \Maatwebsite\Excel\Facades\Excel::load($filename);                  //this will load file
        $results = $reader->noHeading()->get()->toArray();

        dddx($results);
    }
    */
}
