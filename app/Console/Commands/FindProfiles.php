<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class FindJobs extends Command
{
    protected static $notacceptedKeywords = [];
    protected static $notacceptedLocations = array(
        'India', 'Pakistan', 'Bangladesh', 'Russian Federation',
        'Malaysia', 'Indonesia', 'Philipines', 'Ukraine', 'Vietnam', 'Bahrain', 'Bosnia and Herzegovina',
        'Albania'
    );

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'profiles:find';

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
    public function __construct()
    {
        parent::__construct();

        self::$notacceptedKeywords = file('public/notacceptedkeywords.txt');
        //pre(self::$notacceptedKeywords,1);

    }

    protected function getSiteDomain()
    {
        $parts = parse_url(url());
        if (isset($parts['host'])) {
            return $parts['host'];
        }
        return 'noreply@kapver.net';
    }

    protected function isSSLAvailable()
    {
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


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    protected funct

    protected function isLocationAccepted()
    {
        $jobLocation = $this->job->client->country;
        if (in_array($jobLocation, self::$notacceptedLocations)) {
            return false;
        }
        return true;
    }

    protected function isKeywordsAccepted()
    {
        $snippet = $this->job->snippet;
        $title = $this->job->title;
        foreach (self::$notacceptedKeywords as $kw)
            if (
			//stristr($snippet, $kw) or 
			stristr($title, $kw)) {
                return false;
            }
        return true;
    }

    protected function isClientAccepted()
    {
        $feedback = $this->job->client->feedback;
        //$hires = $this->job->client->past_hires;
        $paymentVerification = $this->job->client->payment_verification_status;
        //if(($feedback > 4) && ($hires > 1) && $paymentVerification)
        {
            return true;
        }
         return false;
    }

    public function handle()
    {
        $html = '';

        // all jobs
        $jobs = $this->jobs();
        //pre ($jobs,1);
        foreach ($jobs as $key => $this->job) {
            $filename = $this->job->id . '.json';
            $filepath = 'd:\workspace\upwork\public\data\\' . $filename;
            if (!file_exists($filepath)) {
                $res = file_put_contents($filepath, json_encode($this->job));
                if ($this->isLocationAccepted() &&
                    //$this->isKeywordsAccepted() &&
                    $this->isClientAccepted()) {
                    $html .= view('email.jobsfinder.job', ['job' => $this->job]);
                }
            }
        }

        $res = $this->sendEmail(
            \Config::get('mail.from.address'), \Config::get('mail.from.name'), [
            'kapver@gmail.com',
            'znakdmitry@gmail.com'], 'JOBS FROM UPWORK', $html
        );
        exit('YAHOO!!! YAHOO!!! YAHOO!!! YAHOO!!! YAHOO!!! YAHOO!!! YAHOO!!!');


    }
}
