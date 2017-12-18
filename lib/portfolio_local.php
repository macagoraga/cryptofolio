<?php
$string = file_get_contents("data/portfolio.json", true);
	$coinsmanual = json_decode($string, true);
	ksort($coinsmanual);

	foreach ($coinsmanual['coins'] as $symbol => $value){

		$coindata['coins']['manual'][$symbol]['wallettype'] = 'manual';
		$coindata['coins']['manual'][$symbol]['walletname'] = $coinsmanual['coins'][$symbol]['walletname'];
		$coindata['coins']['manual'][$symbol]['name'] = $coinList['Data'][$symbol]['CoinName'];
	    $coindata['coins']['manual'][$symbol]['fullname'] =  $coinsmanual['coins'][$symbol]['fullname'];
	    $coindata['coins']['manual'][$symbol]['owned'] = $coinsmanual['coins'][$symbol]['owned'];

	}

?>