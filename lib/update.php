<?php 
$getsymbols = $_GET['symbols'].",BTC,ETH,LTC";
$symbols = implode(',', array_unique(explode(',', $getsymbols)));
$prices = file_get_contents("https://min-api.cryptocompare.com/data/pricemultifull?fsyms=$symbols&tsyms=BTC,EUR");

print_r($prices);
?>

