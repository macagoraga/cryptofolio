<?php 
require 'portfolio.php'; 
$string = file_get_contents("config.json", true);
$config = json_decode($string, true);
$investment = $config['investment']['amount'];
?>
<html>
	<head>
		<title>CRYPTOFOLIO</title>
		<link rel="apple-touch-startup-image" href="lib/data/launch.png">
		<link rel="apple-touch-icon" href="lib/data/touch-icon-iphone.png" />
		<meta name="apple-mobile-web-app-title" content="Cryptofolio">
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0">
		<link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto+Mono" >
		<link rel="stylesheet" type="text/css" href="lib/css/styles.css">
		<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css">
		<link rel="stylesheet" type="text/css" href="lib/css/c3.min.css">
		<script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/1.7.2/socket.io.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/d3/3.5.17/d3.min.js" charset="utf-8"></script>
		<script src="lib/js/c3.min.js"></script>
		<script src="lib/js/streamer_utilities.js"></script>
		<script src="lib/js/script.js"></script>


	</head>
<body>
<div id="header">	
	<h1>CRYPTOFOLIO</h1>
		<div id="euro"></div>
	<div class='menu'>
		<button id='currency' class='active'>€</button>
		<button id='settings'>SETTINGS</button>
		
	</div>
		<?php if($investment>0){ ?>
		
			<table class='totals'> 
				<tr>
					<th>INVESTMENT</th>
					<th>TOTAL</th>
					<th>P/L</th>
				</tr>
				<tr>
					<td><span id="investment"><?php echo $investment; ?></span></td>
					<td><span id="totalValue"></span></td>
					<td><span id="plpct"></span></td>
					
				</tr>
			</table>
			
			<?php }	else{ ?>
			
			<table> 
				<tr>
					<td class='first' colspan="3"><span id="totalValue"></span></td>
					
				</tr>
			</table>

			<?php } ?>
			
</div>
<div class="frame">
	<div id="holder"> 
	
			<table>
			<tr>
				<th colspan=2 class='thcoin'>coin</th>
				<th class='thprice'>PRICE</th>
				<th class='thpl'>VALUE (EUR)</th>
			</tr>
			<tr class='ticker'>
				<td colspan='5'><span id="btceuro"></span> <span id="etheuro"></span> <span id="ltceuro"></span></td>
			</tr>

			<?php
			if(sizeof($coins)>0){

				foreach ($coins as $symbol => $value) {
				
					echo "<tr class='". $value['symbol'] ." item' data-coin='". $value['symbol'] ."' >".PHP_EOL;
					echo "<td class='symbol'>".PHP_EOL;
					if($value['wallettype']!='exchange'){
						echo "<a href='editcoin.php?action=remove&symbol=".$value['symbol']."' onclick=\"return confirm('Remove ".$value['symbol']." \\nAre you sure?')\">".PHP_EOL;
					}else{
						echo "<a href='#'>".PHP_EOL;
					}
					echo "<img class='imgholder' src='".$value['image']."' />".PHP_EOL;
					echo "</a></td>".PHP_EOL;
					echo "<td class='symbolname'><span class='symbolholdersmall'>".$value['symbol'];
					echo "</span><span class='symbolholder'>".$value['fullName']."</span>";
					echo "<span class='walletname dim' data-wallettype='" . $value['wallettype'] . "'>". $value['walletname'] ."</span>".PHP_EOL;
					echo "<td class='pl'><span class='europrice'>".$value['coinValue']."</span><span class='btcprice'>&nbsp;</span>".PHP_EOL;
					echo "<span class='change'></span></td>".PHP_EOL;
					echo "<td class='pl'>".PHP_EOL;
					echo "<span class='eurototal'></span><span class='btctotal'></span>".PHP_EOL;
					echo "<span class='owned  dim'>". round($value['coinsOwned'],6) . "</span>".PHP_EOL;
					echo "</td>".PHP_EOL;
					echo "</tr>".PHP_EOL;
				}
			}
			else{
				echo "<tr><td colspan='5'>No coins added.<td></tr>";
			}
			?>
		</table>
	</div>
</div>

	<div class='form'>
		<div class='coinform'>
	<h1>ADD COIN</h1>
		<form action="editcoin.php" method="get">
			<label>Symbol
			<input type='text' value='' name='symbol' placeholder='BTC'/></label>
			<label>Total
			<input type='text' value='' name='total' placeholder='0.01' /></label>
			<label>Wallet Name
			<input placeholder='Wallet Name' type='text' name='walletname' placeholder='Mist'></label>
			<input type='submit' value='Add' /><input type='button' value='Cancel' class="cancel" /><input type='button' value='Settings' id="apiform" />
			<input type="hidden" name='action' value='addcoin'>
		</form>
	</div>
	<div class='apiform'>
		<form action="editapi.php" method="get">
		<h1>Initial Investment</h1>
			<label>Amount
				<input type="text" name="investment" placeholder="1000.00">
			</label>

		<h1>BitTrex API</h1>
			<label>API
				<input type="text" name="bittrexapi">
			</label>
			<label>Secret
				<input type="text" name="bittrexsecret">
			</label>
		<h1>Binance API</h1>
			<label>API
				<input type="text" name="binanceapi">
			</label>
			<label>Secret
				<input type="text" name="binancesecret">
			</label>
		<h1>Kraken API</h1>
			<label>API
				<input type="text" name="krakenapi">
			</label>
			<label>Secret
				<input type="text" name="krakensecret">
			</label>
		<h1>Poloniex API</h1>
			<label>API
				<input type="text" name="poloniexapi">
			</label>
			<label>Secret
				<input type="text" name="poloniexsecret">
			</label>
		<input type='submit' value='Save' /><input type='button' value='Cancel' class="cancel" /><input type='button' value='Add Coin' id="manualform" />
		<input type="hidden" name="action" value="save">
		</form>

	</div>
	</div>
</div>
</body>
</html>
