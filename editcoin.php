<?php
include 'config.php';
$string = file_get_contents($configfile, true);
$localdata = json_decode($string, true); 

if (isset($_GET['symbol']) && isset($_GET['action']) && $_GET['action'] == 'addcoin' && isset($_GET['symbol']) && $_GET['symbol']!='' && $_GET['total']!='')
{  
  
  	$allcoins = file_get_contents('lib/data/coinlist.json');
    // $allcoins = file_get_contents("https://www.cryptocompare.com/api/data/coinlist/");
    $coinnames = json_decode($allcoins, true);

    $getSymbol = strtoupper($_GET['symbol']);
    $totalCoins = $_GET['total'];
    $walletName = $_GET['walletname'];
    
    $localdata['coins'][$getSymbol]['wallettype'] = 'manual';
    $localdata['coins'][$getSymbol]['walletname'] = $walletName;
    $localdata['coins'][$getSymbol]['name'] = $coinnames['Data'][$getSymbol]['CoinName'];
    $localdata['coins'][$getSymbol]['fullname'] = $coinnames['Data'][$getSymbol]['FullName'];
    $localdata['coins'][$getSymbol]['owned'] = $totalCoins;
    $json_data = json_encode($localdata,true);
    
  
    file_put_contents($configfile, $json_data);
    sleep(1);
    header('Location: index.php');
  exit;
}


if ($_GET['action'] == 'remove')
{
    $getSymbol = strtoupper($_GET['symbol']);
    unset($localdata['coins'][$getSymbol]);
    $json_data = json_encode($localdata);
    file_put_contents('config.json', $json_data);
    sleep(1);
    header('Location: index.php');
    exit;
}
?>