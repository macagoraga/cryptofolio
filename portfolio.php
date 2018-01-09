<?php



if(isset($config['coins']) && sizeof($config['coins'])>0){
	foreach ($config['coins'] as $symbol => $value){

		$coindata['coins']['manual'][$symbol]['wallettype'] = 'manual';
		$coindata['coins']['manual'][$symbol]['walletname'] = $config['coins'][$symbol]['walletname'];
		$coindata['coins']['manual'][$symbol]['name'] = $coinList['Data'][$symbol]['CoinName'];
	    $coindata['coins']['manual'][$symbol]['fullname'] =  $config['coins'][$symbol]['fullname'];
	    $coindata['coins']['manual'][$symbol]['owned'] = $config['coins'][$symbol]['owned'];

	}
}

// Kraken API - get balances

if(isset($config['api']['kraken']['api']) && $config['api']['kraken']['api']!=''){
	require_once 'lib/KrakenAPIClient.php'; 

	$beta = false; 
	$url = $beta ? 'https://api.beta.kraken.com' : 'https://api.kraken.com';
	$sslverify = $beta ? false : true;
	$version = 0;

	$kraken = new \Payward\KrakenAPI($config['api']['kraken']['api'], $config['api']['kraken']['secret'], $url, $version, $sslverify);
	$krakendata = $kraken->QueryPrivate('Balance');
	

	foreach ($krakendata['result'] as $value => $v) {

		$symbol = substr($value,-3);
		
		if($symbol!='EUR'){

			if($symbol=='XBT'){
				$symbol='BTC';
			}

			$coindata['coins']['kraken'][$symbol]['wallettype'] = 'exchange';
			$coindata['coins']['kraken'][$symbol]['walletname'] = 'Kraken';
	    	$coindata['coins']['kraken'][$symbol]['name'] = $coinList['Data'][$symbol]['CoinName'];
	    	$coindata['coins']['kraken'][$symbol]['fullname'] = $coinList['Data'][$symbol]['FullName'];
	    	$coindata['coins']['kraken'][$symbol]['owned'] = $v;

		}
	}
}

// BitTrex API - get balances

if(isset($config['api']['bittrex']['api']) && $config['api']['bittrex']['api']!=''){
 
 	$nonce=time();
	$uri='https://bittrex.com/api/v1.1/account/getbalances?apikey='. $config['api']['bittrex']['api'] .'&nonce='.$nonce;
	$sign=hash_hmac('sha512',$uri,$config['api']['bittrex']['secret']);
	$ch = curl_init($uri);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('apisign:'.$sign));
	$execResult = curl_exec($ch);
	$bittrex = json_decode($execResult,true);


	foreach ($bittrex['result'] as $value) {
		if($value["Balance"]>0.0009){
		$coindata['coins']['bittrex'][$value['Currency']]['wallettype'] = 'exchange';
		$coindata['coins']['bittrex'][$value['Currency']]['walletname'] = 'BitTrex';
    	$coindata['coins']['bittrex'][$value['Currency']]['name'] = $coinList['Data'][$value['Currency']]['CoinName'];
    	$coindata['coins']['bittrex'][$value['Currency']]['fullname'] = $coinList['Data'][$value['Currency']]['FullName'];
    	$coindata['coins']['bittrex'][$value['Currency']]['owned'] = $value["Balance"];
		}
	}
}


// Binance API - get balances

if(isset($config['api']['binance']['api']) && $config['api']['binance']['api']!=''){
 	require_once 'lib/BinanceAPIClient.php'; 
   
    $api = new Binance($config['api']['binance']['api'],$config['api']['binance']['secret']);
	$binance = $api->balances();

    foreach ($binance as $key => $value) {

        if($value['available']>0.0009){
            $coindata['coins']['binance'][$key]['wallettype'] = 'exchange';
            $coindata['coins']['binance'][$key]['walletname'] = 'Binance';
            $coindata['coins']['binance'][$key]['name'] = $coinList['Data'][$key]['CoinName'];
            $coindata['coins']['binance'][$key]['fullname'] = $coinList['Data'][$key]['FullName'];
            $coindata['coins']['binance'][$key]['owned'] = $value["available"];
        }
    }
}



// Poloniex API - get balances

if(isset($config['api']['poloniex']['api']) && $config['api']['poloniex']['api']!=''){
 
 	require_once 'lib/PoloniexAPIClient.php'; 
 	$polo = new poloniex($config['api']['poloniex']['api'],$config['api']['poloniex']['secret']);
	$poloniex = $polo->get_balances();
	
	foreach ($poloniex as $currency => $value) {
		if($value>0){
		
			if($currency=='STR'){ $currency='XLM'; }
		
			$coindata['coins']['poloniex'][$currency]['wallettype'] = 'exchange';
			$coindata['coins']['poloniex'][$currency]['walletname'] = 'Poloniex';
	    	$coindata['coins']['poloniex'][$currency]['name'] = $coinList['Data'][$currency]['CoinName'];
	    	$coindata['coins']['poloniex'][$currency]['fullname'] = $coinList['Data'][$currency]['FullName'];
	    	$coindata['coins']['poloniex'][$currency]['owned'] = $value;
    	}
	}
}

// Kucoin API - get balances
if(isset($config['api']['kucoin']['api']) && $config['api']['kucoin']['api']!=''){
	
	$kucoinapi = $config['api']['kucoin']['api'];
	$kucoinsecret = $config['api']['kucoin']['secret'];

	$host = 'https://api.kucoin.com';
	$endpoint = '/v1/account/balance';
	$querystring = '';
	$nonce = round(microtime(true) * 1000);
	$signstring = $endpoint.'/'.$nonce.'/'.$querystring;
	$hash = hash_hmac('sha256',  base64_encode($signstring) , $kucoinsecret);

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "https://api.kucoin.com" . $endpoint);

	$headers = [
	  'KC-API-SIGNATURE:' . $hash,
	  'KC-API-KEY:' . $kucoinapi,
	  'KC-API-NONCE:' . $nonce,
	  'Content-Type:application/json'
	];

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_USERAGENT,
	    'Mozilla/4.0 (compatible; Kucoin Bot; '.php_uname('a').'; PHP/'.phpversion().')'
	);
	curl_setopt($ch, CURLOPT_URL, "https://api.kucoin.com" . $endpoint);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

	$kc = curl_exec($ch);
	$kucoin = json_decode($kc,true);
	
	for($a=0;$a<sizeof($kucoin['data']);$a++){
		$x = $kucoin['data'][$a];
		$currency = $x['coinType'];
		if($x['balance']>0 && strpos( $x['balance'], 'E' ) == false){
			$coindata['coins']['kucoin'][$currency]['wallettype'] = 'exchange';
			$coindata['coins']['kucoin'][$currency]['walletname'] = 'Kucoin';
	    	$coindata['coins']['kucoin'][$currency]['name'] = $coinList['Data'][$currency]['CoinName'];
	    	$coindata['coins']['kucoin'][$currency]['fullname'] = $coinList['Data'][$currency]['FullName'];
	    	$coindata['coins']['kucoin'][$currency]['owned'] = $x['balance'];
		}
	}


}

// portfolio array 

if(isset($coindata) && sizeof($coindata)>0){
foreach ($coindata['coins'] as $key => $val) {
    
    foreach ($val as $i => $v) {
    if($v['owned']>0.001 || $v['walletname']=='watch'){
	    $output[] = array("symbol"=>$i, "name"=>$v['name'], "wallettype"=>$v['wallettype'], "walletname"=>$v['walletname'], "coinsOwned"=>$v['owned'], "fullName"=>$v['fullname'], "image"=>symbol_lookup($i));
	     $coins = $output;
	    }
    }

}

    uasort($coins, 'act_cmp_function');

}

?>
