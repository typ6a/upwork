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
        $categoriesHtlmPath = storage_path('app/categories.html');
        $categoriesJsonPath = storage_path('app/categories.json');
        
        if(!file_exists($categoriesJsonPath)){
            if(!file_exists($categoriesHtlmPath)){
                $html = file_get_contents($this->base_url . $this->catalog_url);
                file_put_contents($categoriesHtlmPath, $html);
            }
        
            $html = file_get_contents($categoriesHtlmPath);
            $html = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $html);

            $crawler = new Crawler($html);
            $blockItems = $crawler->filter('.footer-inner .site-content .info-panel');

            $categories = [];
            foreach ($blockItems as $key => $mainCategoryBlock) {
                $mainCategoryBlockCrawler = new Crawler($mainCategoryBlock);
                $mainCategoryEl = $mainCategoryBlockCrawler->filter('h3 > a');
                $mainCategoryTitle = $mainCategoryEl->text();
                $mainCategoryUrl = $this->base_url . $mainCategoryEl->attr('href');
                if(!in_array($mainCategoryTitle, ['Klantenservice'])){
                    $categories[$mainCategoryTitle] = [
                        'url' => $mainCategoryUrl,
                        'title' => $mainCategoryTitle,
                        'categories' => $this->parseSubCategories($mainCategoryBlockCrawler)
                    ];
                }
            }
            $json = json_encode($categories);
            file_put_contents($categoriesJsonPath, $json);
        }

        $json = file_get_contents($categoriesJsonPath);
    }

    protected function parseSubCategories(Crawler $crawler){
        $categories = $crawler->filter('ul>li>a');
        $data = [];
        foreach($categories as $category){
            $categoryTitle = $category->nodeValue;
            $categoryUrl = $this->base_url . $category->getAttribute('href');
            $data[$categoryTitle] = [
                'title' => $categoryTitle,
                'url' => $categoryUrl,
            ];
        } return $data;
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

