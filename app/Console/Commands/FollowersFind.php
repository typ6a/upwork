<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class FollowersFind extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'followers:find';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'find twitter followers';

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
        $request_count = 1;

        set_time_limit(0);
        //pre('1',1);
        $exc = null;

        $checkedUsers[] = [
            'id' => 'id ',
            'name' => 'name ',
            'screen_name' => 'screen_name',
            'location' => 'location',
            'description' => 'description',
            'followers_count' => 'friends_count',
            'friends_count' => 'friends_count',

        ];

        $nextCursor = @file_get_contents(storage_path('app/twitter.cursor'));

        while (is_null($exc)) {
            try {
                while ($nextCursor != 0) {
                    $nextCursor = @file_get_contents(storage_path('app/twitter.cursor'));
                    pre('next cursor: ' . $nextCursor);
                    $response = \Twitter::getFollowers([
                        'screen_name' => 'checkmarx',
                        //'screen_name' => '9uMa3Hak',
                        //'screen_name' => 'civicrtype',
                        'count' => 200,
                        'cursor' => $nextCursor,
                    ]);

                    base_path();
                    //pre($users[cursor],1);
                    //pre($response->users[3],1);
                    //pre($nextCursor, 1);

                    //pre($response, 1);

                    foreach ($response->users as $key => $user) {
                        //

                        if($key === 0) {
                            pre($user->screen_name);
                        }

                        //$user = ;
                        //pre($user,1);
                        $json = json_encode($user);
                        $path = storage_path('app/twitter/') . $user->screen_name . '.json';
                        //pre($path,1);
                        file_put_contents($path, $json);
                    }

                    sleep(1);

                    file_put_contents(storage_path('app/twitter.cursor'), $response->next_cursor);
                    //pre($checkedUsers,1);
                    //echo $checkedUsers->name, "\n", $checkedUsers->description, "\n";
                    //echo "-------------", "\n";

                    if($request_count === 30){
                        break;
                    }
                    $request_count++;
                }


            } catch (Exception $exc) {
            }
        }
        return;
    }
}
