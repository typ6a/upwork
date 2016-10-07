<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\DomCrawler\Crawler as Crawler;

class SiemensCrawler extends Command
{
    protected $signature = 'siemens:crawl';
    protected $description = 'Command description';

    public function __construct()
    {
        parent::__construct();
    }

    protected $base_url = 'http://www.siemens-home.bsh-group.com';
    protected $catalog_url = '/be/nl/producten';

    public function handle()
    {
        $this->productListCrawler();
        //$this->categories();
        //$this->parseShopProducts();
    }

    protected function categories()
    {
        $categoriesJsonPath = storage_path('app/categories.json');
        $json = file_get_contents($categoriesJsonPath);
        $categoriesObj = json_decode($json);
        $data = [];
        //$categories = jsono_decode(xxx);
        foreach ($categoriesObj as $main_category) {
            foreach ($main_category->categories as $subcategory) {
                $url = $subcategory->url;
                $title = $subcategory->title;
                $data[] = [
                    'url' => $url,
                    'title' => $title,
                ];
            }
        }
        return $data;
    }

    protected function productListCrawler()
    {
        $productListUrls = [];
        //pre(gettype($this->categories()->url));
        $productList = [];
        foreach ($this->categories() as $category) {
            $categoryUrl = $category['url'];
            $categoryTitle = $category['title'];
            $categoryHtmlPath = storage_path('app/' . str_replace('/', '', $categoryTitle) . '.html');
            if (!file_exists($categoryHtmlPath)) {
                $html = file_get_contents($categoryUrl);
                file_put_contents($categoryHtmlPath, $html);
            }
            $html = file_get_contents($categoryHtmlPath);
            $html = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $html);
            $crawler = new Crawler($html);
            $allModelsButtonItems = $crawler->filter('.teaser.type-5 .teaser-inner .figure > a');
            if ($allModelsButtonItems->count()) {
                if (stripos(($allModelsButtonItems->attr('href')), 'http')) {
                    $categoryUrl = trim($allModelsButtonItems->attr('href'));
                } else
                    $categoryUrl = $this->base_url . trim($allModelsButtonItems->attr('href'));
            }
            $productList[] = [
                'url' => $categoryUrl,
                'title' => $categoryTitle,
            ];
        }
        //pre($productList,1);
        return $productList;
    }

    protected function parseProductenProducts()
    {
        foreach ($this->productListCrawler() as $productList) {
            $productListUrl=$productList['url'];
            $productListTitle=$productList['title'];
            $productHtmlPath = storage_path('app/producten' . str_replace('/', '', $categoryTitle) . '.html');

            $crawler = new Crawler($html);
            $items = $crawler->filter('script');

            $productUrl = ;

        }
        $path = storage_path('app/producten' . $category_code . '/products.json');

        if (!file_exists($path)) {
            $html = file_get_contents($this->base_url . $this->category_url);
            $crawler = new Crawler($html);
            $items = $crawler->filter('script');
            foreach ($items as $item) {
                if (stristr($item->nodeValue, '//productFilter')) {
                    $json = $item->nodeValue;
                    $json = trim(preg_replace("/\r|\n|\t/", '', $json));
                    $json = trim(preg_replace("/'/", "\"", $json));
                    $json = trim(preg_replace("/currentCategory/", '"currentCategory"', $json));
                    $json = trim(preg_replace("/itemData/", '"itemData"', $json));

                    $pos = strpos($json, '{');
                    $json = substr($json, $pos);
                    file_put_contents($path, $json);
                    break;
                }
            }
        } else {
            $json = file_get_contents($path);
            $obj = json_decode($json);
            //pre($obj,1);
            $items = $obj->itemData->response->items;
            foreach ($items as $item) {
                $product_path = storage_path('' . md5($item->url));
                $url = $item->link;
                $html = file_get_contents($url);
                $crawler = new Crawler($html);
                $metaTitleObj = $crawler->filter('meta[name="title"]');
                $metaDescriptionObj = $crawler->filter('meta[property="og:description"]');
                $title = $metaTitleObj->attr('content');
                $description = $metaDescriptionObj->attr('content');
                $data = [
                    'raw' => $html,
                    'url' => $url,
                    'title' => $title,
                    'description' => $description
                ];
                $json = json_encode($data);
                file_put_contents($path, $json);
            }
            pre(count($items), 1);
        }
    }


}
