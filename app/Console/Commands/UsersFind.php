<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class UsersFind extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:find';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Find users accounts by description';

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
        pre(class_exists('Thujohn\Twitter\Twitter'),1);
        return;

        $users = \Thujohn\Twitter\Twitter::getUsersSearch([
            'q' => 'honda civic eg3',
            'page' => 1,
            'count' => 20
        ]);


        pre($users,1);
    }
}
