<?php
if ($_POST['action'] == 'load'){
    
    $string = file_get_contents("config.json", true);
    $localdata = json_decode($string, true); 
    $api = json_encode($localdata);
    print_r($api);
}


if (isset($_GET['action']) && $_GET['action'] == 'save'){
   
    $string = file_get_contents("config.json", true);
    $localdata = json_decode($string, true); 

    unset($localdata['api']);
    unset($localdata['investment']);
    $localdata['api']['bittrex']['api']=trim($_GET['bittrexapi']);
    $localdata['api']['bittrex']['secret']=trim($_GET['bittrexsecret']);
    $localdata['api']['kraken']['api']=trim($_GET['krakenapi']);
    $localdata['api']['kraken']['secret']=trim($_GET['krakensecret']);
    $localdata['api']['poloniex']['api']=trim($_GET['poloniexapi']);
    $localdata['api']['poloniex']['secret']=trim($_GET['poloniexsecret']);
    $localdata['api']['binance']['api']=trim($_GET['binanceapi']);
    $localdata['api']['binance']['secret']=trim($_GET['binancesecret']);


    $localdata['investment']['amount']=trim($_GET['investment']);
    
    $json_data = json_encode($localdata,true);
    
    file_put_contents('config.json', $json_data);
    header('Location: index.php');
    
    exit;
}
// print_r($localdata);

// if (isset($_GET['symbol']) && isset($_GET['action']) && $_GET['action'] == 'addcoin')
// {  
  
//   	$allcoins = file_get_contents('lib/data/coinlist.json');
//     // $allcoins = file_get_contents("https://www.cryptocompare.com/api/data/coinlist/");
//     $coinnames = json_decode($allcoins, true);

//     $getSymbol = strtoupper($_GET['symbol']);
//     $totalCoins = $_GET['total'];
//     $walletName = $_GET['walletname'];
    
//     $localdata['coins'][$getSymbol]['wallettype'] = 'manual';
//     $localdata['coins'][$getSymbol]['walletname'] = $walletName;
//     $localdata['coins'][$getSymbol]['name'] = $coinnames['Data'][$getSymbol]['CoinName'];
//     $localdata['coins'][$getSymbol]['fullname'] = $coinnames['Data'][$getSymbol]['FullName'];
//     $localdata['coins'][$getSymbol]['owned'] = $totalCoins;
//     $json_data = json_encode($localdata,true);
    
  
//     file_put_contents('lib/data/portfolio.json', $json_data);
//     header('Location: index.php');
//   exit;
// }

// if ($_GET['action'] == 'remove')
// {
//     $getSymbol = strtoupper($_GET['symbol']);
//     unset($localdata['coins'][$getSymbol]);
//     $json_data = json_encode($localdata);

//     print_r($json_data);
//     file_put_contents('lib/data/portfolio.json', $json_data);
//     header('Location: index.php');
//     exit;
// }
?>