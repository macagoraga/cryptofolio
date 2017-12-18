<?php
$ini_array = parse_ini_file("api.ini.php");
$allcoins = file_get_contents('lib/data/coinlist.json');
$coinList = json_decode($allcoins, true);



include_once 'functions.php';
$ini_array = parse_ini_file("api.ini.php");
include_once 'lib/portfolio_bittrex.php';
include_once 'lib/portfolio_local.php';


foreach ($coindata['coins'] as $key => $val) {
    
    foreach ($val as $i => $v) {
    
    $output[] = array("symbol"=>$i, "name"=>$v['name'], "wallettype"=>$v['wallettype'], "walletname"=>$v['walletname'], "coinsOwned"=>$v['owned'], "fullName"=>$v['fullname'], "image"=>symbol_lookup($i));
     $coins = $output;
}
}
function act_cmp_function($a, $b) {
        if ($a['name'] > $b['name']) {
                return 1;
        } else if ($a['name'] < $b['name']) {
                return -1;
        } else {
                return 0;
        }
}


uasort($coins, 'act_cmp_function');

?>
