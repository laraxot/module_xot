<?php

namespace Modules\Xot\Services;

use Illuminate\Support\Facades\Storage;

/*
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

\PhpOffice\PhpWord\TemplateProcessor($file);
https://stackoverflow.com/questions/41296206/read-and-replace-contents-in-docx-word-file
composer require phpoffice/phpword 
https://github.com/wrklst/docxmustache

https://code-boxx.com/convert-html-to-docx-using-php/

*/

use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\TemplateProcessor;

class DocxService {
	public $docx_input;

	private static $instance = null;

	public static function getInstance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }


	public static function setDocxInput($filename){
		$obj=self::getInstance();
		$obj->docx_input=$filename;
		return $obj;
	}

	public static function setValues($values){
		$obj=self::getInstance();
		$obj->values=$values;
		return $obj;
	}

	public function out($params=[]){
		extract($params);
		require __DIR__.'/vendor/autoload.php'; //carico la mia libreria che uso solo qui..

		//return response()->download($this->docx_input);
		$tpl = new TemplateProcessor($this->docx_input);
		//$tpl->setValue('customer_title', 'test');
		$tpl->setValues($this->values);

		try{
        	$tpl->saveAs(storage_path('tmp.docx'));
    	}catch (\Exception $e){
        	//handle exception
        	ddd($e);
    	}
    	return response()->download(storage_path('tmp.docx'));

	}




}//end class

/*
https://appdividend.com/2018/04/23/how-to-create-word-document-file-in-laravel/
*/

/*
$templateProcessor->cloneRow('rowValue', 10);
$templateProcessor->setValue('rowValue#1', htmlspecialchars('Sun'));
$templateProcessor->setValue('rowValue#2', htmlspecialchars('Mercury'));
$templateProcessor->setValue('rowValue#3', htmlspecialchars('Venus'));
$templateProcessor->setValue('rowValue#4', htmlspecialchars('Earth'));
$templateProcessor->setValue('rowValue#5', htmlspecialchars('Mars'));
$templateProcessor->setValue('rowValue#6', htmlspecialchars('Jupiter'));
$templateProcessor->setValue('rowValue#7', htmlspecialchars('Saturn'));
$templateProcessor->setValue('rowValue#8', htmlspecialchars('Uranus'));
$templateProcessor->setValue('rowValue#9', htmlspecialchars('Neptun'));
$templateProcessor->setValue('rowValue#10', htmlspecialchars('Pluto'));
$templateProcessor->setValue('rowNumber#1', htmlspecialchars('1'));
$templateProcessor->setValue('rowNumber#2', htmlspecialchars('2'));
$templateProcessor->setValue('rowNumber#3', htmlspecialchars('3'));
$templateProcessor->setValue('rowNumber#4', htmlspecialchars('4'));
$templateProcessor->setValue('rowNumber#5', htmlspecialchars('5'));
$templateProcessor->setValue('rowNumber#6', htmlspecialchars('6'));
$templateProcessor->setValue('rowNumber#7', htmlspecialchars('7'));
$templateProcessor->setValue('rowNumber#8', htmlspecialchars('8'));
$templateProcessor->setValue('rowNumber#9', htmlspecialchars('9'));
$templateProcessor->setValue('rowNumber#10', htmlspecialchars('10'));
*/


/*
// Creating the new document...
$zip = new \PhpOffice\PhpWord\Shared\ZipArchive();

//This is the main document in a .docx file.
$fileToModify = 'word/document.xml';

$file = public_path('template.docx');
$temp_file = storage_path('/app/'.date('Ymdhis').'.docx');
copy($template,$temp_file);

if ($zip->open($temp_file) === TRUE) {
    //Read contents into memory
    $oldContents = $zip->getFromName($fileToModify);

    echo $oldContents;

    //Modify contents:
    $newContents = str_replace('{officeaddqress}', 'Yahoo \n World', $oldContents);
    $newContents = str_replace('{name}', 'Santosh Achari', $newContents);

    //Delete the old...
    $zip->deleteName($fileToModify);
    //Write the new...
    $zip->addFromString($fileToModify, $newContents);
    //And write back to the filesystem.
    $return =$zip->close();
    If ($return==TRUE){
        echo "Success!";
    }
} else {
    echo 'failed';
}
*/

/*
$full_path = 'template.docx';
    //Copy the Template file to the Result Directory
    copy($template_file_name, $full_path);

    // add calss Zip Archive
    $zip_val = new ZipArchive;

    //Docx file is nothing but a zip file. Open this Zip File
    if($zip_val->open($full_path) == true)
    {
        // In the Open XML Wordprocessing format content is stored.
        // In the document.xml file located in the word directory.

        $key_file_name = 'word/document.xml';
        $message = $zip_val->getFromName($key_file_name);               

        $timestamp = date('d-M-Y H:i:s');

        // this data Replace the placeholders with actual values
        $message = str_replace("{officeaddress}", "onlinecode org", $message);
        $message = str_replace("{Ename}", "ingo@onlinecode.org", $message); 
        $message = str_replace("{name}", "www.onlinecode.org", $message);   

        //Replace the content with the new content created above.
        $zip_val->addFromString($key_file_name, $message);
        $zip_val->close();
    }
    */