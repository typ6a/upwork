<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\DomCrawler\Crawler as Crawler;

class FindCh extends Command
{
    
    protected $signature = 'find:ch';

    protected $description = 'Command description';

    public function __construct()
    {
        parent::__construct();
    }

    protected $base_url = 'https://tel.search.ch/?misc=Hotel&pages=100';

    public function handle()
    {
        $this->getHotelsDedails();
    }

    Protected function getHotels(){
        $HtmlPath = storage_path('app/ch.html');
        $JsonPath = storage_path('app/ch.json');
       
            
               // $html = file_get_contents($this->base_url);
               //file_put_contents($HtmlPath, $html);
            
        
            $html = file_get_contents($HtmlPath);
            $html = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $html);
            $crawler = new Crawler($html);
            $blockItems = $crawler->filter('.sl-col.sl-col-5.sl-col-4-medium .sl-card.tel-card-results .tel-results.tel-entries .tel-commercial');
            //pre(count($blockItems),1);
            $hotels = [];
            foreach ($blockItems as $hotelBlock) {
            //pre($hotelBlock,1);
                $hotelBlockCrawler = new Crawler($hotelBlock);
                $hotelTitle = $hotelBlockCrawler->filter('.tel-resultentry .tel-resultentry .tel-result-main .tel-categories')->text();
                $hotelName = $hotelBlockCrawler->filter('.tel-resultentry .tel-resultentry .tel-result-main h1')->text();
                $hotelUrl = $hotelBlockCrawler->filter('.tel-resultentry .tel-resultentry .tel-result-main h1 > a')->attr('href');
                $hotelAddress = $hotelBlockCrawler->filter('.tel-resultentry .tel-resultentry .tel-result-main .tel-address')->text();
                $hotelPhone = $hotelBlockCrawler->filter('.tel-resultentry .tel-resultentry .tel-result-main .tel-number .sl-nowrap')->text();
               // $hotelPhone = preg_replace('*', '', $hotelPhone);
                    $hotels[] = [
                        'name' => $hotelName,
                        'phone' => $hotelPhone,
                        'url' => 'https://tel.search.ch' . $hotelUrl,
                        'title' => $hotelTitle,
                        'address' => $hotelAddress
                    ];
                
            }
            $json = json_encode($hotels);
            file_put_contents($JsonPath, $json);
    return $json;
    }

    protected function getHotelsDedails(){
        $this->getHotels();
    $JsonPath = storage_path('app/ch.json');
    $fh = fopen(storage_path('app/ch.csv'), 'w');
        $head = [
            'Name',
            'Phone',
            'Email',
            'Website',
            'Address',
        ];
        fputcsv($fh, $head, ';');
        
                
            $json = file_get_contents($JsonPath);
            $hotels = json_decode($json);
            //pre($obj,1);
            foreach ($hotels as $obj) {
                //pre($obj,1);
            fputcsv($fh, [

                trim(preg_replace("/\r|\n/", ' ', $obj->url)),
                trim(preg_replace("/\r|\n/", ' ', $obj->phone)),
                '',
                '',
                trim(preg_replace("/\r|\n/", ' ', $obj->address)),
               
            ], ';');
           // exit;
            }
               
         
        fclose($fh);
    }
}
