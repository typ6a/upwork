<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\DomCrawler\Crawler as Crawler;

class ProductenCrawler extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'producten:crawl';

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

    protected $base_url = 'http://www.siemens-home.bsh-group.com';
    protected $catalog_url = '/be/nl/producten';

    public function handle()

    {
        //$this->parseProductenMainCategories();
        $this->parseProductenCategories();


    }

    protected function parseProductenCategories()
    {

        $html = file_get_contents($this->base_url . $this->catalog_url);
        //pre($html);
        $html = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $html);
        $crawler = new Crawler($html);
        $blockItems = $crawler->filter('.footer-inner .site-content .info-panel');
        $path = storage_path('app/categories.json');
        $data=[];
        foreach ($blockItems as $key => $blockItem) {
            $crawler = new Crawler($blockItem);
            $mainCategoryItems = $crawler->filter('h3 > a');
            $mainCategoryUrl = $this->base_url . $mainCategoryItems->attr('href');
        //pre($mainCategoryUrl, 1);
            $mainCategoryTitle = $mainCategoryItems->text();
            $data = [
                'mainCategoryUrl' => $mainCategoryUrl,
                'mainCategoryTitle' => $mainCategoryTitle,

            ];
            pre ($data,1);
            $json = json_encode($data);
            //pre($json,1);
            file_put_contents($path, $json);


            file_put_contents($path, $data[$key]);
            foreach ($mainCategoryItems as $k => $mainCategoryItem) {
                $categoryItems = $crawler->filter('ul li > a');
                $categoryUrl = $this->base_url . $categoryItems->attr('href');
                $categoryTitle = $categoryItems->text();
                $data[$k] = [
                    'CategoryUrl' => $categoryUrl,
                    'CategoryTitle' => $categoryTitle,

                ];
                file_put_contents($path, $data);
                $json = json_encode($data);
                file_put_contents($path, $json);
            }

        }

    }
}
/*$items = $mainItem->filter('ul li > a');

}

$items = $mainItems->filter('.footer-inner .site-content .info-panel ul li > a');
//pre ($items,1);


$mainItems->each(function (Crawler $linkMainCategoryNode) use (&$mainData) {
$mainCategoryUrl = $this->base_url . ltrim($linkMainCategoryNode->filter('h3 > a')->attr('href'));
$mainCategoryTitle = $linkMainCategoryNode->filter('h3 > a')->text();

$data = [];
$items = $mainItems->filter('.footer-inner .site-content .info-panel ul li > a');
$items->each(function (Crawler $linkCategoryNode) use (&$data) {

}

$categoryUrl = $this->base_url . ltrim($linkCategoryNode->filter('ul li > a')->attr('href'));
//pre($title,1);
$mainData[] = [
    'mainCategoryTitle' => $mainCategoryTitle,
    'mainCategoryUrl' => $mainCategoryUrl,
    'category' => $Data,
];
});
$json = json_encode($data);
$path = storage_path('app/productenMainCategories.json');
//pre($path,1);
file_put_contents($path, $json);
//pre($categoryUrls, 1);

}

protected function parseProductenCategories()
{

$html = file_get_contents($this->base_url . $this->catalog_url);
//pre($html);
$html = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $html);
$crawler = new Crawler($html);
$items = $crawler->filter('.footer-inner .site-content .info-panel ul li > a');
//pre ($items,1);
$data = [];
$items->each(function (Crawler $linkCategoryNode) use (&$data) {
$url = $this->base_url . ltrim($linkCategoryNode->attr('href'));
$title = $linkCategoryNode->text();

//pre($title,1);
$data[] = [
    'title' => $title,
    'url' => $url,
];
});
$json = json_encode($data);
$path = storage_path('app/productenCategories.json');
//pre($path,1);
file_put_contents($path, $json);
//pre($categoryUrls, 1);

}*/

