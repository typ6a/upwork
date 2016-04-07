<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Session;

class JobController extends Controller {
    
    public function find(){
        $data = [
            'consumerKey'       => '187335280ef0ccdacb1b146edf110d98',  // SETUP YOUR CONSUMER KEY
            'consumerSecret'    => 'ca9f909c8dc55fd7',                  // SETUP KEY SECRET
            'accessToken'       => Session::get('access_token'),        // got access token
            'accessSecret'      => Session::get('access_secret'),       // got access secret
            'debug'             => true,                                // enables debug mode
        ];
        $config = new \Upwork\API\Config($data);
        $client = new \Upwork\API\Client($config);
        $accessTokenInfo = $client->auth();
        pre($accessTokenInfo,1);
        
        $jobs = new \Upwork\API\Routers\Jobs\Search($client);
        $jobs->find([
            'q' => 'something to request',
            'skills' => 'php,laravel'
        ]);
        
        foreach($jobs as $job){
            $job_code = $job->getCode();
            if($job_code){
                $job_object = new \Upwork\API\Routers\Jobs\Profile($client);
                $job_object;    
            }
        }
        
        // /api/search/params
    }
    
}
