<?php
include_once 'functions.php';
// load portfolio	
$string = file_get_contents("lib/data/portfolio.json", true);
$coins = json_decode($string, true);

if (isset($_GET['symbol']) && isset($_GET['action']) && $_GET['action'] == 'addcoin')
{
    $getsymbol = strtoupper($_GET['symbol']);
    $gettotal = $_GET['total'];
    $getamount = $_GET['amount'];
    $getcurrency = strtoupper($_GET['currency']);

    $coins['coins'][$getsymbol];
    $coins['coins'][$getsymbol]['currency'] = $getcurrency;
    $coins['coins'][$getsymbol]['owned'] = $gettotal;
    $coins['coins'][$getsymbol]['paid'] = $getamount;

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
    $price = round($priceArray['RAW'][$symbol][$currency]['PRICE'],2);
    $change = round($priceArray['RAW'][$symbol][$currency]['CHANGEPCT24HOUR'],2);
    $difference = round($price*$value['owned'],2);
    $profit = round($difference-$value['paid'],2);
          
    $output[] = array("symbol"=>$symbol , "currency"=>$currency, "updown"=>number_format($profit,2, '.', ''), "price"=>number_format($price,2,'.', ''), "change"=>$change, "image"=>symbol_lookup($symbol));
    $json = json_encode($output);
}

 print_r($json);
?>
