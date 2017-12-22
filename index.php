<?php 
require 'portfolio.php'; 
$ini_array = parse_ini_file("api.ini.php");
$investment = $ini_array['investment'];

?>
<html>
	<head>
		<title>CRYPTOFOLIO</title>
		<link rel="apple-touch-startup-image" href="lib/images/launch.png">
		<link rel="apple-touch-icon" href="lib/images/touch-icon-iphone.png" />
		<meta name="apple-mobile-web-app-title" content="Cryptofolio">
		<meta name="apple-mobile-web-app-capable" content="yes">
		<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0">
		<link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto+Mono" >
		<link rel="stylesheet" type="text/css" href="lib/css/styles.css">
		<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css">
		<link rel="stylesheet" type="text/css" href="lib/css/c3.min.css">
		<script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/d3/3.5.17/d3.min.js" charset="utf-8"></script>
		<script src="lib/js/c3.min.js"></script>
		<script src="lib/js/jquery.color.min.js"></script>
		<script src="lib/js/script.js"></script>
	</head>
<body>
<div id="header">	
	<h1>CRYPTOFOLIO</h1>
		<div id="euro"></div>
	<div class='menu'>
		<a href='#' id='addcoin'>
			<i class="fa fa-plus" aria-hidden="true"></i>
		</a>
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
					echo "<td class='pl'><span class='currentprice'>".$value['coinValue']."</span><br/>".PHP_EOL;
					echo "<span class='owned  dim'>". $value['coinsOwned'] . "</span></td>".PHP_EOL;
					echo "<td class='pl'>".PHP_EOL;
					echo "<span class='value'></span><br/>".PHP_EOL;
					echo "<span class='change'></span>".PHP_EOL;
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
<div id="coinform">
		<form action="editcoin.php" method="get">
			<h1>Add Coin</h1>
			<table>
				<tr>
					<td>Coin:</td>
					<td><input type='text' value='' name='symbol' placeholder='Symbol'/></td>
				</tr>
				<tr>
					<td>Total:</td>
					<td><input type='text' value='' name='total' placeholder='Total coins' /></td>
				</tr>
				<tr>
					<td>Wallet:</td>
					<td>
						<input placeholder='Wallet Name' name='walletname'>
					</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td><input type='submit' value='Add Coin' /><input type='button' value='Cancel' id="cancel" /></td>
				</tr>
			</table>
			<input type="hidden" name='action' value='addcoin'>
		</form>
	</div>
</body>
</html>
