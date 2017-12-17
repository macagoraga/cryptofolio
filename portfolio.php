<?php
include_once 'functions.php';

$string = file_get_contents("lib/data/portfolio.json", true);
$coins = json_decode($string, true);


if (isset($_GET['symbol']) && isset($_GET['action']) && $_GET['action'] == 'addcoin')
{   
    $allcoins = file_get_contents("https://www.cryptocompare.com/api/data/coinlist/");
    $coinnames = json_decode($allcoins, true);

    $getSymbol = strtoupper($_GET['symbol']);
    $totalCoins = $_GET['total'];
    $amountPaid = $_GET['amount'];
    $getCurrency = $_GET['currency'];
    $timestamp = strtotime($_GET['buydate']);

    // retrieve price in currency at buy date and calculate total amount back to EUR
    if($getCurrency!="EUR"){

        $prices = file_get_contents("https://min-api.cryptocompare.com/data/pricehistorical?fsym=$getSymbol&tsyms=EUR&ts=$timestamp");
        $price = json_decode($prices, true);
        $btcPriceInEur = $price[$getSymbol]['EUR'];
        $totalPriceInEur = ($btcPriceInEur*$totalCoins);
    }
    else{
        $totalPriceInEur =  $amountPaid; 
    }
    
    $coins['coins'][$getSymbol];
    $coins['coins'][$getSymbol]['name'] = $coinnames['Data'][$getSymbol]['CoinName'];
    $coins['coins'][$getSymbol]['fullname'] = $coinnames['Data'][$getSymbol]['FullName'];
    $coins['coins'][$getSymbol]['currency'] = 'EUR';
    $coins['coins'][$getSymbol]['owned'] = $totalCoins;
    $coins['coins'][$getSymbol]['paid'] = $totalPriceInEur;
    $coins['coins'][$getSymbol]['buydate'] = $timestamp;
    $json_data = json_encode($coins);
    
    file_put_contents('lib/data/portfolio.json', $json_data);
   header('Location: index.php');
   exit;
}

if ($_GET['action'] == 'remove')
{
    $getSymbol = strtoupper($_GET['symbol']);
    unset($coins['coins'][$getSymbol]);
    $json_data = json_encode($coins);

    file_put_contents('lib/data/portfolio.json', $json_data);
    header('Location: index.php');
    exit;
}

$symbols = implode(",", array_keys($coins['coins']));
$currencies = implode(",", array_values(array_column($coins['coins'], 'currency')));

$prices = file_get_contents("https://min-api.cryptocompare.com/data/pricemultifull?fsyms=$symbols&tsyms=$currencies");
$priceArray = json_decode($prices, true);
$output = array();

foreach ($coins['coins'] as $symbol => $value){

    $currency = strtoupper($value['currency']);
    $coinname = $value['name'];
    $price = round($priceArray['RAW'][$symbol][$currency]['PRICE'],2);
    $change = round($priceArray['RAW'][$symbol][$currency]['CHANGEPCT24HOUR'],2);
    $difference = round($price*$value['owned'],2);
    $profit = round($difference-$value['paid'],2);
          
    $output[] = array("symbol"=>$symbol , "currency"=>$currency, "updown"=>number_format($profit,2, '.', ''), "price"=>number_format($price,2,'.', ''), "change"=>$change, "image"=>symbol_lookup($symbol));
   
    $json = json_encode($output);
}

 print_r($json);
?>
