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
        $this->parseProductenCategories();


    }

    protected function parseProductenCategories()
    {

        $html = file_get_contents($this->base_url . $this->catalog_url);
        //pre($html);
        $html = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $html);
        $crawler = new Crawler($html);
        $items = $crawler->filter('.teaser-inner .figure.shift > a');
        //pre ($items,1);
        $data =[];
        $items->each(function (Crawler $linkCategoryNode) use (&$data) {
            $url = $this->base_url . ltrim($linkCategoryNode->attr('href'));
            //pre($url,1);
            $title = $linkCategoryNode->filter('img')->attr('alt');
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

    }
}
