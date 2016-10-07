<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class UserLookup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:lookup';

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
        //pre('1',1);
        $exc = null;
        $nextCursor = -1;
        $checkedUsers[] = [
            'id' => 'id ',
            'name' => 'name ',
            'screen_name' => 'screen_name',
            'location' => 'location',
            'description' => 'description',
            'followers_count' => 'friends_count',
            'friends_count' => 'friends_count',

        ];

        while (is_null($exc)) {
            try {
                $response = \Twitter::getUsers([

                    //'screen_name' => 'checkmarx',
                    'screen_name' => '9uMa3Hak',

                ]);

                pre($response,1);
                //pre($users->users[3],1);
                //pre($nextCursor, 1);

                pre($response->users[3], 1);

                foreach ($users as $user) {
                    //gettype($user);

                    $checkedUsers[] = [
                        'id' => $user->id,
                        'name' => $user->name,
                        'screen_name' => $user->screen_name,
                        'location' => $user->location,
                        //'description' => trim(preg_replace('/\s\s+/', ' ', $user->description)),
                        'description' => trim(preg_replace("/\r|\n/", ' ', $user->description))
                    ];
                }
                sleep(1);

                //pre($checkedUsers[9],1);
//pre($checkedUsers,1);
                //echo $checkedUsers->name, "\n", $checkedUsers->description, "\n";
                //echo "-------------", "\n";

                $fp = fopen('d://file.csv', 'w');

                foreach ($checkedUsers as $checkedUser) {

                    fputcsv($fp, $checkedUser);
                }

                fclose($fp);

            } catch (Exception $exc) {
            }
        }
        return;
    }
}