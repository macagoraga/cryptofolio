<?php


// debug
//$krakendata = Array('error' => Array(), 'result' => Array('ETH' => 3415.8014, 'ZEUR' => 155.5649, 'XBTC' => 149.9688412800, 'XXRP' => 499889.51600000));
 

	$apikey=$ini_array['krakenapi'];
	$apisecret=$ini_array['krakensecret'];

	if($apikey!=''){
	 
		require_once 'KrakenAPIClient.php'; 

		$beta = false; 
		$url = $beta ? 'https://api.beta.kraken.com' : 'https://api.kraken.com';
		$sslverify = $beta ? false : true;
		$version = 0;

		$kraken = new \Payward\KrakenAPI($apikey, $apisecret, $url, $version, $sslverify);
		$krakendata = $kraken->QueryPrivate('Balance');
		


		foreach ($krakendata['result'] as $value => $v) {

			
				$coindata['coins']['kraken'][$value]['wallettype'] = 'exchange';
				$coindata['coins']['kraken'][$value]['walletname'] = 'kraken';
		    	$coindata['coins']['kraken'][$value]['name'] = $coinList['Data'][$value]['CoinName'];
		    	$coindata['coins']['kraken'][$value]['fullname'] = $coinList['Data'][$value]['FullName'];
		    	$coindata['coins']['kraken'][$value]['owned'] = $v;
		
		}

	}





?>