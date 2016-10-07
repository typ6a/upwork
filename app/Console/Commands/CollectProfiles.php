<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CollectProfiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'manta:collect';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manta.com profiles finder.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    protected function makeRequest($url, $loop = 1, $data = [], $useCookies = false){
        //$url = 'http://kapver.net';
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 4);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/53.0.2785.116 Safari/537.36');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
        curl_setopt($ch, CURLOPT_PROXY, '97.77.104.22');
        curl_setopt($ch, CURLOPT_PROXYPORT, '80');
        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            if ($login) {
                curl_setopt($ch, CURLOPT_COOKIEJAR, self::getCookiesFile());
            }
        }
        if ($useCookies) {
            curl_setopt($ch, CURLOPT_COOKIEFILE, self::getCookiesFile());
        }
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        //self::$response_info = curl_getinfo($ch);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
    
    public function handle()
    {
        $page = 1;
        $pages = 100;
        
        $url = 'http://www.manta.com/search?search=real%20estate%20developers&pg=21&pt=40.7528,-73.9725&search_location=New%20York%20NY';
        $res = $this->makeRequest($url);
        pre($res,1);
        
        while($page <= 100){
            
            foreach($items as $item){
                
            }
            $page++;
        }
        
    }
}
