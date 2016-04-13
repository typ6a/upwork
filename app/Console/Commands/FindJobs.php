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
    protected function sendEmail($from, $from_name, $recipients, $subject, $message, $attachmentFile = false) {
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

        if ((int) $code === 200) {
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
            'q' => 'scraper'
        ]);

        // TODO save data to file or DB
        //json_encode($response);
        //pre($response, 1);
        if ($response->jobs) {

            foreach ($response->jobs as $job) {
                $filename = $job->id . '.json';
                $filepath = 'd:\workspace\upwork\public\data\\' . $filename;
                if (!file_exists($filepath)) {
                    $res = file_put_contents($filepath, json_encode($job));
                }
            }
            
            $res = $this->sendEmail(
                \Config::get('mail.from.address'), 
                \Config::get('mail.from.name'), 
                ['kapver@gmail.com','znakdmitry@gmail.com'], 
                'Some Test Subject',
                view('email.jobsfinder', ['jobs' => $response->jobs])
            );
        }
        exit('YAHOO!!!');
    }

    protected function temp($user) {


        if ($response->jobs) {
            foreach ($response->jobs as $job) {
                $filename = $job->id . '.txt';
                $filepath = 'd:\workspace\upwork\public\data\\' . $filename;
                //pre ($filepath,1);
                $data = [
                    'budget' => 'budget:' . ' ' . $job->budget,
                    'title' => 'title:' . ' ' . $job->title,
                    'url' => 'url:' . ' ' . $job->url,
                    'snippet' => 'snippet:' . ' ' . $job->snippet,
                    'country' => 'country:' . ' ' . $job->client->country,
                    'skills' => 'skills:' . ' ' . implode($job->skills),
                ];
                if (!file_exists($filepath)) {



                    file_put_contents($filepath, $data);


                    $filename = $filepath; //Имя файла для прикрепления
                    $to = "znakdmitry@gmail.com"; //Кому
                    $from = "znakd@ukr.net"; //От кого
                    $subject = "Test"; //Тема
                    $message = "Текстовое сообщение"; //Текст письма
                    $boundary = "---"; //Разделитель
                    /* Заголовки */
                    $headers = "From: $from\nReply-To: $from\n";
                    $headers .= "Content-Type: multipart/mixed; boundary=\"$boundary\"";
                    $body = "--$boundary\n";
                    /* Присоединяем текстовое сообщение */
                    $body .= "Content-type: text/html; charset='utf-8'\n";
                    $body .= "Content-Transfer-Encoding: quoted-printablenn";
                    $body .= "Content-Disposition: attachment; filename==?utf-8?B?" . base64_encode($filename) . "?=\n\n";
                    $body .= $message;
                    $body .= "--$boundary\n";
                    $file = fopen($filename, "r"); //Открываем файл
                    $text = fread($file, filesize($filename)); //Считываем весь файл
                    fclose($file); //Закрываем файл
                    /* Добавляем тип содержимого, кодируем текст файла и добавляем в тело письма */
                    $body .= "Content-Type: application/octet-stream; name==?utf-8?B?" . base64_encode($filename) . "?=\n";
                    $body .= "Content-Transfer-Encoding: base64\n";
                    $body .= "Content-Disposition: attachment; filename==?utf-8?B?" . base64_encode($filename) . "?=\n\n";
                    $body .= chunk_split(base64_encode($text));
                    $body .= "--" . $boundary . "--\n";
                    mail($to, $subject, $body, $headers); //Отправляем письмо
                }

                // pre($job->client->country,1);

                continue;

                $job_code = $job->getCode();
                if ($job_code) {
                    $job_object = new \Upwork\API\Routers\Jobs\Profile($client);
                    $job_object;
                }
            }
        }


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

    protected function performTask($user) {
        $this->info("\n\n" . $user . ' user in progress.');
        sleep(1);
    }

}
