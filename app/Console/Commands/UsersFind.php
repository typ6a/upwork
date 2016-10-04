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
        //pre('1',1);
        $exc = null;
        $page = 1;
        $checkedUsers[]= [
            'id' => 'id ',
            'name' => 'name ',
            'screen_name' => 'screen_name ',
            'location' => 'location ',
            'description' => 'description '
        ];
        while(is_null($exc)){
            try {
                $users = \Twitter::getUsersSearch([
                    'q' => 'honda civic',
                    'page' => $page,
                    'count' => 20
                ]);

                pre($users[9],1);
                //pre($users[9]->screen_name,1);

                //pre(gettype($users[9]->name),1);

                foreach ($users as $user){
               //gettype($user);

                    if (stristr($user->description, 'honda civic'))
                    {
                        $checkedUsers[] = [
                            'id' => $user->id,
                            'name' => $user->name,
                            'screen_name' => $user->screen_name,
                            'location' => $user->location,
                            //'description' => trim(preg_replace('/\s\s+/', ' ', $user->description)),
                            'description' => trim(preg_replace("/\r|\n/", ' ', $user->description))


                        ];
                    };
                }
                sleep (1);

                $page++;

                //pre($checkedUsers[9],1);


//pre($checkedUsers,1);
                    //echo $checkedUsers->name, "\n", $checkedUsers->description, "\n";
                    //echo "-------------", "\n";

                $fp = fopen('d://file.csv', 'w');

                foreach ($checkedUsers as $checkedUser) {

                    fputcsv($fp, $checkedUser);
                }

                fclose($fp);

            } catch (Exception $exc) {}
        }return;
    }
}
