<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Symfony\Component\DomCrawler\Crawler as Crawler;

class VendorListScraper extends Command
{
   
    protected $signature = 'scrape:list';

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
        $this->getLists();
        }
        
    

    protected function getListsUrls(){
        
            $pagesNum = 25;
            $urls = [];
        for ($pageNum = 1; $pageNum <= $pagesNum; $pageNum++){
            $pageUrl = 'http://www.randolphstreetmarket.com/index.php?p=' . $pageNum;
            $pageHtmlPath = storage_path('app/randolphstreetmarket/' . $pageNum . '.html');
               // pre($pageHtmlPath,1);
            if (!file_exists($pageHtmlPath)){
                $pageHtml = file_get_contents($pageUrl);
                file_put_contents($pageHtmlPath, $pageHtml);
            }
            $pageHtml = file_get_contents($pageHtmlPath);
            $pageHtml = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $pageHtml);
            $crawler = new Crawler($pageHtml);
            $adUrls = $crawler->filter('td[height="28"] a');
            $adTitles = $crawler->filter('td .newsheadline');
            foreach ($adUrls as $adUrl) {
                $adUrlCrawler = new Crawler($adUrl);
                $adUrl = $adUrlCrawler->attr('href');
                $adUrlText = $adUrlCrawler->text();
            $urls[] = [
            'url' => 'http://randolphstreetmarket.com/' . $adUrl,
            'text' => $adUrlText,
           ];
            }
            
        }
                //pre($urls);
        // pre($urls,1);
               return $urls;
        //$this->getPagesUrlByBasic();
    }
    
    protected function getLists(){
        
        $adUrls = $this->getListsUrls();
        // pre($adUrls,1);
        $fh = fopen(storage_path('app/randolphstreetmarket/randolphstreetmarketVendorList.csv'), 'w');
        $head = [
            'VendorName',
            'VendorSiteUrl',
            'VendorListTitle',
            'VendorListUrl',
        ];
        fputcsv($fh, $head, ';');
        foreach ($adUrls as $adUrl) {
            if ($adUrl['text'] === '>Click to view list!' && $adUrl['url'] != 'http://randolphstreetmarket.com/newsarticle.php?p=1&a=407'){
                $fields = [
                'VendorName' => '',
                'VendorSiteUrl' => '',
                'VendorListTitle' => '',
                'VendorListUrl' => '',
                ];
                $vendorListUrl = $adUrl['url'];
                $VendorListHtmlPath = storage_path('app/randolphstreetmarket/' . md5($vendorListUrl) . '.html');
                    pre($vendorListUrl);
                if (!file_exists($VendorListHtmlPath)){
                    $VendorListHtml = file_get_contents($vendorListUrl);
                    file_put_contents($VendorListHtmlPath, $VendorListHtml);
                }
                $VendorListHtml = file_get_contents($VendorListHtmlPath);
                $VendorListHtml = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $VendorListHtml);
                $crawler = new Crawler($VendorListHtml);
                if (count($crawler->filter('.newscopy b span'))){ //full
                    $rowVendors = $crawler->filter('.newscopy span b');
                    // pre($rowVendors);
                    foreach ($rowVendors as $rowVendor) {
                        $vendorCrawler =new Crawler($rowVendor);
                        $VendorName = ltrim($vendorCrawler->text());
                        $VendorSiteUrl = $vendorCrawler->filter('a')->attr('href');
                        $VendorListTitle = $crawler->filter('td .newsheadline')->text();
                        $VendorListUrl = $vendorListUrl;
                        $fields = [
                            'VendorName' => trim(preg_replace("/\r|\n/", '', $VendorName)),
                            'VendorSiteUrl' => $VendorSiteUrl,
                            'VendorListTitle' => $VendorListTitle,
                            'VendorListUrl' => $VendorListUrl,
                            ];
                    pre($fields);
                    }
                }
                elseif (count($crawler->filter('.newscopy td'))){ //short
                    $rowVendors = $crawler->filter('.newscopy tbody');
                    $rowVendorsHtml = trim(preg_replace("/\r|\n/", '', $rowVendors->html()));
                    $pieces = explode('<br>', $rowVendorsHtml);
                    foreach ($pieces as $piece) {
                        $VendorName = strip_tags($piece);
                     //pre($VendorName,1);
                        $VendorSiteUrl = '';
                        $VendorListTitle = $crawler->filter('td .newsheadline')->text();
                        $VendorListUrl = $vendorListUrl;
                        $fields = [
                            'VendorName' => $VendorName,
                            'VendorSiteUrl' => $VendorSiteUrl,
                            'VendorListTitle' => $VendorListTitle,
                            'VendorListUrl' => $VendorListUrl,
                            ];
                    //pre($fields);
                    }
                }

            fputcsv($fh, [
                    $fields['VendorName'],
                    $fields['VendorSiteUrl'],
                    $fields['VendorListTitle'],
                    $fields['VendorListUrl'],
                ], ';');
            
            }//main if
        }
        fclose($fh);

    }
}