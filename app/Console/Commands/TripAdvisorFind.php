<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Symfony\Component\DomCrawler\Crawler as Crawler;

class TripAdvisorFind extends Command
{
    protected $signature = 'advisor:find';

    protected $description = 'Command description';

    public function __construct()
    {
        parent::__construct();
    }
     protected function makeRequest($url, $data = [], $useCookies = false){
        //$url = 'http://kapver.net';
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 4);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/54.0.2840.71 Safari/537.36');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
        //curl_setopt($ch, CURLOPT_PROXY, '97.77.104.22');
        //curl_setopt($ch, CURLOPT_PROXYPORT, '80');
        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            //if ($login) {
                //curl_setopt($ch, CURLOPT_COOKIEJAR, self::getCookiesFile());
            //}
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
        $search_id = '186338';
        $url = 'https://www.tripadvisor.com/Hotels';

        $path = 'd:/' . join('-', [
            $search_id,
            $page,
        ])  .'.html';

        if(!file_exists($path)){
            $html = $this->makeRequest($url, [
                'seen' => '0',
                'sequence' => '1',
                'geo' => $search_id,
                'adults' => '2',
                'rooms' => '1',
                'searchAll' => 'false',
                'requestingServlet' => 'Hotels',
                'refineForm' => 'true',
                'hs' => '',
                'o' => 'a0',
                'pageSize' => '', 
                'rad'  => '0',
                'dateBumped' => 'NONE',
                'displayedSortOrder' => 'popularity',
            ]);

            file_put_contents($path, $html);
        }else{
            $html = file_get_contents($path);
        }

        $html = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $html);
        $html = preg_replace('#<style(.*?)>(.*?)</style>#is', '', $html);

        pre($html,1);

        $crawler = new Crawler($html);

        pre(get_class($crawler),1);

        $hotelsUrl = $crawler->filter('.listing_title > a');
            foreach ($hotelsUrl as $hotelUrl) {
                $hotelUrlCrawler = new $crawler($hotelUrl);
                $urls[] = [
                'urls' => 'https://www.tripadvisor.com' . $hotelUrlCrawler->attr('href')
               ];
        pre($urls,1);
            }
        
    }
}