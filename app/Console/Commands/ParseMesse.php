<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\DomCrawler\Crawler as Crawler;

class ParseMesse extends Command
{

    protected $signature = 'parse:messe';

    protected $description = 'Command description';

    const STORAGE_FOLDER_NAME = 'messe';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->getUrls();
    }
        

    protected function getStoragePath(){
        return storage_path('app/' . self::STORAGE_FOLDER_NAME);
    }

    protected function getPageUrlPath($baseUrl){
        $this->checkStoragePath();
        return $this->getStoragePath() . md5($baseUrl) . '.html';
    }    

    protected function checkStoragePath(){
        if(!file_exists($this->getStoragePath())){
            mkdir($this->getStoragePath(), 0777);
        }
    }

    protected function getPageHtml($baseUrl){
        $pageHtmlPath = $this->getPageUrlPath($baseUrl);
        if (!file_exists($pageHtmlPath)){
            $pageHtml = file_get_contents($baseUrl);
            file_put_contents($pageHtmlPath, $pageHtml);
        }else{
            $pageHtml = file_get_contents($pageHtmlPath);
        }
        return $pageHtml;
    }

    protected function cleanHtml($pageHtml){
        return preg_replace('#<script(.*?)>(.*?)</script>#is', '', $pageHtml);
    }

    protected function getUrls(){
        $pageUrls = [
            'https://www.messe-und-stadthalle.de/messen/ostseemesse/ausstellerliste-hallenplan.html',
            'https://www.messe-und-stadthalle.de/messen/hochzeitsmesse/ausstellerliste-hallenplan.html',
            'https://www.messe-und-stadthalle.de/messen/flair-am-meer/ausstellerliste-gelaendeplan.html',
            'https://www.messe-und-stadthalle.de/messen/pflegemesse/ausstellerliste-hallenplan.html',


            'https://www.messe-und-stadthalle.de/messen/tiernatur-in-mv/ausstellerliste-hallenplan.html',
            'https://www.messe-und-stadthalle.de/messen/gastro/ausstellerliste-hallenplan.html',
            'https://www.messe-und-stadthalle.de/messen/autotrend/ausstellerliste-hallenplan.html',
        ];
        $urls = [];
                $fh = fopen(storage_path('app/messe.csv'), 'w');
        $head = [
            'Company',
            'Address',
            'Postal Code',
            'City',
            'Province',
            'Country',
            'Phone Number(1)',
            'Phone Number(2)',
            'Fax',
            'Website',
            'Email',
            'Sectors',
            'Activities',
            'Company Profile / Description',
            'Products',
            'Brands',
        ];
        fputcsv($fh, $head, ',');
        foreach ($pageUrls as $pageUrl){
            // pre('--------------------------------------------------');
            // pre($pageUrl);
            $pageHtml = $this->getPageHtml($pageUrl);
            $pageHtml = $this->cleanHtml($pageHtml);
            $crawler = new Crawler($pageHtml);
            $sections = $crawler->filter('.ka-panel');
            if($sections->count()){
                foreach ($sections as $section) {
                    $sectionCrawler = new Crawler($section);
                    $sectionName = $sectionCrawler->filter('div div h2')->text();
                     pre($sectionName);
                     if($sectionCrawler->filter('div[id] > div[id]')->count()){
                        $sectionCompanies = $sectionCrawler->filter('div[id] > div[id]');
                        }
                    if ($sectionCompanies->count()){
                        foreach ($sectionCompanies as $sectionCompany) {
                        $company = 'NA';
                        $sectionCompanyCrawler = new Crawler($sectionCompany);
                        if(count($sectionCompanyCrawler->filter('.csc-default .csc-header h2'))){
                        $company = $sectionCompanyCrawler->filter('.csc-default .csc-header h2')->text();
                             // pre($company);
                        }
                        $address = 'NA';
                        if(count($sectionCompanyCrawler->filter('.bodytext'))){
                        $address = rtrim(ltrim(preg_replace("/\r|\n/", ' ', $sectionCompanyCrawler->filter('.bodytext')->text())));
                        }
                        $address= preg_replace("/\s{2,}/"," ",$address);
                        // $address = preg_replace('remove-this.', '', $address);
                         // pre($address,1);

                        $postalCode = 'NA';
                        //  if(count($sectionCompanyCrawler->filter('.bodytext'))){
                        // $postalCode = rtrim(ltrim(preg_replace("/\r|\n/", ' ', $sectionCompanyCrawler->filter('.bodytext')->text())));
                        // }
                        // $postalCode= preg_replace("/\s{2,}/"," ",$postalCode);
                          // pre($postalCode,1);



                        
                        $locality = 'NA';
                        // if(count($sectionCompanyCrawler->filter('.bodytext'))){
                        // $locality = rtrim(ltrim(preg_replace("/\r|\n/", ' ', $sectionCompanyCrawler->filter('.bodytext')->text())));
                        // } 
                        // $locality= preg_replace("/\s{2,}/"," ",$locality);
                          // pre($locality);

                        $city='na';
                        
                        // $street = 'NA';
                        // if(count($crawler->filter('.street-address'))){
                        // $street = rtrim(ltrim(preg_replace("/\r|\n/", ' ', $crawler->filter('.street-address')->text())));
                        // }
                        // $street= preg_replace("/\s{2,}/"," ",$street);
                        //   pre($street);  

                        $country = 'NA';
                        // if(count($sectionCompanyCrawler->filter('.bodytext'))){
                        // $country = rtrim(ltrim(preg_replace("/\r|\n/", ' ', $sectionCompanyCrawler->filter('.bodytext')->text())));
                        // }
                        // $country = preg_replace("/\s{2,}/"," ",$country);
                          // pre($country);

                        $phoneNumber1 = 'NA';
                        // if(count($sectionCompanyCrawler->filter('.bodytext'))){
                        // $phoneNumber1 = rtrim(ltrim(preg_replace("/\r|\n/", ' ', $sectionCompanyCrawler->filter('.bodytext')->text())));
                        // }
                        // $phoneNumber1= preg_replace("/\s{2,}/"," ",$phoneNumber1);
                         // pre($phoneNumber1);

                        $phoneNumber2 = 'NA';
                        // if(count($sectionCompanyCrawler->filter('.tel span'))){
                        // $phoneNumber2 = $sectionCompanyCrawler->filter('.tel')->eq(1)->text();
                        // $phoneNumber2 = rtrim(ltrim(preg_replace("/\r|\n/", ' ', $phoneNumber2)));
                        // $phoneNumber2= preg_replace("/\s{2,}/"," ",$phoneNumber2);
                        //  // pre($phoneNumber2,1);
                        // }
                        // $phoneNumber2= preg_replace("/\s{2,}/"," ",$phoneNumber2);
                        $fax = 'na';
                        
                        $website = 'NA';
                        if(count($sectionCompanyCrawler->filter('.bodytext a'))){
                        $website = rtrim(ltrim(preg_replace("/\r|\n/", ' ', $sectionCompanyCrawler->filter('.bodytext a')->attr('href'))));
                        } 
                        $website = preg_replace("/\s{2,}/"," ",$website);
                        if (stristr($website, 'javascript:linkTo')){
                            $website = 'NA';
                        }
                         pre($website);

                        $email = 'NA';
                        if(count($sectionCompanyCrawler->filter('.bodytext .mail'))){
                        $email = rtrim(ltrim(preg_replace("/\r|\n/", ' ', $sectionCompanyCrawler->filter('.bodytext .mail')->text())));
                        } 
                        $email = preg_replace("/\s{2,}/"," ",$email);
                        $email = str_replace('remove-this.', '', $email);
                          // pre($email,1);
                    


                        $sectors = 'NA';
                        $sectionCompanyCrawler = new Crawler($sectionCompany);
                        if(count($sectionCompanyCrawler->filter('.csc-header h2'))){
                        $sectors = $sectionCompanyCrawler->filter('.csc-header h2')->text();
                        // if(count($sectionCompanyCrawler->filter('dl dd'))){
                        // $sectors = rtrim(ltrim(preg_replace("/\r|\n/", ' ', $sectionCompanyCrawler->filter('dl dd')->text())));
                        // }
                        // $sectors = preg_replace("/\s{2,}/"," ",$sectors);
                          pre($sectors);

                        $activities = 'NA';

                        // $activities =  substr($pageUrl, 10);
                        $activities = str_replace('https://www.messe-und-stadthalle.de/messen/', '', $pageUrl);
                        $activities = str_replace('/ausstellerliste-hallenplan.html', '', $activities);
                        


                        // if(count($sectionCompanyCrawler->filter('dl'))){
                        // $activities = rtrim(ltrim(preg_replace("/\r|\n/", ' ', $sectionCompanyCrawler->filter('dl')->text())));
                        // }
                        // $activities = preg_replace("/\s{2,}/"," ",$activities);
                          // pre($activities,1);

                        $description = 'NA';
                        // if(count($sectionCompanyCrawler->filter('.description.wrapWhiteSpace'))){
                        // $description = rtrim(ltrim(preg_replace("/\r|\n/", ' ', $sectionCompanyCrawler->filter('.description.wrapWhiteSpace')->text())));
                        // };
                        // $description= preg_replace("/\s{2,}/"," ",$description);

                        $products = 'NA';  
                        // if(count($sectionCompanyCrawler->filter('#productsList'))){
                        // $products = rtrim(ltrim(preg_replace("/\r|\n/", ' ', $sectionCompanyCrawler->filter('#productsList')->text())));
                        // }
                        // $products = preg_replace("/\s{2,}/"," ",$products);
                        // // pre($products,1);
                        $brands = 'NA';
                                
                        

                        $contact = 'NA';
                        // if(count($sectionCompanyCrawler->filter('.bodytext'))){
                        // $contact = $sectionCompanyCrawler->filter('.bodytext')->text();
                        // }
                        // $contact = ltrim(preg_replace("/\r|\n/", ' ', $contact));
                        // $contact= preg_replace("/\s{2,}/"," ",$contact);
                        //  pre($contact);
                        $standDetails = 'NA';
                        // if (count($sectionCompanyCrawler->filter('.standDetails .exhibitor'))){
                        // $standDetails = $sectionCompanyCrawler->filter('.standDetails .exhibitor')->text();
                        // }
                        // $standDetails= preg_replace("/\s{2,}/"," ",$standDetails);
                           // pre($standDetails . 'aaaaaaaaaaaaaaaaaaaaa');


                        fputcsv($fh, [
                            // $data['exhibitorName'],
                            $company,
                            $address,
                            $postalCode,
                            $city,
                            $locality,
                            $country,
                            $phoneNumber1,
                            $phoneNumber2,
                            $fax,
                            $website,
                            $email,
                            $sectors,
                            $activities,
                            $description,
                            $products,
                            $brands,
                            ], ',');

                            };
                        }
                    }     
                }    
            }
        }
    fclose($fh);
    }       
}

    
