<?php
	// get api bittrex keys
	
	$apikeyBittrex=$ini_array['bittrex']['api'];
	$apisecretBittrex=$ini_array['bittrex']['secret'];

	if($apikeyBittrex!=''){
	 
	 	$nonce=time();
		$uri='https://bittrex.com/api/v1.1/account/getbalances?apikey='.$apikeyBittrex.'&nonce='.$nonce;
		$sign=hash_hmac('sha512',$uri,$apisecretBittrex);
		$ch = curl_init($uri);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('apisign:'.$sign));
		$execResult = curl_exec($ch);
		$bittrex = json_decode($execResult,true);


		foreach ($bittrex['result'] as $value) {

			if($value["Balance"]>0.009){
				$coindata['coins']['bittrex'][$value['Currency']]['wallettype'] = 'exchange';
				$coindata['coins']['bittrex'][$value['Currency']]['walletname'] = 'bittrex';
		    	$coindata['coins']['bittrex'][$value['Currency']]['name'] = $coinList['Data'][$value['Currency']]['CoinName'];
		    	$coindata['coins']['bittrex'][$value['Currency']]['fullname'] = $coinList['Data'][$value['Currency']]['FullName'];
		    	$coindata['coins']['bittrex'][$value['Currency']]['owned'] = $value["Balance"];
			}
		
		}
	
	}

?>