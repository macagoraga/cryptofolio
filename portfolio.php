<?php
include platformSlashes("lib/ccxt.php");

if(isset($config['coins']) && sizeof($config['coins'])>0){

	foreach ($config['coins'] as $symbol => $value){

		$coindata['coins']['manual'][$symbol]['wallettype'] = 'manual';
		$coindata['coins']['manual'][$symbol]['walletname'] = $config['coins'][$symbol]['walletname'];
		$coindata['coins']['manual'][$symbol]['name'] = $coinList['Data'][$symbol]['CoinName'];
	    $coindata['coins']['manual'][$symbol]['fullname'] =  $config['coins'][$symbol]['fullname'];
	    $coindata['coins']['manual'][$symbol]['owned'] = $config['coins'][$symbol]['owned'];

	}
}

if(isset($config['api']) && sizeof($config['api'])>0){
	foreach ($config['api'] as $key => $value) {

		if($value['secret']!=''){
		
			$c = '\ccxt\\'.$key;
			try {
			$out = new $c ( array ( 'apiKey' => $config['api'][$key]['api'], 'secret' => $config['api'][$key]['secret'] )); 
			
				foreach ($out->fetch_balance()['total'] as $k => $v) {
					
					if($v>0){

						$coindata['coins'][$key][$k]['wallettype'] = 'exchange';
						$coindata['coins'][$key][$k]['walletname'] = $key;
						
						if( isset($coinList['Data'][$k]['CoinName']) ){
				    		$coindata['coins'][$key][$k]['name'] = $coinList['Data'][$k]['CoinName'];
				    		$coindata['coins'][$key][$k]['fullname'] = $coinList['Data'][$k]['FullName'];
				    	}
				    	else{
							$coindata['coins'][$key][$k]['name'] = $k;
				    		$coindata['coins'][$key][$k]['fullname'] = $k.' - Unknown coin';
				    	

				    	}
				    	$coindata['coins'][$key][$k]['owned'] = $v;
			    	}	
				
				}
			}
			catch (Exception $e) {
    		echo "<div style='width:100%;padding:4px;background-color: #900;color: #fff; font-size:11px'>Error: ".  $e->getMessage () ."</div>";
		
			}
		}
	}
}



if(isset($coindata) && sizeof($coindata)>0){
foreach ($coindata['coins'] as $key => $val) {
    
    foreach ($val as $i => $v) {
    
	    $output[] = array("symbol"=>$i, "name"=>$v['name'], "wallettype"=>$v['wallettype'], "walletname"=>$v['walletname'], "coinsOwned"=>$v['owned'], "fullName"=>$v['fullname'], "image"=>symbol_lookup($i));
	     $coins = $output;
	    }
    

}

    uasort($coins, 'act_cmp_function');

}


?>
