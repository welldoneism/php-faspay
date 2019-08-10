<?php

require '../vendor/autoload.php';
use App\Controllers\FaspayLib;

$fsp = new FaspayLib;


?>
<h1>Faspay Payment Gateway</h1>
<hr>
                                 
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
      cursor:pointer;
    }
</style>

 <form method="post" action="get_debit_channel.php" target="_blank">
  Trx.ID:<br>
  <input type="text" name="trx_id" value="233850" readonly="readonly"><br>
  Grand Total:<br>
  <input type="text" name="grand_total" value="45000" readonly="readonly">
  <br>
  <br>
  <input class="button" type="submit" value="Submit" name="submit">
</form> 


