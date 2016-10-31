<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\DomCrawler\Crawler as Crawler;

class SectionCrawl2 extends Command
{
   
    protected $signature = 'crawl:section2';

    
    protected $description = 'Command description';


    
    public function __construct()
    {
        parent::__construct();
    }
    protected $base_url1 = 'http://www.gosection8.com/Tenant/tn_Results.aspx?Address=Orange+County%2c+Ca&minRent=0&maxRent=5000&propertyTypeList=House|Townhouse/Villa|&bedrooms=0&bathrooms=1&Accessible=False&pictures=False&pets=False&ac=False&AgeRestricted=False&smoking=False&coveredParking=False&MaxSqFt=5000&MinSqFt=0&keyword=&sortBy=LastUpdate&pg=';
    protected $pagesNum1 = 3;
    
    public function handle()
    {
        $this->getPropertiesDetails();
        }
        
    

    protected function getPagesUrlByPhoto(){
        
            $urls = [];
        for ($pageNum = 0; $pageNum <= $this->pagesNum1; $pageNum++){
            $pageUrl = $this->base_url1 . $pageNum;
            $pageHtmlPath = storage_path('app/section/2_' . $pageNum . '.html');
                //pre($pageHtmlPath,1);
            if (!file_exists($pageHtmlPath)){
                $pageHtml = file_get_contents($pageUrl);
                file_put_contents($pageHtmlPath, $pageHtml);
            }
            $pageHtml = file_get_contents($pageHtmlPath);
            $pageHtml = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $pageHtml);
            $crawler = new Crawler($pageHtml);
            $propertyUrls = $crawler->filter('.photo > a');
            foreach ($propertyUrls as $propertyUrl) {
                $propertyUrlCrawler = new $crawler($propertyUrl);
                $urls[] = [
                'urls' => 'http://www.gosection8.com' . $propertyUrlCrawler->attr('href')
               ];
               // $json = json_encode($urls);
            // file_put_contents($JsonPath, $json, FILE_APPEND);
            }
        }
               return $urls;
               // pre($urls,1);
        //$this->getPagesUrlByBasic();
    }
       

        protected function getPagesUrlByBasic(){
            $urls = [];
        for ($pageNum = 0; $pageNum <= $this->pagesNum1; $pageNum++){
            $pageUrl = $this->base_url1 . $pageNum;
            $pageHtmlPath = storage_path('app/section/2_' . $pageNum . '.html');
                //pre($pageHtmlPath,1);
            if (!file_exists($pageHtmlPath)){
                $pageHtml = file_get_contents($pageUrl);
                file_put_contents($pageHtmlPath, $pageHtml);
            }
            $pageHtml = file_get_contents($pageHtmlPath);
            $pageHtml = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $pageHtml);
            $crawler = new Crawler($pageHtml);
            $propertyUrls = $crawler->filter('.listing.basic .resultslearnmore');
            foreach ($propertyUrls as $propertyUrl) {
                $propertyUrlCrawler = new $crawler($propertyUrl);
                $urls[] = [
                'urls' => 'http://www.gosection8.com' . $propertyUrlCrawler->attr('href')
               ];
            //    $json = json_encode($urls);
            // file_put_contents($JsonPath, $json, FILE_APPEND);
            }
        }
         return $urls;
            
            
            
        }

    protected function getAllPropertyUrls(){
        $urlsByBasic = $this->getPagesUrlByBasic();
        $urlsByPhoto = $this->getPagesUrlByPhoto();
        $allPropertyUrls [] = array_merge_recursive($urlsByBasic, $urlsByPhoto);
        return $allPropertyUrls;
    }

    protected function getPropertiesDetails(){
        $rowUrls = $this->getAllPropertyUrls();
        //pre($urls,1);
        foreach($rowUrls as $urls){
        $urls = array_column($urls, 'urls');
        $fh = fopen(storage_path('app/section/section8_2.csv'), 'w');
        $head = [
            'Name',
            'Phone',
            'Address',
            'City',
            'State',
            'Zip  Code',
        ];
        fputcsv($fh, $head, ';');
        foreach ($urls as $url) {
        $propertyHtmlPath = storage_path('app/section/2_' . md5($url) . '.html');
        if (!file_exists($propertyHtmlPath)){
            $propertyHtml = file_get_contents($url);
            file_put_contents($propertyHtmlPath, $propertyHtml);
            usleep(100000);
            }
            //pre($propertyHtmlPath);
            $propertyHtml = file_get_contents($propertyHtmlPath);
            $propertyHtml = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $propertyHtml);
            $crawler = new Crawler($propertyHtml);
            //pre($crawler,1);
            $propertyAgentName = $crawler->filter('.printcontact h1 span')->text();
            pre($propertyAgentName);

            $propertyAgentPhone = $crawler->filter('.printcontact b span')->text();
            pre($propertyAgentPhone);
            
            $propertyAddress = $crawler->filter('.header .address')->text();
            pre($propertyAddress);

            $propertyAddressRow = $crawler->filter('#ctl00_MainContentPlaceHolder_addr2')->text();
            //pre($propertyAddressRow,1);
            
            $propertyCityLenght = stripos($propertyAddressRow, ',');
            $propertyCity = substr($propertyAddressRow, 0, $propertyCityLenght);
            pre($propertyCity);
            
            $propertyStateRow = str_replace(', ', '', stristr($propertyAddressRow, ', '));
            $propertyStatelenght = stripos($propertyStateRow ,'  ');
            $propertyState = substr($propertyStateRow, 0, $propertyStatelenght);
            pre($propertyState);

            $propertyZipCode = substr(ltrim(stristr($propertyAddressRow, ', '), ', '), -5);
            pre($propertyZipCode);

            pre('------------------------------------------');
            //Address, City, State, Zip Code
            fputcsv($fh, [
                    $propertyAgentName,
                    $propertyAgentPhone,
                    $propertyAddress,
                    $propertyCity,
                    $propertyState,
                    $propertyZipCode
                ], ';');
            }
        fclose($fh);

        }
        //pre($urls);

    }

    
}
