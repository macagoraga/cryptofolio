<?php 
	// load portfolio	
	$string = file_get_contents("lib/data/portfolio.json",true);
	$coins=json_decode($string,true);

	if(isset($_GET['symbol']) && isset($_GET['action']) && $_GET['action'] =='addcoin'){
		$getsymbol = strtoupper($_GET['symbol']);
		$gettotal = $_GET['total'];
		$getamount = $_GET['amount'];
		$getcurrency = strtoupper($_GET['currency']);
		$coins['coins'][$getsymbol];
		$coins['coins'][$getsymbol]['currency']=$getcurrency;
		$coins['coins'][$getsymbol]['owned']=$gettotal;
		$coins['coins'][$getsymbol]['paid']=$getamount;

		$json_data = json_encode($coins);
		file_put_contents('lib/data/portfolio.json', $json_data);
		header('Location: index.php');
		exit;
	}

	if($_GET['action'] =='remove'){
		$getsymbol = strtoupper($_GET['symbol']);
		unset($coins['coins'][$getsymbol]);
		$json_data = json_encode($coins);
		file_put_contents('lib/data/portfolio.json', $json_data);
		header('Location: index.php');
		exit;
		//print_r($json_data);
	}

	$symbols = implode(",", array_keys($coins['coins']));
	$currencies = implode(",", array_values(array_column($coins['coins'], 'currency')));
	
	$prices = file_get_contents("https://min-api.cryptocompare.com/data/pricemultifull?fsyms=$symbols&tsyms=$currencies");
	$priceArray = json_decode($prices,true);


	function currencyformat($currency){

		if($currency=="EUR"){
			return "€ ";
		}
		if($currency=="USD"){
			return "$";
		}

	}

	function symbol_lookup($symbol){
		$coinlist = file_get_contents("lib/data/coinlist.json");
		$json = json_decode($coinlist,true);
		return "<img src='https://www.cryptocompare.com/". $json['Data'][$symbol]['ImageUrl'] ."?width=20' /><br/>". $json['Data'][$symbol]['Symbol'];
	}

	function price_lookup($symbol,$currency){

		$prices = file_get_contents("https://min-api.cryptocompare.com/data/pricehistorical?fsym=$symbol&tsyms=$currency&ts=$buyDate");
		$buyDate = strtotime($date);
	
	}

	function graph_data($symbol){
		//curl "https://min-api.cryptocompare.com/data/histoday?fsym=$symbol&tsym=$currency&limit=15"
		//https://min-api.cryptocompare.com/data/histohour?fsym=BTC&tsym=ETH&limit=30&aggregate=1&e=CCCAGG
		$graph = file_get_contents("https://min-api.cryptocompare.com/data/histohour?fsym=$symbol&tsym=EUR&limit=24");
		//$graph = file_get_contents("graph.json");

		$graphdata = json_decode($graph,true);
		//print_r($graphdata['Data']);
		foreach ($graphdata['Data'] as $key => $value) {
			$out .= $value['close'].",";
		}
		return substr($out,0,strlen($out)-1);
	}

?>
<html>
	<head>
		<title>CRYPTOFOLIO</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0">
		<link href="https://fonts.googleapis.com/css?family=Roboto+Mono" rel="stylesheet">
		<link rel="stylesheet" type="text/css" href="lib/css/styles.css">
		<script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-sparklines/2.1.2/jquery.sparkline.min.js"></script>
		<script src="lib/js/script.js"></script>
	</head>
<body>
	<div class='addcoin'><a href='#' id='addcoin'>+</a></div>
	<h1>CRYPTOFOLIO</h1>
	<table class='totals'>
		<tr>
			<th>INVESTMENT</th>
			<th>P/L</th>
			<th>TOTAL</th>
		</tr>			
		<tr class='noborder'>
			<td><span id="total"></span></td>
			<td><span id="profit"></span></td>
			<td><span id="currentvalue"></span></td>
		</tr>
	</table>

<div class="frame">
	<div id="coinform">
		<form action="index.php" method="get">
			<h1>Add Coin</h1>
			<table>
				<tr>
					<td>Coin:</td>
					<td><input type='text' value='' name='symbol' placeholder='Enter Symbol'/></td>
				</tr>
				<tr>
					<td>Total:</td>
					<td><input type='text' value='' name='total' placeholder='Number of coins' /></td>
				</tr>
				<tr>
					<td>Paid:</td>
					<td><input type='text' value='' name='amount' placeholder='xxx.xx' /></td>
				</tr>
				<tr>
					<td>Currency:</td>
					<td><input type='text' value='' name='currency' placeholder='EUR' /></td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td><input type='submit' value='Add Coin' /><input type='button' value='Cancel' id="cancel" /></td>
				</tr>
			</table>
			<input type="hidden" name='action' value='addcoin'>
		</form>
	</div>
	<div id="holder">
		<table id="portfolio">
			<tr>
				<th>Coin</th>
				<th>P/L</th>
				<th>Price</th> 
				<th>Last 24hrs</th> 
			</tr>
		<?php
			foreach ($coins['coins'] as $symbol => $value){

				$currency = strtoupper($value['currency']);
				$price = round($priceArray['RAW'][$symbol][$currency]['PRICE'],2);
				$change = round($priceArray['RAW'][$symbol][$currency]['CHANGEPCT24HOUR'],2);

				$difference = round($price*$value['owned'],2);
				$profit = round($difference-$value['paid'],2);
				
				echo "<tr>";
				echo "<td class='symbol'><a href='index.php?action=remove&symbol=".$symbol."' onclick=\"return confirm('Remove ".$symbol." \\nAre you sure?')\">".symbol_lookup($symbol) . "</td>"; 
				echo "<td class='pl'><span class='updown'>". currencyformat($currency).number_format($profit,2, '.', '')."</span><br/><span class='owned'>".number_format($value['owned'],8)."</span><br/><span class='paid'>".  currencyformat($currency).number_format($value['paid'],2, '.', '') . "</span></td>";
				echo "<td class='currentprice'>".currencyformat($currency)." ".number_format($price,2)."<br/><span class='change'>". number_format($change,2, '.', '')."%</span></td>";
				echo "<td class='graphtd'><span class='graph'>". graph_data($symbol)."</span></td>";
				echo "</tr>";
			}

		?>
		</table>
	</div>
</div>
</body>
</html>


