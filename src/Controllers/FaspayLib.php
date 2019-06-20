<?php

namespace App\Controllers;

class FaspayLib
{   
    protected $userid;
    protected $password;
    protected $merchantCode;
    protected $merchantName;
    protected $signature;
    protected $production;
    protected $expirationHours = 1;
    
    public function faspay_init($userid, $password, $merchantCode, $merchantName,$signature, $production,$expirationHours)
    {
        $this->userid = $userid;
        $this->password = $password;
        $this->merchantCode = $merchantCode;
        $this->merchantName = $merchantName;
        $this->signature = $signature;
        $this->production = $production;
        $this->expirationHours = $expirationHours;
    }
    
    public function getChannelUrl()
    {
        return $this->production ? 
          'https://web.faspay.co.id/cvr/100001/10' :
          'https://dev.faspay.co.id/cvr/100001/10';
    }
    
    public function getPaymentUrl()
    {
        return $this->production ? 
          'https://web.faspay.co.id/cvr/300011/10' :
          'https://dev.faspay.co.id/cvr/300011/10';
    }
    
    public function postData($url,$req)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                              
        $result = curl_exec($ch);
        curl_close($ch);
        
        return $result;   
    }
    
    public function getChannelList($url,array $data = [])
    {
        $req = json_encode($data); 
        $result = $this->postData($url,$req);
        $channels = json_decode($result);
        
        $result = '';
        
        if (isset( $channels->response_error )){
            $result = "something wrong";
        }else{
            $channel = $channels->payment_channel ;
            foreach ($channel as $chn) {
                $result .= '<a href="register_payment.php?channel='.$chn->pg_code.'">'.$chn->pg_name. '</a><br>';
            }     
        }
        
        return $result;
    }
    
    public function registerPayment($url,array $data = [],$redirect=false)
    {   
        $req = json_encode($data);        
        $result = $this->postData($url,$req);
        $data_return = json_decode($result);       
        
        if ($redirect==true){
            if(isset($data_return->redirect_url)){
                echo "<META http-equiv=\"refresh\" content=\"0;URL=".$data_return->redirect_url."\">";
            }else{
                echo "something wrong";
            }                   
        }
        return $data_return;                           
    }                                                  
   
  
    private function makeSignature($billNo)
    {
        return sha1(md5($this->userid . $this->password . $billNo));
    }
    
}








