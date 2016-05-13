<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SQLToExcel extends Command {

  

    protected $signature = 'SQLToExcel';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'MySQL to MS excel export.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    
    public function handle() {
        

        
       // pre (sys_get_temp_dir(),1);
        $output[] = ['header col #1', 'header col #2', 'header col #3'];
        $output[] = ['row #1 col#1', 'row #1 col#2', 'row #1 col#3'];
        $output[] = ['row #2 col#1', 'row #2 col#2', 'row #2 col#3'];
        
        
        fputcsv($fh, $output);
        
            
        $fileName = 'test_php_excel_lib' . '.xlsx';
        
        $uploadedPath = tempnam(sys_get_temp_dir(), 'sql_to_excel_tmp');
        $bytes = file_put_contents($uploadedPath, $csvFormatted);
        if($bytes){
            $fileType = PHPExcel_IOFactory::identify($uploadedPath);
            $reader = PHPExcel_IOFactory::createReader($fileType);
            $reader->setReadDataOnly(true);
            $excel = $reader->load($uploadedPath);
            
            if(file_exists($uploadedPath) && is_file($uploadedPath)){
                unlink($uploadedPath);
            }
            
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment;filename="'.$fileName.'"');
            header('Cache-Control: max-age=0');
            
            $writer = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
            $writer->save('d:\\' . $fileName);
        }
    }
}
