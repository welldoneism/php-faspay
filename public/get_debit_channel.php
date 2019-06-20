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


$fsp = new FaspayLib;         

$userid = $config['user_id'];
$password = $config['password'];
$merchantCode = $config['merchant_code'];
$merchantName = $config['merchant_name'];
$signature = $config['signature'];
$production = $config['production'];
$expirationHours = $config['expiration_hours'];

$fsp->faspay_init($userid,$password,$merchantCode,$merchantName,$signature,$production,$expirationHours);
$url = $fsp->getChannelUrl();

$data = array(
    "request" => "Daftar Payment Channel",
    "merchant_id" => $merchantCode,
    "merchant" => $merchantName,
    "signature" => $signature
);

$ChannelList = $fsp->getChannelList($url,$data);

echo '<h1>Choose your payment method</h1><hr>';
echo $ChannelList;

?>
