<?php
include_once 'functions.php';

$string = file_get_contents("lib/data/portfolio.json", true);
$coins = json_decode($string, true);


if (isset($_GET['symbol']) && isset($_GET['action']) && $_GET['action'] == 'addcoin')
{   
    $allcoins = file_get_contents("https://www.cryptocompare.com/api/data/coinlist/");
    $coinnames = json_decode($allcoins, true);

    $getsymbol = strtoupper($_GET['symbol']);
    $gettotal = $_GET['total'];
    $getamount = $_GET['amount'];
    $getcurrency = $_GET['currency'];
    $getbuydate = $_GET['buydate'];
    $timestamp = strtotime($_GET['buydate']);

    // retrieve price in currency at buy date and calculate total amount back to EUR
    if($getcurrency!="EUR"){
        echo "not EUR";
        $prices = file_get_contents("https://min-api.cryptocompare.com/data/pricehistorical?fsym=$getcurrency&tsyms=EUR&ts=$timestamp");
        $price = json_decode($prices, true);
        $priceInEur = $price[$getcurrency]['EUR'];
        $totalPriceInEur = (($priceInEur*$getamount)*$gettotal);
    }
    else{
        $totalPriceInEur =  $getamount; 
    }
    
    $coins['coins'][$getsymbol];
    $coins['coins'][$getsymbol]['name'] = $coinnames['Data'][$getsymbol]['CoinName'];
    $coins['coins'][$getsymbol]['fullname'] = $coinnames['Data'][$getsymbol]['FullName'];
    $coins['coins'][$getsymbol]['currency'] = 'EUR';
    $coins['coins'][$getsymbol]['owned'] = $gettotal;
    $coins['coins'][$getsymbol]['paid'] = number_format($totalPriceInEur,2,'.', '');
    $coins['coins'][$getsymbol]['buydate'] = $timestamp;
    $json_data = json_encode($coins);
    file_put_contents('lib/data/portfolio.json', $json_data);
    header('Location: index.php');
    exit;
}

if ($_GET['action'] == 'remove')
{
    $getsymbol = strtoupper($_GET['symbol']);
    unset($coins['coins'][$getsymbol]);
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
