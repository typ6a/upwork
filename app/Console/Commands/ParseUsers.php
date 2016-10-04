<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ParseUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parse:users';

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
        //pre(storage_path('app/twitter'),1);
        $d = dir(storage_path('app/twitter'));
        //echo "Handle: " . $d->handle . "\n";
        //echo "Path: " . $d->path . "\n";
        $fh = fopen(storage_path('app/twitter.csv'), 'w');
        $head = [
            'Name',
            'Twitter Handle',
            'Profile link',
            'Location',
            'Bio'
        ];
        fputcsv($fh, $head);
        while (false !== ($entry = $d->read())) {
            if (!in_array($entry, ['.', '..'])) {
                $file_path = storage_path('app/twitter/') . $entry;
                if (file_exists($file_path) && is_file($file_path)) {
                    //pre(file_get_contents($file_path),1);
                    $json = file_get_contents($file_path);
                    $obj = json_decode($json);
                    fputcsv($fh, [

                        trim(preg_replace("/\r|\n/", ' ', $obj->name)),
                        trim(preg_replace("/\r|\n/", ' ', $obj->screen_name)),
                        trim(preg_replace("/\r|\n/", ' ', 'http://twitter.com/' . $obj->screen_name)),
                        trim(preg_replace("/\r|\n/", ' ', $obj->location)),
                        trim(preg_replace("/\r|\n/", ' ', $obj->description)),

                    ]);
                   // exit;
                }
            }
        }
        fclose($fh);
        $d->close();
    }
}
