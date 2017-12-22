<?php
require 'functions.php';


$allcoins = file_get_contents('lib/data/coinlist.json');
$coinList = json_decode($allcoins, true);
$ini_array = parse_ini_file("api.ini.php",true);

$investment = $ini_array['investment'];
// load manual coins 

$string = file_get_contents("lib/data/portfolio.json", true);

$coinsmanual = json_decode($string, true);
if(sizeof($coinsmanual['coins'])>0){
	foreach ($coinsmanual['coins'] as $symbol => $value){

		$coindata['coins']['manual'][$symbol]['wallettype'] = 'manual';
		$coindata['coins']['manual'][$symbol]['walletname'] = $coinsmanual['coins'][$symbol]['walletname'];
		$coindata['coins']['manual'][$symbol]['name'] = $coinList['Data'][$symbol]['CoinName'];
	    $coindata['coins']['manual'][$symbol]['fullname'] =  $coinsmanual['coins'][$symbol]['fullname'];
	    $coindata['coins']['manual'][$symbol]['owned'] = $coinsmanual['coins'][$symbol]['owned'];

	}
}

// Kraken API - get balances

if($ini_array['kraken']['api']!=''){
 
	require_once 'KrakenAPIClient.php'; 

	$beta = false; 
	$url = $beta ? 'https://api.beta.kraken.com' : 'https://api.kraken.com';
	$sslverify = $beta ? false : true;
	$version = 0;

	$kraken = new \Payward\KrakenAPI($ini_array['kraken']['api'], $ini_array['kraken']['secret'], $url, $version, $sslverify);
	$krakendata = $kraken->QueryPrivate('Balance');
	

	foreach ($krakendata['result'] as $value => $v) {

			$symbol = substr($value,-3);
			
			if($symbol!='EUR'){

				if($symbol=='XBT'){
					$symbol='BTC';
				}

				$coindata['coins']['kraken'][$symbol]['wallettype'] = 'exchange';
				$coindata['coins']['kraken'][$symbol]['walletname'] = 'kraken';
		    	$coindata['coins']['kraken'][$symbol]['name'] = $coinList['Data'][$symbol]['CoinName'];
		    	$coindata['coins']['kraken'][$symbol]['fullname'] = $coinList['Data'][$symbol]['FullName'];
		    	$coindata['coins']['kraken'][$symbol]['owned'] = $v;
	
			}
	}
}

// BitTrex API - get balances

if($ini_array['bittrex']['api']!=''){
 
 	$nonce=time();
	$uri='https://bittrex.com/api/v1.1/account/getbalances?apikey='. $ini_array['bittrex']['api'] .'&nonce='.$nonce;
	$sign=hash_hmac('sha512',$uri,$ini_array['bittrex']['secret']);
	$ch = curl_init($uri);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('apisign:'.$sign));
	$execResult = curl_exec($ch);
	$bittrex = json_decode($execResult,true);

	foreach ($bittrex['result'] as $value) {

		$coindata['coins']['bittrex'][$value['Currency']]['wallettype'] = 'exchange';
		$coindata['coins']['bittrex'][$value['Currency']]['walletname'] = 'bittrex';
    	$coindata['coins']['bittrex'][$value['Currency']]['name'] = $coinList['Data'][$value['Currency']]['CoinName'];
    	$coindata['coins']['bittrex'][$value['Currency']]['fullname'] = $coinList['Data'][$value['Currency']]['FullName'];
    	$coindata['coins']['bittrex'][$value['Currency']]['owned'] = $value["Balance"];

	}

}

// portfolio array 

if(sizeof($coindata)>0){
foreach ($coindata['coins'] as $key => $val) {
    
    foreach ($val as $i => $v) {
    if($v['owned']>0.005){
	    $output[] = array("symbol"=>$i, "name"=>$v['name'], "wallettype"=>$v['wallettype'], "walletname"=>$v['walletname'], "coinsOwned"=>$v['owned'], "fullName"=>$v['fullname'], "image"=>symbol_lookup($i));
	     $coins = $output;
	    }
    }

}

    uasort($coins, 'act_cmp_function');

}
?>
