<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\DomCrawler\Crawler as Crawler;

class ParseEquipbaie extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parse:equipbaie';

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
        $this->getDetails();
        }
        

    protected function getUrls(){
            $elsNum = 5000;
            $urls = [];
        for ($elNum = 1; $elNum <= $elsNum; $elNum = $elNum + 64){
            $pageUrl = 'http://www.equipbaie.com/fr/EQB-2016/Exposants/?startRecord='. $elNum . '&rpp=64';
            // pre($pageUrl,1);
            $pageHtmlPath = storage_path('app/equipbaie/' . $elNum . '.html');
               // pre($pageHtmlPath,1);
            if (!file_exists($pageHtmlPath)){
                $pageHtml = file_get_contents($pageUrl);
                file_put_contents($pageHtmlPath, $pageHtml);
            }
            $pageHtml = file_get_contents($pageHtmlPath);
            $pageHtml = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $pageHtml);
            $crawler = new Crawler($pageHtml);
            $eUrls = $crawler->filter('.listItemDetail.exhibitorDetail .name a');
            // pre($eUrls,1);
            foreach ($eUrls as $eUrl) {
                $eUrlCrawler = new Crawler($eUrl);
                $eUrl = $eUrlCrawler->attr('href');
                $urls[] = [
                'url' => 'http://www.equipbaie.com' . $eUrl
               ];
                //pre($urls,1);
               // $json = json_encode($urls);
            // file_put_contents($JsonPath, $json, FILE_APPEND);
            // pre($urls);
            }
        }
        // pre($urls,1);
             // pre($urls,1);
               return $urls;
        //$this->getPagesUrlByBasic();
    }

    protected function getDetails(){
        $rowUrls = $this->getUrls();
        $data =[];
// pre($rowUrls,1);
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
        fputcsv($fh, $head, ';');
        foreach($rowUrls as $rowUrl){
            pre($rowUrl);
                $url = $rowUrl['url'];
                $eHtmlPath = storage_path('app/equipbaie/' . md5($url) . '.html');
                if (!file_exists($eHtmlPath)){
                $eHtml = file_get_contents($url);
                file_put_contents($eHtmlPath, $eHtml);
                }
            usleep(10000);
            $eHtml = file_get_contents($eHtmlPath);
            $eHtml = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $eHtml);
            $crawler = new Crawler($eHtml);
            
            $company = 'NA';
            if(count($crawler->filter('#pageName'))){
            $company = $crawler->filter('#pageName')->text();
            };

            $address = 'NA';
            if(count($crawler->filter('.adr'))){
            $address = rtrim(ltrim(preg_replace("/\r|\n/", ' ', $crawler->filter('.adr')->text())));
            }
            $address= preg_replace("/\s{2,}/"," ",$address);
             // pre($address,1);

            $postalCode = 'NA';
            if(count($crawler->filter('.postal-code'))){
            $postalCode = rtrim(ltrim(preg_replace("/\r|\n/", ' ', $crawler->filter('.postal-code')->text())));
            }
            $postalCode= preg_replace("/\s{2,}/"," ",$postalCode);
              pre($postalCode);

            $locality = 'NA';
            if(count($crawler->filter('.locality'))){
            $locality = rtrim(ltrim(preg_replace("/\r|\n/", ' ', $crawler->filter('.locality')->text())));
            }
            $locality= preg_replace("/\s{2,}/"," ",$locality);
              pre($locality);

            $city='na';
            
            // $street = 'NA';
            // if(count($crawler->filter('.street-address'))){
            // $street = rtrim(ltrim(preg_replace("/\r|\n/", ' ', $crawler->filter('.street-address')->text())));
            // }
            // $street= preg_replace("/\s{2,}/"," ",$street);
            //   pre($street);  

            $country = 'NA';
            if(count($crawler->filter('.country-name'))){
            $country = rtrim(ltrim(preg_replace("/\r|\n/", ' ', $crawler->filter('.country-name')->text())));
            }
            $country = preg_replace("/\s{2,}/"," ",$country);
              // pre($country);

            $phoneNumber1 = 'NA';
            if(count($crawler->filter('.tel'))){
            $phoneNumber1 = rtrim(ltrim(preg_replace("/\r|\n/", ' ', $crawler->filter('.tel')->text())));
            }
            $phoneNumber1= preg_replace("/\s{2,}/"," ",$phoneNumber1);
             // pre($phoneNumber1);

            $phoneNumber2 = 'NA';
            if(count($crawler->filter('.tel span'))){
            $phoneNumber2 = $crawler->filter('.tel')->eq(1)->text();
            $phoneNumber2 = rtrim(ltrim(preg_replace("/\r|\n/", ' ', $phoneNumber2)));
            $phoneNumber2= preg_replace("/\s{2,}/"," ",$phoneNumber2);
             // pre($phoneNumber2,1);
            }
            $phoneNumber2= preg_replace("/\s{2,}/"," ",$phoneNumber2);
            $fax = 'na';
            $website = 'na';
            $email = 'na';
            $sectors = 'NA';
            if(count($crawler->filter('dl dd'))){
            $sectors = rtrim(ltrim(preg_replace("/\r|\n/", ' ', $crawler->filter('dl dd')->text())));
            }
            $sectors = preg_replace("/\s{2,}/"," ",$sectors);
              // pre($sectors);
            $activities = 'NA';  
            if(count($crawler->filter('dl'))){
            $activities = rtrim(ltrim(preg_replace("/\r|\n/", ' ', $crawler->filter('dl')->text())));
            }
            $activities = preg_replace("/\s{2,}/"," ",$activities);
              // pre($activities,1);

            $description = 'NA';
            if(count($crawler->filter('.description.wrapWhiteSpace'))){
            $description = rtrim(ltrim(preg_replace("/\r|\n/", ' ', $crawler->filter('.description.wrapWhiteSpace')->text())));
            };
            $description= preg_replace("/\s{2,}/"," ",$description);

            $products = 'NA';  
            if(count($crawler->filter('#productsList'))){
            $products = rtrim(ltrim(preg_replace("/\r|\n/", ' ', $crawler->filter('#productsList')->text())));
            }
            $products = preg_replace("/\s{2,}/"," ",$products);
            // pre($products,1);
            $brands = 'NA';
            
            

            $contact = 'NA';
            if(count($crawler->filter('.inner-attribute-container.inner-attribute-container-0.tabContent'))){
            $contact = $crawler->filter('.inner-attribute-container.inner-attribute-container-0.tabContent')->text();
            }
            $contact = ltrim(preg_replace("/\r|\n/", ' ', $contact));
            $contact= preg_replace("/\s{2,}/"," ",$contact);
             // pre($contact);
            $standDetails = 'NA';
            if (count($crawler->filter('.standDetails .exhibitor'))){
            $standDetails = $crawler->filter('.standDetails .exhibitor')->text();
            }
            $standDetails= preg_replace("/\s{2,}/"," ",$standDetails);


        
        // 'Company',
        //     'Address',
        //     'Postal Code',
        //     'City',  
        //     'Province', //locality
        //     'Country',
        //     'Phone Number(1)',
        //     'Phone Number(2)',
        //     'Fax',
        //     'Website',
        //     'Email',
        //     'Sectors`
        //     'Activities',
        //     'Company Profile / Description',
        //     'Products',
        //     'Brands',
            
            pre('------------------------------------------');
            //Address, City, State, Zip Code
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
                ], ';');
            }
        fclose($fh);


    }
}
