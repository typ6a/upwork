<?php


namespace App\Console;

require_once base_path() . '/../kdg/libs/debug.php';

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        // Commands\Inspire::class,
        Commands\XmlConvert::class,
        Commands\FindJobs::class,
        Commands\SQLToExcel::class,
        Commands\FastTrack::class,
        Commands\UsersFind::class,
        Commands\FollowersFind::class,
        Commands\UserLookup::class,
        Commands\ParseUsers::class,

        Commands\ProductenCrawler::class,
        Commands\ShopCrawler::class,
        Commands\SiemensCrawler::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {

        //1) loop all <script> tags
        //2) match that //productFilter config substring exists
        //3) parse matched <script> for json string
        //4) convert json to object/array - decode
        //5) process data as always

        // $schedule->command('inspire')
        //          ->hourly();
    }
}
