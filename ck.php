<?php

/*
        * Script Created By : Will Pratama - facebook.com/yaelahhwil
        * Thanks To : Charles Giovanni
*/

date_default_timezone_set("Asia/Jakarta");
class bigtoken extends modules
{
        private $domains = "@sharklasers.com";

        public function registerAccount($referralCode)
        {
                $email = $this->randNama()['nama'].$this->randStr("huruf_angka","3").$this->domains;
                $headers = array();
                $headers[] = "Accept: application/json";
                $headers[] = "User-Agent: Redmi ".rand(00000,999999)." Plus_".rand(00,99).".1.2_1.0.".rand(00,99);
                $headers[] = "Host: api.bigtoken.com";
                $register = $this->curl("https://api.bigtoken.com/signup", 'email='.str_replace("@", "%40", $email).'&password='.$this->randStr("kapital", "7").'23%23&referral_id='.$referralCode.'&monetize=1', false, false, $headers);
                if(strpos($register, '"message": "Too Many Attempts."'))
                {
                        print "\r\"message\": \"Too Many Attempts.\"";
                        $this->register($referralCode);
                }elseif(strpos($register, '"user_id":')){
                        print PHP_EOL."Success Register... || ".$email.PHP_EOL;
                        $tempMail = new tempMail(str_replace($this->domains, "", $email));
                        $linkActivasi = trim($tempMail->temporraryMail());
                        if(preg_match('/Sabar\.\./', $linkActivasi) or strpos($linkActivasi, 'Sabar..'))
                        {
                                //print $linkActivasi;
                        }else{  
                                $verifs = $this->curl(trim($linkActivasi), null, false, true, array(), 'GET');
                                $fetchLocation = $this->fetchLocation($verifs)['https://my_bigtoken_com/verify?code'];
                                $verifikasiEmail = $this->verifikasiEmail($linkActivasi, $fetchLocation, $email);
                                print $verifikasiEmail;
                        }       
                }else{
                        print PHP_EOL.$register.PHP_EOL;
                }
        }

        protected function verifikasiEmail($linkActivasi, $fetchLocation, $mail)
        {
                for($a=1;$a<=20;$a++)
                {
                        $headVer = array();
                        $headVer[] = "accept-language: id-ID,id;q=0.9,en-US;q=0.8,en;q=0.7";
                        $headVer[] = "upgrade-insecure-requests: 1";
                        $headVer[] = "user-agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.86 Safari/537.36";
                        $verif = $this->curl("https://my.bigtoken.com/verify?code=".trim($fetchLocation)."&type=signup&email=".trim($mail), null, false, false, $headVer, 'GET');
                        if(strpos($verif, 'We\'re sorry but My Bigtoken doesn\'t') or preg_match('/We\'re sorry but My Bigtoken doesn\'t/i', $verif))
                        {       
                                $hoa = array();
                                $hoa[] = "Content-Type: application/json";
                                $hoa[] = "Origin: https://my.bigtoken.com";
                                $hoa[] = "User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.86 Safari/537.36";
                                $hoa[] = "X-Requested-With: XMLHttpRequest";
                                $hoa[] = "X-Srax-Big-Api-Version: 2";
                                $hoa[] = "host: api.bigtoken.com";
                                $ver2 = $this->curl("https://api.bigtoken.com/signup/email-verification", '{"email":"'.trim($mail).'","verification_code":"'.trim($fetchLocation).'"}', false, false, $hoa);
                                if(strpos($ver2, 'msg":"Reward successfully made"'))
                                {
                                        if($a == 1)
                                        {
                                                print str_replace("\r", "", PHP_EOL."Success Verifikasi!".PHP_EOL);
                                                return false;
                                        }else{
                                                print str_replace("\r", "", PHP_EOL.PHP_EOL."Success Verifikasi!".PHP_EOL);
                                                return false;
                                        }
                                }else{
                                        print "\rSabar.. ".$a." Sec...";
                                        if($a == 20)
                                        {
                                                print PHP_EOL."Silahkan Verifikasi Manual : ".$linkActivasi.PHP_EOL;
                                                $this->fwrites("verifManual.txt", $linkActivasi.PHP_EOL);
                                                return false;
                                        }
                                }
                        }else{
                                print "\r\rFailed Verifikasi!".PHP_EOL;
                        }
                }
        }               
}

class tempMail extends modules
{

        protected $mailName;
        protected $domain = "guerrillamail.com";

        public function __construct($mailName)
        {
                $this->mailName = $mailName;
        }

        protected function getIpToken()
        {
                $apiToken = $this->getStr($this->curl("https://www.guerrillamail.com", null, false, false, array(), 'GET'), 'api_token : \'', '\'', 1, 0);
                return $apiToken;
        }

        private function createMail($mailName, $PHPSESSID)
        {
                $domain = $this->domain;
                $headers = array();
                $headers[] = "Authorization: ApiToken ".$this->getIpToken(); 
                $headers[] = "Content-Type: application/x-www-form-urlencoded; charset=UTF-8"; 
                $headers[] = "Cookie: PHPSESSID=".$PHPSESSID."; __cfduid=d7ce4997ba4fe3d0b677434d8e17e73fe1554249274; _ga=GA1.2.1939465402.1554249275; _gid=GA1.2.263874565.1554249275"; 
                $headers[] = "Host: www.guerrillamail.com"; 
                $headers[] = "Origin: https://www.guerrillamail.com"; 
                $headers[] = "Referer: https://www.guerrillamail.com/inbox"; 
                $headers[] = "User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.86 Safari/537.36"; 
                $headers[] = "X-Requested-With: XMLHttpRequest"; 
                $createMail = $this->curl("https://www.guerrillamail.com/ajax.php?f=set_email_user", "email_user=".$mailName."&lang=en&site=".$domain."&in=+Set+cancel", false, true, $headers);
                return $createMail;
        }

        private function pageInbox($mailName, $PHPSESSID)
        {
                for($a=1;$a<=50;$a++)
                {
                        $headers = array();
                        $headers[] = "Authorization: ApiToken ".$this->getIpToken();
                        $headers[] = "Content-Type: application/x-www-form-urlencoded; charset=UTF-8"; 
                        $headers[] = "Cookie: PHPSESSID=".$PHPSESSID."; __cfduid=d7ce4997ba4fe3d0b677434d8e17e73fe1554249274; _ga=GA1.2.1939465402.1554249275; _gid=GA1.2.263874565.1554249275"; 
                        $headers[] = "Host: www.guerrillamail.com"; 
                        $headers[] = "Origin: https://www.guerrillamail.com"; 
                        $headers[] = "Referer: https://www.guerrillamail.com/inbox"; 
                        $headers[] = "User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.86 Safari/537.36"; 
                        $headers[] = "X-Requested-With: XMLHttpRequest"; 
                        $pageInbox = $this->curl("https://www.guerrillamail.com/inbox", null, false, false, $headers, 'GET');
                        $this->curl("https://www.guerrillamail.com/ajax.php?f=check_email&seq=1&site=".$this->domain."&in=".$mailName."&_=".time(), null, false, false, $headers, 'GET');
                        if(preg_match('/Confirmation needed: Your BIGtoken email address/', $pageInbox) or strpos($pageInbox, 'Confirmation needed: Your BIGtoken email address') or preg_match('/Big Token/i', $pageInbox) or strpos($pageInbox, 'BIGToken') or preg_match('/BIGToken/', $pageInbox) or preg_match('/BIG Token/', $pageInbox))
                        {
                                @$linkMail = $this->getStr($pageInbox, '<a rel="nofollow" href="', '"', 1, 0);
                                if(!empty($linkMail))
                                {
                                        return "https://www.guerrillamail.com".$linkMail;
                                }else{
                                        print "\r\rLink Mail Not found";
                                }
                        }else{
                                print "\r\r\rEmail Belum Masuk!.. ".$a." Sec...";
                                if($a == 50)
                                {
                                        print PHP_EOL."Next...".PHP_EOL;
                                        return false;
                                }
                        }
                }       
        }

        private function getLinkActivasi($url, $PHPSESSID)
        {
                $headers[] = "Authorization: ApiToken ".$this->getIpToken();
                $headers[] = "Content-Type: application/x-www-form-urlencoded; charset=UTF-8"; 
                $headers[] = "Cookie: PHPSESSID=".$PHPSESSID."; __cfduid=d7ce4997ba4fe3d0b677434d8e17e73fe1554249274; _ga=GA1.2.1939465402.1554249275; _gid=GA1.2.263874565.1554249275"; 
                $headers[] = "Host: www.guerrillamail.com"; 
                $headers[] = "Origin: https://www.guerrillamail.com"; 
                $headers[] = "Referer: https://www.guerrillamail.com/inbox"; 
                $headers[] = "User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.86 Safari/537.36"; 
                $headers[] = "X-Requested-With: XMLHttpRequest"; 
                $getLinkActivasi = $this->curl($url, null, false, false, $headers, 'GET');
                @$linkActivasi = $this->getStr($getLinkActivasi, '<br><a href="', '">', 1, 0);
                if(!empty($linkActivasi))
                {
                        return $linkActivasi;
                }else{
                        print "Link Activasi Tidak Ada!";
                }
        }

        public function temporraryMail()
        {
                $mailName = $this->mailName;
                $PHPSESSID = $this->randStr("huruf_angka", "20");
                $createMail = $this->createMail($mailName, $PHPSESSID); 
                if(strpos($createMail, '"success":true'))
                {
                        $pageInbox = $this->pageInbox($mailName, $PHPSESSID);
                        if(strpos($pageInbox, 'https://www.guerrillamail.com/') or preg_match('/www.guerrillamail.com/i', $pageInbox))
                        {
                                $getLinkActivasi = $this->getLinkActivasi($pageInbox, $PHPSESSID);
                                return $getLinkActivasi;
                        }else{  
                                print $pageInbox;
                        }
                }else{  
                        return "Failed Create Email";
                }
        }
}

class modules 
{
        public function curl($url, $params, $cookie, $header, $httpheaders, $request = 'POST', $socks = "")
        {
                $this->ch = curl_init();
                        
                curl_setopt($this->ch, CURLOPT_URL, $url);
                curl_setopt($this->ch, CURLOPT_POSTFIELDS, $params);
                curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, 0);
                curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, 1);

                curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, $request);

                if($cookie == true)
                {       
                        $cookies = tempnam('/tmp','cookie.txt');
                        curl_setopt($this->ch, CURLOPT_COOKIEJAR, $cookies);
                        curl_setopt($this->ch, CURLOPT_COOKIEFILE, $cookies);
                }

                curl_setopt($this->ch, CURLOPT_HEADER, $header);
                @curl_setopt($this->ch, CURLOPT_HTTPHEADER, $httpheaders);

                curl_setopt($this->ch, CURLOPT_HTTPPROXYTUNNEL, 1);
                curl_setopt($this->ch, CURLOPT_PROXY, $socks);
                curl_setopt($this->ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS4);

                curl_setopt($this->ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
                $response = curl_exec($this->ch);
                return $response;
                curl_close($this->ch);
        }


        public function getStr($page, $str1, $str2, $line_str2, $line)
        {
                $get = explode($str1, $page);
                $get2 = explode($str2, $get[$line_str2]);
                return $get2[$line];
        }

        public function randStr($type, $length) 
        {
                $characters = array();
                $characters['angka'] = '0123456789';
                $characters['kapital'] = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
                $characters['huruf'] = 'abcdefghijklmnopqrstuvwxyz';
                $characters['kapital_angka'] = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
                $characters['huruf_angka'] = '0123456789abcdefghijklmnopqrstuvwxyz';
                $characters['all'] = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                $charactersLength = strlen($characters[$type]);
                $randomString = '';

                for ($i = 0; $i < $length; $i++) 
                {
                        $randomString .= $characters[$type][rand(0, $charactersLength - 1)];
                }

                return $randomString;

        }  

        public function randNama()
        {
                $get = file_get_contents("https://api.randomuser.me");
                $j = json_decode($get, true);
                $first = $j['results'][0]['name']['first'];
                $last = $j['results'][0]['name']['last'];
                $nama = $first .$last.$this->randStr('huruf_angka','2');
                $rand = rand(00000,99999);
                $domain = array("@gmail.com","@yahoo.com","@hotmail.co.id");
                $email = $first.$last.$this->randStr("all", "2").$domain[rand(0, 2)];   
                $nomorhp = "+628".$this->randStr('angka','10')."";
                $password = $first.$this->randStr('huruf_angka','6');   
                if(empty($first) or empty($last))
                {
                        $this->randNama();
                }else{
                        return array("first" => $first, "last" => $last, "nama" => $nama, "email" => $email, "nope" => $nomorhp, "password" => $password);
                }
        } 

        public function fwrites($namafile, $data)
        {
                $fh = fopen($namafile, "a");
                fwrite($fh, $data);
                fclose($fh);  
        }

        public function fetchLocation($source) 
        {
                preg_match_all('/^Location:\s*([^;]*)/mi', $source, $matches);
                $cookies = array();
                foreach($matches[1] as $item) 

                {
                        parse_str($item, $cookie);
                        $cookies = array_merge($cookies, $cookie);
                }

                return $cookies;
        }
}


$bigToken = new bigToken();
//$tempMail = new tempMail("asspakhaji69");
$modules = new modules();

echo "[?] Referral Code : ";
$referralCode = trim(fgets(STDIN));
echo "[?] Jumlah : ";
$jumlah = trim(fgets(STDIN));

for($a=1;$a<=$jumlah;$a++)
{
        print $bigToken->registerAccount($referralCode);
}

?>
