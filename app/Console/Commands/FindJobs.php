<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class FindJobs extends Command
{

    protected $job = null;

    protected static $notacceptedKeywords = array(
        'Wordpress', 'Word press', 'WP', 'Prestashop', 'Drupal', 'Joomla',
        'Magento', 'Python', 'C#', 'C++', 'C+', 'SEO', 'Java', 'CEO',
        'ASP', '.NET', 'dot net', 'ROR', 'Ruby', 'Rails', 'Django', 'iPhone', 'Android', 'jomsocial',
        'Coldfusion', 'iOS', 'Socialengine', 'PhoneGap', 'Shopify', 'Woocommerce', 'Woo commerce', 'webdesigner',
        'Month Bafsis', 'MongoDB', 'Mongo DB', 'Angular.js', 'Angularjs', 'Angular js', 'Assistance',
        'Mailchimp', 'Moodle', 'NodeJS', 'Node JS', 'Node.js', 'Zoho CRM', 'Social Media Platform', 'Fixing',
        'CakePHP', 'Cake PHP', 'Zen Cart', 'ZenCart', 'Graphic Design', 'Open Graph', 'Facebook Graph',
        'Infographic', 'VirtueMart', 'Bigcommerce', 'htaccess', 'mod_rewrite',
        'dolphin', 'boonex', 'adwords', 'Espresso', 'PSD to', 'maverick', 'Xamarin',
        'Scala', 'Elastic', ' TYPO3', 'Concrete5', 'Wowza', 'perl',
        'Volusion', 'Assist With', 'Salesforce', 'landing page', 'SquareSpace'
    );

    protected static $notacceptedLocations = array(
        'India', 'Pakistan', 'Bangladesh', 'Russian Federation',
        'Malaysia', 'Indonesia', 'Philipines', 'Ukraine',
    );

    protected static $preferredKeywords	= array(
        'Crawl', 'Crawler', 'Scrape', 'Scraper','Aggregator', 'Laravel'
    );

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
    public function __construct()
    {
        parent::__construct();
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
    protected function sendEmail($from, $from_name, $recipients, $subject, $message, $attachmentFile = false)
    {
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
        if ((int)$code === 200) {
            return true;
        }
        return false;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    protected function jobs()
    {
        $data = [
            'consumerKey' => '47696c9412b3f5875f56494e812af800', // SETUP YOUR CONSUMER KEY
            'consumerSecret' => 'e8b4f9ddf17edbf1', // SETUP KEY SECRET
            'accessToken' => 'fade5362c6d72e078ce3f7b1dc8e6557', // got access token
            'accessSecret' => '27464d0a88d5a254', // got access secret
            'debug' => false, // enables debug mode
        ];
        $config = new \Upwork\API\Config($data);
        $client = new \Upwork\API\Client($config);
        $jobs = new \Upwork\API\Routers\Jobs\Search($client);
        $response = $jobs->find([
            'q' => ''
        ])->jobs;
        return $response;
    }

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
        $snippet      = $this->job->snippet;
        $title        = $this->job->title;
        $category2    = $this->job->category2;
        $subcategory2 = $this->job->subcategory2;
        foreach (self::$preferredKeywords as $pkw) {
            if (stristr($category2, $pkw) || stristr($subcategory2, $pkw) || stristr($snippet, $pkw) || stristr($title, $pkw)) {
                return true;
            }
        }
        foreach (self::$notacceptedKeywords as $nkw) {
            if (stristr($snippet, $nkw) or stristr($title, $nkw)) {
                return false;
            }
        }
        return true;
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
                if ($this->isLocationAccepted() && $this->isKeywordsAccepted())
                {
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
