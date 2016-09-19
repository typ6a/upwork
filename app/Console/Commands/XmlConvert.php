<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class XmlConvert extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'xml:convert';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Convert XML to CSV/JSON.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    public function handle() {
        $xml = file_get_contents('public/projects/xmlconvert/PLCWeeklyXMLDownload.xml');
        $items = new \SimpleXMLElement($xml);
        
        $head = [
            'SWTitle + VersionNumber',
            '',
        ];
        
        foreach($items->SWTitleRecord as $record){
            $version = isset($record->Versions) && isset($record->Versions->Version) && isset($record->Versions->Version->versionNumber) ? $record->Versions->Version->versionNumber : '"no version number"';
            
            $title = str_replace(['edition', 'Edition'], ['', ''], $record->SWTitle);
            echo $title . "\n";
            if(preg_match_all('!\040\d+(?:\.\d+)?\040?!', $title, $matches)){
                pre($matches,1);
            }
            echo $title . "\n--------\n";
            continue;
            $data[] = [
                $title . ' ' . $version
            ];
            
            
            pre($data);
        }
        
        exit('WORKS!');
    }

}
