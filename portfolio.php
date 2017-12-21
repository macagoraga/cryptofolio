<?php
include_once 'functions.php';

$ini_array = parse_ini_file("api.ini.php");
$allcoins = file_get_contents('lib/data/coinlist.json');
$coinList = json_decode($allcoins, true);
$ini_array = parse_ini_file("api.ini.php",true);

include_once 'lib/portfolio_local.php';
usleep(100000);
include_once 'lib/portfolio_kraken.php';
usleep(100000);
include_once 'lib/portfolio_bittrex.php';
usleep(100000);


if(sizeof($coindata)>0){
foreach ($coindata['coins'] as $key => $val) {
    
    foreach ($val as $i => $v) {
    
    $output[] = array("symbol"=>$i, "name"=>$v['name'], "wallettype"=>$v['wallettype'], "walletname"=>$v['walletname'], "coinsOwned"=>$v['owned'], "fullName"=>$v['fullname'], "image"=>symbol_lookup($i));
     $coins = $output;
    }

}

    uasort($coins, 'act_cmp_function');

}
?>
