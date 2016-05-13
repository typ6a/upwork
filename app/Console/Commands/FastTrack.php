<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class FastTrack extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fasttrack:trial';

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
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        $monthes = [];
        
        $month = 1;
        while($month < 13){
            $start = mktime(0, 0, 0, $month, 1, 2016);
            $monthes[] = [
                'start' => $start,
                'end' => mktime(23, 59, 59, $month, date('t', $start), 2016)
            ];
            $month++;
        }

        $head = ['URL 1', 'URL 2'];
        $csv[] = '"' . join('","', $head) . '"';
        
        foreach($monthes as $month){
            $json = $this->makeRequest('https://fasttrack.grv.org.au/Meeting/CalendarMeetings', $month);
            $items = json_decode($json);
            pre(date('Y-m', $month['start']));
            if(count($items)){
                foreach ($items as $item) {
                    $url1 = 'https://fasttrack.grv.org.au/Meeting/Details/' . $item->id;
                    $url2 = 'https://fasttrack.grv.org.au/RaceField/ViewRaces/' . $item->id;
                    $csv[] = '"' . join('","', [$url1, $url2]) . '"';
                }
            }
            
            sleep(1);
        }
        
        file_put_contents('d:\\workspace\\upwork\\public\trial.csv', join("\n", $csv));
    }

    protected function makeRequest($url, $data = array(), $login = false, $useCookies = false) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 4);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_8_2) AppleWebKit/537.11 (KHTML, like Gecko) Chrome/23.0.1271.6 Safari/537.11');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
        //curl_setopt($ch, CURLOPT_PROXY, '94.231.182.6');
        //curl_setopt($ch, CURLOPT_PROXYPORT, '8080');
        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            if ($login) {
                curl_setopt($ch, CURLOPT_COOKIEJAR, self::getCookiesFile());
            }
        }
        if ($useCookies) {
            curl_setopt($ch, CURLOPT_COOKIEFILE, self::getCookiesFile());
        }
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        //self::$response_info = curl_getinfo($ch);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

}
