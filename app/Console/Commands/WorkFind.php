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
        $html = '';
        $jobs = $this->jobs();
        //pre ($jobs,1);
        foreach ($jobs as $job) {
            $filename = $job['id'] . '.json';
            //pre($filename, 1);
            $filepath = 'd:\workspace\upwork\storage\app\upwork\\' . $filename;
            $res = false;
            if (!file_exists($filepath)) {
                $res = file_put_contents($filepath, json_encode($job));
                //pre($res . 'qaz',1);
                    $html .= view('email.jobsfinder.jobNoApi', ['job' => $job]);
            }
        }
            pre($html,1);
        $res = $this->sendEmail(
            \Config::get('mail.from.address'), \Config::get('mail.from.name'), [
            'kapver@gmail.com',
            'znakdmitry@gmail.com'], 'JOBS FROM UPWORK', $html
        );
        exit('YAHOO!!! YAHOO!!! YAHOO!!! YAHOO!!! YAHOO!!! YAHOO!!! YAHOO!!!');
    }


    protected function sendEmail($from, $from_name, $recipients, $subject, $message, $attachmentFile = false)
    {
        $api_key = env('SENDGRID_API_KEY');
        
        $options = array('turn_off_ssl_verification' => $this->isSSLAvailable());

        $email = new \SendGrid\Email();
        $email->setSmtpapiTos(array_values($recipients));
        $email->setFrom($from);
        $email->setFromName($from_name);
        $email->setSubject($subject);
        $email->setHtml($message);
        if (!empty($attachmentFile)) {
            if (is_object($attachmentFile) && ($attachmentFile instanceof FileAttachment)) {
                $email->addAttachment($attachmentFile->getPath(), $attachmentFile->getReadableName());
            } else {
                $email->addAttachment($attachmentFile);
            }
        }
        $sendgrid = new \SendGrid($api_key, $options);
        $response = $sendgrid->send($email);
        $code = $response->getCode();
        if ((int)$code === 200) {
            return true;
        }
        return false;
    }

    protected function isSSLAvailable()
    {
        return defined('CURL_SSLVERSION_SSLv3') ? false : true;
    }

    public function jobs()
    {   $querys =[
            'crawl',
            'scrape',
            'collect',
            'grab',
            'extract',
            ];
            pre($query,1);
        $jobs = [];
        for ($page = 1, $page <= 2, $page++){
        //$baseUrl = 'https://www.upwork.com/o/jobs/browse/?q=crawl&sort=create_time%2Bdesc';
            foreach ($querys as $query {
                $baseUrl = 'https://www.upwork.com/o/jobs/browse/?page=' . $page . '&q=' . $query . '&sort=create_time%2Bdesc';
                //$html = file_get_contents($baseUrl);
                //$html = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $html);
                //file_put_contents('d:/upwork.html', $html);
                //exit;
                $html = file_get_contents('d:/upwork.html');
                $crawler = new Crawler($html);
                $jobBlockItems = $crawler->filter('.row .col-sm-12.jobs-list .job-tile');
        //pre($jobBlockItems, 1);
                    foreach ($jobBlockItems as $key => $jobBlock) {
                        $jobBlockCrawler = new Crawler($jobBlock);
                        
                        $jobTitle = trim($jobBlockCrawler->filter('h2 > a')->text());
                        $jobDescription = preg_replace('/\s+/', ' ', trim($jobBlockCrawler->filter('.description.break  div')->text()));
                        $jobType = preg_replace('/\s+/', ' ', trim($jobBlockCrawler->filter('.text-muted.display-inline-block.m-sm-bottom.m-sm-top')->text()));
                        $jobUrl = $jobBlockCrawler->filter('h2 > a')->attr('href');

                        $jobId = preg_replace('#^.+(_\~[^\/]+).*$#', '$1', $jobUrl);
                        $jobUrl = 'https://www.upwork.com/jobs/' . $jobId . '/';
                        //$jobLocation = $jobBlockCrawler->filter('.glyphicon.glyphicon-md.air-icon-location.m-0 .text-muted.client-location')->text();
                        //pre ($jobUrl, 1);
        }
                            $jobs[$jobUrl] = [
                                'title' => $jobTitle,
                                'description' => $jobDescription,
                                'type' => $jobType,
                                'url' => $jobUrl,
                                'id' => $jobId,
                            ];

                    }
            
        }            //pre($jobs,1);
        return $jobs;
    }
}
