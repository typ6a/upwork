<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\DomCrawler\Crawler as Crawler;

class CaRealEstate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'find:agents';

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
    
    public function handle()
    {
        $this->getRealtors();
        }
        
    

    protected function getRealtorsUrls(){
        
            $pagesNum = 13;
            $urls = [];
        for ($pageNum = 1; $pageNum <= $pagesNum; $pageNum++){
            $pageUrl = 'http://www.karea.ca/index.cfm/buyers-sellers/find-a-realtor/?page=' . $pageNum . '&name=&office=';
            $pageHtmlPath = storage_path('app/caRealtor/' . $pageNum . '.html');
               // pre($pageHtmlPath,1);
            if (!file_exists($pageHtmlPath)){
                $pageHtml = file_get_contents($pageUrl);
                file_put_contents($pageHtmlPath, $pageHtml);
            }
            $pageHtml = file_get_contents($pageHtmlPath);
            $pageHtml = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $pageHtml);
            $crawler = new Crawler($pageHtml);
            $realtorUrls = $crawler->filter('#agentList tr td[style="width: 30%;"] > a');
            foreach ($realtorUrls as $realtorUrl) {
                $realtorUrlCrawler = new Crawler($realtorUrl);
                $realtorUrl = $realtorUrlCrawler->attr('href');
                $urls[] = [
                'url' => 'http://www.karea.ca' . $realtorUrl
               ];
                //pre($urls,1);
               // $json = json_encode($urls);
            // file_put_contents($JsonPath, $json, FILE_APPEND);
            }
        }
        // pre($urls,1);
               return $urls;
        //$this->getPagesUrlByBasic();
    }
    
    protected function getRealtors(){
        
        $realtorUrls = $this->getRealtorsUrls();
        $fh = fopen(storage_path('app/caRealtor/kareaAgents.csv'), 'w');
        $head = [
            'Name',
            'Phone',
            'Cell',
            'Fax',
            'Email',
            'Website',
            'Office',
        ];
        fputcsv($fh, $head, ';');
        foreach ($realtorUrls as $realtorUrl) {
            $fields = [
            'name' => '',
            'phone' => '',
            'cell' => '',
            'fax' => '',
            'email' => '',
            'website' => '',
            'office' => '',
        ];
            $realtorUrl = $realtorUrl['url'];
            $realtorPageHtmlPath = storage_path('app/caRealtor/' . md5($realtorUrl) . '.html');
            if (!file_exists($realtorPageHtmlPath)){
                $realtorPageHtml = file_get_contents($realtorUrl);
                file_put_contents($realtorPageHtmlPath, $realtorPageHtml);
            }
            $realtorPageHtml = file_get_contents($realtorPageHtmlPath);
            $realtorPageHtml = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $realtorPageHtml);
            $crawler = new Crawler($realtorPageHtml);
            $fields['name'] = $crawler->filter('.primary .body .pageTitle')->text();
            
            $rows = $crawler->filter('.primary .body .dataDefinition');
            foreach($rows as $row){ // и нам похуй какой там тайтл, имя, фон или мыло
                $row = new Crawler($row);
                $field_name = $row->filter('dt')->text();
                $field_value = $row->filter('dd')->text();
                // дальше проверяем что есть что и есть ли вообще
                // для таких полей которых может и не быть, но чтобы не сбивать последователность в csv файле
                // делаем значения по умолчанию пустыми сразу
                if($field_name === 'Phone'){
                    $fields['phone'] = rtrim(ltrim($field_value));
                }elseif($field_name === 'Email'){
                    $fields['email'] = rtrim(ltrim($field_value));
                }elseif($field_name === 'Cell'){
                    $fields['cell'] = rtrim(ltrim($field_value));
                }elseif($field_name === 'Website'){
                    $fields['website'] = rtrim(ltrim($field_value));
                }elseif($field_name === 'Office'){
                    $fields['office'] = rtrim(ltrim($field_value));
                }elseif($field_name === 'Fax'){
                    $fields['fax'] = rtrim(ltrim($field_value));
                }
            }

            fputcsv($fh, [
                    $fields['name'],
                    $fields['phone'],
                    $fields['cell'],
                    $fields['fax'],
                    $fields['email'],
                    $fields['website'],
                    $fields['office']
                ], ';');
            }
        fclose($fh);
        }

    }


  

    // protected function getPropertiesDetails(){
    //     $rowUrls = $this->getPagesUrls();
    //     //pre($urls,1);
    //     foreach($rowUrls as $urls){
    //     $urls = array_column($urls, 'urls');
    //     $fh = fopen(storage_path('app/section/section8_2.csv'), 'w');
    //     $head = [
    //         'Name',
    //         'Phone',
    //         'Address',
    //         'City',
    //         'State',
    //         'Zip  Code',
    //     ];
    //     fputcsv($fh, $head, ';');
    //     foreach ($urls as $url) {
    //     $propertyHtmlPath = storage_path('app/section/2_' . md5($url) . '.html');
    //     if (!file_exists($propertyHtmlPath)){
    //         $propertyHtml = file_get_contents($url);
    //         file_put_contents($propertyHtmlPath, $propertyHtml);
    //         usleep(100000);
    //         }
    //         //pre($propertyHtmlPath);
    //         $propertyHtml = file_get_contents($propertyHtmlPath);
    //         $propertyHtml = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $propertyHtml);
    //         $crawler = new Crawler($propertyHtml);
    //         //pre($crawler,1);
    //         $propertyAgentName = $crawler->filter('.printcontact h1 span')->text();
    //         pre($propertyAgentName);

    //         $propertyAgentPhone = $crawler->filter('.printcontact b span')->text();
    //         pre($propertyAgentPhone);
            
    //         $propertyAddress = $crawler->filter('.header .address')->text();
    //         pre($propertyAddress);

    //         $propertyAddressRow = $crawler->filter('#ctl00_MainContentPlaceHolder_addr2')->text();
    //         //pre($propertyAddressRow,1);
            
    //         $propertyCityLenght = stripos($propertyAddressRow, ',');
    //         $propertyCity = substr($propertyAddressRow, 0, $propertyCityLenght);
    //         pre($propertyCity);
            
    //         $propertyStateRow = str_replace(', ', '', stristr($propertyAddressRow, ', '));
    //         $propertyStatelenght = stripos($propertyStateRow ,'  ');
    //         $propertyState = substr($propertyStateRow, 0, $propertyStatelenght);
    //         pre($propertyState);

    //         $propertyZipCode = substr(ltrim(stristr($propertyAddressRow, ', '), ', '), -5);
    //         pre($propertyZipCode);

    //         pre('------------------------------------------');
    //         //Address, City, State, Zip Code
    //         fputcsv($fh, [
    //                 $propertyAgentName,
    //                 $propertyAgentPhone,
    //                 $propertyAddress,
    //                 $propertyCity,
    //                 $propertyState,
    //                 $propertyZipCode
    //             ], ';');
    //         }
    //     fclose($fh);

    //     }
    //     //pre($urls);

    // }

    

