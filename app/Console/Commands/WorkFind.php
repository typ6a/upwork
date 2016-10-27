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
        // $json = file_get_contents('c:\temp\list.json');
        // //$d = dir(storage_path('app/icef'));
        // $fh = fopen(storage_path('app/icef.csv'), 'w');

        // $head = [
        //     'Country',
        //     'Company name',
        //     'Address',
        //     'Phone',
        //     'Contact Email',
        //     'Website',
        //     'Principal Agent',
        //     'URL at icef.com'
        // ];
        
        // pre($head);

        // fputcsv($fh, $head, ';');
       
        // $obj = json_decode($json);

        // $agencies = $obj->aaData;
        
        // $baseUrl = 'https://connect.pierapps.com';
        // $total = count($agencies);

        // foreach($agencies as $key => $agency){
        //     //pre($agency,1);
        //     $addressParts = explode(' ', $agency[2]);
        //     //pre($addressParts,1);

        //     $url = $baseUrl . trim(preg_replace("/\r|\n/", ' ', preg_replace('/^.+href="(.+)".+$/', '$1', $agency[1])));
        //     $info = $this->parseAdditionalData($url);

        //     $data = [
        //         'country' => trim(preg_replace("/\r|\n/", ' ', end($addressParts))), // country
        //         'name' => trim(preg_replace("/\r|\n/", ' ', preg_replace('/^<a.+>(.+)<\/a>$/', '$1', $agency[1]))), // company name
        //         'address' => trim(preg_replace("/\r|\n|\040{2,}/", ' ', $agency[2])), // address,
        //         'phone' => $info['phone'],
        //         'email' => $info['email'],
        //         'website' => $info['website'],
        //         'agent' => $info['agent'],

        //         'url' => $url, // company URL at icef.com
        //     ];
        
        //     fputcsv($fh, $data, ';');

        //     echo "\r" . 'Progress: ' . number_format((100 * (($key + 1) / $total)), 2) . '%';

        //     sleep(1);

        // }

        // fclose($fh);

        
        $html = '';
        $jobs = $this->jobs();
        pre ($jobs,1);
        foreach ($jobs as $job) {
            $filename = $job['id'] . '.json';
            //pre($filename, 1);
            $filepath = 'd:\workspace\upwork\storage\app\upwork\\' . $filename;
            $res = false;
            if (!file_exists($filepath)) {
                $res = file_put_contents($filepath, json_encode($job));
                //pre($res,1);
                    $html .= view('email.jobsfinder.job', ['job' => $job]);
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

    protected function parseAdditionalData($url){
        $path = storage_path('app/icaf/' . md5($url));
        if(!file_exists($path)){
            $html = file_get_contents($url);
            $html = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $html);
            file_put_contents($path, $html);
        }else{
            $html = file_get_contents($path);
        }

        $crawler = new Crawler($html);

        $divs = $crawler->filter('.panel-body.text-info .col-md-4 .card');
        $agent = trim($crawler->filter('.pull-left > h4')->text());
        //pre($agent);
        foreach ($divs as $div) {
            $div = new Crawler($div);
            if($div->filter('strong')->count() > 0){
                $strong = $div->filter('strong')->text();
                if($strong === 'Ph:') {
                    $phone = trim(str_replace('Ph:', '', $div->text()));
                }elseif($strong === 'Contact Email:'){
                    $mail = trim(str_replace('Contact Email:', '', $div->text()));
                }elseif($strong === 'Website:'){
                    $site = trim(str_replace('Website:', '', $div->text()));
                }
            }
            
        }
        
        $data = [
            'phone' => isset($phone) ? $phone : '',
            'email' => isset($mail) ? $mail : '',
            'website' => isset($site) ? $site : '',
            'agent' => isset($agent) ? $agent : '',
        ];

        return $data;
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
    {
        $baseUrl = 'https://www.upwork.com/o/jobs/browse/?q=crawl&sort=create_time%2Bdesc';
        $baseUrl = 'https://www.upwork.com/o/jobs/browse/?page=2&q=scrape&sort=create_time%2Bdesc';
        //$html = file_get_contents($baseUrl);
        //$html = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $html);
        //file_put_contents('d:/upwork.html', $html);
        //exit;
        $html = file_get_contents('d:/upwork.html');
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

                $jobId = preg_replace('#^.+(_\~[^\/]+).*$#', '$1', $jobUrl);
                $jobUrl = 'https://www.upwork.com/jobs/' . $jobId . '/';
                //$jobLocation = $jobBlockCrawler->filter('.glyphicon.glyphicon-md.air-icon-location.m-0 .text-muted.client-location')->text();
                //pre ($jobUrl, 1);

                    $jobs[$jobUrl] = [
                        'title' => $jobTitle,
                        'description' => $jobDescription,
                        'type' => $jobType,
                        'url' => $jobUrl,
                        'id' => $jobId,
                    ];

            }
            
                    pre($jobs,1);
        return $jobs;
    }
}
