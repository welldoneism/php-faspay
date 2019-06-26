<?php
session_start();
$config = include('../config/faspay.php');
require '../vendor/autoload.php';
use App\Controllers\FaspayLib;


if (!isset($_POST['submit']))
{
    return false;
}

$_SESSION['trx_id'] = $_POST['trx_id'];
$_SESSION['grand_total'] = $_POST['grand_total'];

$trx_id = $_SESSION['trx_id'];

$fsp = new FaspayLib;         

$userid = $config['user_id'];
$password = $config['password'];
$merchantCode = $config['merchant_code'];
$merchantName = $config['merchant_name'];
$production = $config['production'];
$expirationHours = $config['expiration_hours'];

$fsp->faspay_init($userid,$password,$merchantCode,$merchantName,$production,$expirationHours);

$signature = sha1(md5($userid.$password));

$url = $fsp->getChannelUrl();

$data = array(
    "request" => "Daftar Payment Channel",
    "merchant_id" => "$merchantCode",
    "merchant" => "$merchantName",
    "signature" => "$signature"
);

$ChannelList = $fsp->getChannelList($url,$data);

echo '<h1>Choose your payment method</h1><hr>';
echo $ChannelList;

?>
