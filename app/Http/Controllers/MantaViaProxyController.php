<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

class MantaViaProxyController extends Controller
{

    public function proxy()
    {
        $page = 1;
        $pages = 100;
        
        $url = 'http://www.manta.com/search?search=real%20estate%20developers&pg=21&pt=40.7528,-73.9725&search_location=New%20York%20NY';
        $res = $this->makeRequest($url);
        pre($res,1);
        
        while($page <= 100){
            
            foreach($items as $item){
                
            }
            $page++;
        }
        
    }
    
    protected function updateProxyList(){
        $this->loadProxyList();
        $this->checkProxyList();
        $this->saveProxyList();
    }
    protected function loadProxyList(){
        $this->loadHideMyAssList();
    }
    protected function loadHideMyAssList(){
        $data = [
            'c'         => ['United States'],
            'allPorts'  => 1,
            'p'         => '',
            'pr'        => [0, 1],
            'a'         => [0, 1, 2, 3, 4],
            'sp'        => [1, 2, 3],
            'ct'        => [1, 2, 3],
            's'         => 0,
            'o'         => 0,
            'pp'        => 3,
            'sortBy'    => 'date'
        ];
    }
    protected function checkProxyList(){}
    protected function saveProxyList($data){
        file_put_contents($this->getProxyListPath(), $data);
    }
    protected function getProxyList(){
        return @file_get_contents($this->getProxyListPath());
    }
    
    protected function  getProxyListPath(){
        return base_path('storage/app/proxies.txt');
    }
    
    protected function makeRequest($url, $data = []){
        $url = 'http://kapver.net/';
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 4);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/53.0.2785.116 Safari/537.36');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
        //curl_setopt($ch, CURLOPT_PROXY, '97.77.104.22');
        //curl_setopt($ch, CURLOPT_PROXYPORT, '80');
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
        $result = curl_exec($ch);
        $http_code = curl_getinfo($ch);
        curl_close($ch);
        return [
            'response' => $result, 
            'http_code' => $http_code
        ];
    }    

}
