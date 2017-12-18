<?php 
$getsymbols = $_GET['symbols'].",BTC";

$symbols = implode(',', array_unique(explode(',', $getsymbols)));

$prices = file_get_contents("https://min-api.cryptocompare.com/data/pricemultifull?fsyms=$symbols&tsyms=BTC,EUR");

//$priceArray = json_decode($prices, true);
print_r($prices);
?>