<?php 
	// load portfolio	
	$string = file_get_contents("lib/data/portfolio.json",true);
	$coins=json_decode($string,true);

	if(isset($_GET['symbol']) && isset($_GET['action']) && $_GET['action'] =='addcoin'){
		$getsymbol = strtoupper($_GET['symbol']);
		$gettotal = $_GET['total'];
		$getamount = $_GET['amount'];

		$coins['coins'][$getsymbol];
		$coins['coins'][$getsymbol]['currency']='EUR';
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
	$prices = file_get_contents("https://min-api.cryptocompare.com/data/pricemultifull?fsyms=$symbols&tsyms=EUR");
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

	function graph_data($symbol,$currency){
		//curl "https://min-api.cryptocompare.com/data/histoday?fsym=$symbol&tsym=$currency&limit=15"
		$graph = file_get_contents("https://min-api.cryptocompare.com/data/histoday?fsym=$symbol&tsym=$currency&limit=30");
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
		<link rel="stylesheet" type="text/css" href="lib/css/styles.css">
		<script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
		<script type="text/javascript">
		$(function(){
			$("#addcoin").click(function(){

				$("#coinform").toggle()
			})
			var sum = 0;
    	
	    	$(".paid").each(function() {
	        	sum += parseFloat($(this).text().replace("€", ""));
	    	});
	    	
	    	$("#total").text("Investment: € "+sum.toFixed(2))

	    	var profits = 0;
	    	$(".updown").each(function() {
	        	profits += parseFloat($(this).text().replace("€", ""));
	    	});

	    	$("#profit").html("Profit/Loss: <span class='profit'>€ "+parseFloat(profits).toFixed(2)+"</span>")
	    	$("#currentvalue").text("Current value: € "+parseFloat(sum+profits).toFixed(2))
	    	
	    	if(profits>0){

	    		$(".profit").removeClass().addClass("positive")
	    		}else{
	    		$(".profit").removeClass().addClass("negative")
    		
	    	}
	    	$(".updown:contains('-')").removeClass().addClass('negative')
	    	$(".updown").addClass('positive')
	    	$("#cancel").click(function(){
				$("#coinform").toggle()
	    	})

		})

	</script>
	</head>
<body>
	<div class='addcoin'><a href='#' id='addcoin'>+</a></div>
	<h1>CRYPTOFOLIO</h1>
	<h2><span id="total"></span><br/>
		<span id="profit"></span></br>
		<span id="currentvalue"></span></h2>

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
			<td>&nbsp;</td>
			<td><input type='submit' value='Add Coin' /><input type='button' value='Cancel' id="cancel" /></td>
		</tr>
	</table>
	<input type="hidden" name='action' value='addcoin'>
</form>
</div>
<table>
	<tr>
		<th>Coin</th>
		<th>P/L</th>
		<th>Current price</td> 
	</tr>
<?php
	foreach ($coins['coins'] as $symbol => $value){

		$currency = strtoupper($value['currency']);
		$price = round($priceArray['RAW'][$symbol][$currency]['PRICE'],2);
		$difference = round($price*$value['owned'],2);
		$profit = round($difference-$value['paid'],2);
		
		echo "<tr>";
		echo "<td class='symbol'><a href='index.php?action=remove&symbol=".$symbol."' onclick=\"return confirm('Remove ".$symbol." \\nAre you sure?')\">".symbol_lookup($symbol) . "</td>"; 
		echo "<td><span class='updown'>". currencyformat($currency).round($profit,2)."</span><br/>".$value['owned']."<br/><span class='paid'>".  currencyformat($currency).$value['paid'] . "</span></td>";
		echo "<td>".currencyformat($currency)." ".$price."</td>";
		echo "</tr>";
	}

?>
</table>

</div>
</body>
</html>


