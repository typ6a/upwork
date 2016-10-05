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
    protected $category_url = '/be/nl/eshop-productenlijst/koken/koken-toebehoren';

    public function handle()
    {
        $this->parseProductenProducts();
        $this->parseShopProducts();
    }

    protected $categories = [
        'category_code' => 'category_url',
    ];

    protected function parseProductenProducts()
    {

        foreach ($this->categories as $category_code => $category_url){

        }
        $path = storage_path('app/' . $category_code .'/products.json');

        if(!file_exists($path)){
            $html = file_get_contents($this->base_url . $this->category_url);
            $crawler = new Crawler($html);
            $items = $crawler->filter('script');
            foreach ($items as $item){
                if(stristr($item->nodeValue, '//productFilter')){
                    $json = $item->nodeValue;
                    $json=trim(preg_replace("/\r|\n|\t/", '', $json));
                    $json=trim(preg_replace("/'/", "\"", $json));
                    $json=trim(preg_replace("/currentCategory/", '"currentCategory"', $json));
                    $json=trim(preg_replace("/itemData/", '"itemData"', $json));

                    $pos = strpos($json, '{');
                    $json = substr($json, $pos);
                    file_put_contents($path, $json);
                    break;
                }
            }
        }else{
            $json = file_get_contents($path);
            $obj = json_decode($json);
            //pre($obj,1);
            $items = $obj->itemData->response->items;
            foreach($items as $item){
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
            pre(count($items),1);
        }
    }





}
