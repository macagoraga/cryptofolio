<?php
include 'config.php';
if ($_POST['action'] == 'load'){
    

    $string = file_get_contents($configfile, true);

    $localdata = json_decode($string, true); 
    $api = json_encode($localdata);
   print_r($api);
}


if (isset($_GET['action']) && $_GET['action'] == 'save'){
   
    $string = file_get_contents($configfile, true);
    $localdata = json_decode($string, true); 

    unset($localdata['api']);
    unset($localdata['investment']);
    unset($localdata['user']);
    $localdata['user']['username']=trim($_GET['username']);
    $localdata['user']['password']=trim($_GET['password']);
    
    $localdata['api']['bittrex']['api']=trim($_GET['bittrexapi']);
    $localdata['api']['bittrex']['secret']=trim($_GET['bittrexsecret']);
    $localdata['api']['kraken']['api']=trim($_GET['krakenapi']);
    $localdata['api']['kraken']['secret']=trim($_GET['krakensecret']);
    $localdata['api']['poloniex']['api']=trim($_GET['poloniexapi']);
    $localdata['api']['poloniex']['secret']=trim($_GET['poloniexsecret']);
    $localdata['api']['binance']['api']=trim($_GET['binanceapi']);
    $localdata['api']['binance']['secret']=trim($_GET['binancesecret']);
    $localdata['api']['kucoin']['api']=trim($_GET['kucoinapi']);
    $localdata['api']['kucoin']['secret']=trim($_GET['kucoinsecret']);

    $localdata['investment']['amount']=trim($_GET['investment']);
    
    $json_data = json_encode($localdata,true);
    
    file_put_contents($configfile, $json_data);
    header('Location: index.php');
    
    exit;
}

?>