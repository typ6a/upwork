<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
ini_set('memory_limit', '-1');

class XmlToXls extends Command
{
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
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $xmlPath = storage_path('app/bulk/test.xml');
        $xml = file_get_contents($xmlPath);
        $obj = simplexml_load_string($xml);

        \Excel::create(storage_path('app/bulk/test.xls'), function($excel) {

})->export($obj);
        pre($obj,1);
    }
}
