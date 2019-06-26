<?php
session_start();
$config = include('../config/faspay.php');
require '../vendor/autoload.php';
use App\Controllers\FaspayLib;


if (!isset($_SESSION['trx_id']))
{
    return false;
}

$fsp = new FaspayLib;         

$userid = $config['user_id'];
$password = $config['password'];
$merchantCode = $config['merchant_code'];
$merchantName = $config['merchant_name'];
$production = $config['production'];
$expirationHours = $config['expiration_hours'];


$channel = (isset($_GET['channel'])) ? $_GET['channel'] : '0' ; 

$fsp->faspay_init($userid,$password,$merchantCode,$merchantName,$production,$expirationHours);
$url = $fsp->getPaymentUrl(); 


$curr_date = $expirationHours;
$exp = strtotime("+".$expirationHours." hours");
$exp_date=date('Y-m-d H:i:s',$exp); 

$trx_id=$_SESSION['trx_id'];
$grand_total=$_SESSION['grand_total'];

$signature = sha1(md5($userid.$password.$trx_id));

$gtotal=$grand_total.'00';

$data = array(
    "request"=>"Transmisi Info Detail Pembelian",
    "merchant_id"=>"$merchantCode",
    "merchant"=>"$merchantName",
    "bill_no"=>"$trx_id",    
    "bill_date"=>$curr_date,
    "bill_expired"=>$exp_date,
    "bill_desc"=>"Pembayaran #12345678",
    "bill_currency"=>"IDR",
    "bill_gross"=>"0",
    "bill_miscfee"=>"0",
    "bill_total"=>"$gtotal",
    "cust_no"=>"12",
    "cust_name"=>"Test Trx",
    "payment_channel"=>$channel,
    "pay_type"=>"1", 
    "terminal"=>"10",
    "billing_name"=>"0",
    "billing_lastname"=>"0",
    "billing_address"=>"jalan pintu air raya",
    "billing_address_city"=>"Jakarta Pusat",
    "billing_address_region"=>"DKI Jakarta",
    "billing_address_state"=>"Indonesia",
    "billing_address_poscode"=>"10710",
    "billing_msisdn"=>"",
    "billing_address_country_code"=>"ID",
    "receiver_name_for_shipping"=>"Faspay Test",
    "shipping_lastname"=>"",
    "shipping_address"=>"jalan pintu air raya",
    "shipping_address_city"=>"Jakarta Pusat",
    "shipping_address_region"=>"DKI Jakarta",
    "shipping_address_state"=>"Indonesia",
    "shipping_address_poscode"=>"10710",
    "shipping_msisdn"=>"628909864434",
    "shipping_address_country_code"=>"ID",
    
    "item"=>array(  
          "product"=>"Invoice No. inv-985/2017-03/1234567891",
          "qty"=>"1",
          "amount"=>"$gtotal",
          "payment_plan"=>"01",
          "merchant_id"=>"99999",
          "tenor"=>"00"
        )
      ,
      
      "signature"=>"$signature"
);


$data_result = $fsp->registerPayment($url,$data,false);

echo '<h1>Detail Payment</h1><hr>';

?>

<table> 
    <tr>
        <td>trx_id</td>
        <td>:</td>
        <td><?=$data_result->trx_id;?></td>
    </tr>
    <tr>
        <td>merchant_id</td>
        <td>:</td>
        <td><?=$data_result->merchant_id;?></td>
    </tr>  
    <tr>
        <td>merchant</td>
        <td>:</td>
        <td><?=$data_result->merchant;?></td>
    </tr>
    <tr>
        <td>bill_no</td>
        <td>:</td>
        <td><?=$data_result->bill_no;?></td>
    </tr>
    <tr>
        <td>response_code</td>
        <td>:</td>
        <td><?=$data_result->response_code;?></td>
    </tr>
    <tr>
        <td>response_desc</td>
        <td>:</td>
        <td><?=$data_result->response_desc;?></td>
    </tr>
    <tr>
        <td>redirect_url</td>
        <td>:</td>
        <td><?=$data_result->redirect_url;?></td>
    </tr>
</table>

<?
    if ($data_result->response_code=='00'){
        $items = $data_result->bill_items;
?>

<br>
<h3>Detail Item</h3>
<br>
<style>
    table {
  border-collapse: collapse;
}

.items {
  border: 1px solid black;
}
</style>
<table class="items">
    
    <?foreach ($items as $item) {?>
        <tr class="items">
            <td class="items">Product</td>
            <td class="items">:</td>
            <td class="items"><?=$item->product;?></td>
        </tr>
        <tr class="items">
            <td class="items">Qty</td>
            <td class="items">:</td>
            <td class="items"><?=$item->qty;?></td>
        </tr>
        <tr class="items">
            <td class="items">Amount</td>
            <td class="items">:</td>
            <td class="items"><?=$item->amount;?></td>
        </tr>
        <tr class="items">
            <td class="items">Payment_Plan</td>
            <td class="items">:</td>
            <td class="items"><?=$item->payment_plan;?></td>
        </tr>
        <tr class="items">
            <td class="items">Merchant_Id</td>
            <td class="items">:</td>
            <td class="items"><?=$item->merchant_id;?></td>
        </tr>
        <tr class="items">
            <td class="items">Tenor</td>
            <td class="items">:</td>
            <td class="items"><?=$item->tenor;?></td>
        </tr>
    <?}?>
    
</table>
<br>

<style>
    .button {
      font: bold 20px Arial;
      text-decoration: none;
      background-color: #ffa500;
      color: #333333;
      padding: 2px 6px 2px 6px;
      border-top: 1px solid #CCCCCC;
      border-right: 1px solid #333333;
      border-bottom: 1px solid #333333;
      border-left: 1px solid #CCCCCC;
    }
</style>

<a href="<?=$data_result->redirect_url;?>" target="_blank" class="button">
    Pay Now
</a>
<?}?>