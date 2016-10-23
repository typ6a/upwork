<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\DomCrawler\Crawler as Crawler;

class WorkFind extends Command
{
    protected $signature = 'find:work';

    protected $description = 'find upwork jobs with no api';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->findWork();
    }

    public function findWork()
    {
        $baseUrl = 'https://www.upwork.com/o/jobs/browse/?q=crawl&sort=create_time%2Bdesc';
        $html = file_get_contents($baseUrl);
        $html = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $html);
        $crawler = new Crawler($html);
        $jobBlockItems = $crawler->filter('.row .col-sm-12.jobs-list .job-tile');
//pre($jobBlockItems, 1);
        $jobs = [];
            foreach ($jobBlockItems as $key => $jobBlock) {
                $jobBlockCrawler = new Crawler($jobBlock);
                
                $jobTitle = trim($jobBlockCrawler->filter('h2 > a')->text());
                $jobDescription = trim($jobBlockCrawler->filter('.description.break  div')->text());
                $jobType = preg_replace('/\s+/', ' ', trim($jobBlockCrawler->filter('.text-muted.display-inline-block.m-sm-bottom.m-sm-top')->text()));
                $jobUrl = $jobBlockCrawler->filter('h2 > a')->attr('href');
                $jobId = preg_replace("/o/jobs/job/", '', $jobBlockCrawler->filter('h2 > a')->attr('href'));
//pre($jobUrl,1);/o/jobs/job/_~017b1759de70734a52/
                    $jobs[$jobUrl] = [
                        'title' => $jobTitle,
                        'description' => $jobDescription,
                        'type' => $jobType,
                        'url' => $jobUrl,
                        'id' => $jobId,
                    ];

            }
                    pre($jobs,1);
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

        pre($html, 1);


    }
}
