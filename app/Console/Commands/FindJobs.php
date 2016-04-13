<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class FindJobs extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jobs:find';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Find jobs on Upwork by some conditions.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    protected function getSiteDomain() {
        $parts = parse_url(url());
        if (isset($parts['host'])) {
            return $parts['host'];
        }
        return 'noreply@kapver.net';
    }

    protected function isSSLAvailable() {
        return defined('CURL_SSLVERSION_SSLv3') ? false : true;
    }

    /**
     * Sends email via SendGrid service
     * @param string $from
     * @param string $from_name
     * @param string|array $recipients
     * @param string $subject
     * @param string $message
     * @return boolean
     * @author Pavel Klyagin <kapver@gmail.com>
     */
    protected function sendEmail($from, $from_name, $recipients, $subject, $message, $attachmentFile = false){
        $api_key = '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';
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
        
        if((int) $code === 200){
            return true;
        } return false;
    }
    
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        $data = [
            'consumerKey'       => '47696c9412b3f5875f56494e812af800',  // SETUP YOUR CONSUMER KEY
            'consumerSecret'    => 'e8b4f9ddf17edbf1',                  // SETUP KEY SECRET
            'accessToken'       => 'fade5362c6d72e078ce3f7b1dc8e6557',  // got access token
            'accessSecret'      => '27464d0a88d5a254',       // got access secret
            'debug'             => false,                                // enables debug mode
        ];
        $config = new \Upwork\API\Config($data);
        $client = new \Upwork\API\Client($config);
        
        $jobs = new \Upwork\API\Routers\Jobs\Search($client);
        
        $response = $jobs->find([
            'q' => 'scraper'
        ]);
        
        // TODO save data to file or DB
        json_encode($response);
        
        if($response->jobs){
            $res = $this->sendEmail(
                \Config::get('mail.from.address'), 
                \Config::get('mail.from.name'), 
                [
                    'kapver@gmail.com',
                    'znakdmitry@gmail.com'
                ], 
                'Some Test Subject', view('email.jobsfinder', [
                    'jobs' => $response->jobs
                ])
            );
        }
        
        exit('YAHOO!!!');
        
    }
    
    protected function temp($user){
        $users = [
            'Dmitry',
            'Pavel',
            'Veronika'
        ];
        
        $bar = $this->output->createProgressBar(count($users));
        foreach ($users as $user) {
            $this->performTask($user);
            $bar->advance();
        }
        $bar->finish();        
    }
    
    protected function performTask($user){
        $this->info("\n\n" . $user . ' user in progress.');
        sleep(1);
    }

}
