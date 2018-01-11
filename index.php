<?php 
include 'functions.php';
include 'config.php';

$allcoins = file_get_contents(platformSlashes('lib/data/coinlist.json'));
$coinList = json_decode($allcoins, true);

$string = file_get_contents(platformSlashes($configfile), true);
$config = json_decode($string, true);


require_auth();

include 'portfolio.php'; 
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
		<link rel="stylesheet" type="text/css" href="lib/css/styles.css">
		<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css">
		<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.4/css/selectize.css">
		<script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/1.7.2/socket.io.js"></script>
 		<script src="https://cdnjs.cloudflare.com/ajax/libs/d3/4.12.2/d3.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/techan.js/0.8.0/techan.min.js"></script>	
		<script src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.4/js/standalone/selectize.min.js"></script>
 		<script src="lib/js/streamer_utilities.js"></script>
		<script src="lib/js/script.js"></script>


	</head>
<body>
<div id="euro"></div>

<div id="investment"><?php echo $investment; ?></div>
	<div class='header'> 	
		<div class='flex-item'>
			<h1>CRYPTOFOLIO</h1>
		</div>
	
			<div class='flex-item'>
				<div class='content'>
					<span id="totalValue"></span><br/>
					<small>Total Value</small>
				</div>
			</div>

			<div class='flex-item'>
				<div class='content'>
				<?php if($investment>0){ ?>
					<span id="plpct"></span><br/>
					<small>P/L</small>
					<?php } ?>
				</div>
			</div>


			<div class='flex-item'>
				<div class='content'>
					<span class='currency' id="btceuro"></span><br/>
					<span class='currency' id="etheuro"></span>
				</div>	
			</div>

			<div class='flex-item'>
				<div class='content'>
					<a href="javascript:void(0)" id="settings"><i class="fa fa-bars" aria-hidden="true"></i></a>
				</div>
			</div>
	</div>
	<div id="form"></div>
	
	<div id="table"> 
	<div class='filter'><a href='javascript:void(0)' class='selected'>ALL</a> | <a href='javascript:void(0)'>PORTFOLIO</a> | <a href='javascript:void(0)'>WATCH</a></div>
			<table>
			<tr>
				<th colspan=2 class='thcoin'>coin</th>
				<th class='thprice'>PRICE</th>
				<th class='thchange'>24HR</th>
				<th class='thpl'>VALUE (EUR)</th>
			</tr>
			

			<?php
			if(isset($coins) && sizeof($coins)>0){

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
					echo "<td class='pl'><span class='europrice'></span><span class='btcprice dim'>&nbsp;</span></td>".PHP_EOL;
					echo "<td class='thchange'><span class='change'></span></td>".PHP_EOL;
					echo "<td class='pl'>".PHP_EOL;
					if($value['walletname']=='watch'){

						echo "<span class='eurototal watch'></span><span class='btctotal watch'></span><span class='watchonly'><i class='fa fa-eye' aria-hidden='true'></i></span>".PHP_EOL;
						echo "<span class='owned watch'>". round($value['coinsOwned'],6) . "</span>".PHP_EOL;
					
					}else{
						echo "<span class='eurototal'></span><span class='btctotal'></span>".PHP_EOL;
						echo "<span class='owned  dim'>". round($value['coinsOwned'],6) . "</span>".PHP_EOL;
					
					}
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
<div id="chartcontainer">
	<h2><span id='chartsymbolname'></span> <span id='charttickereuro'></span> <span id='charttickerbtc'></span> <span id='charttickerchange'></h2>
	
	<div id="chart"></div>
	<div id="load"><i class="fa fa-circle-o-notch fa-spin fa-3x fa-fw"></i></div>
	<div id="error">Error loading data.</div>
	<nav>
		<a href='#' id="charthide">HIDE</a> | <a href='javascript:void(0)' id="1d" class='selected'>1D</a> | <a href='javascript:void(0)' id='7d'>7D</a> | <a href='javascript:void(0)' id='1m'>1M</a> | <a href='javascript:void(0)' id='3m'>3M</a><br/>
		
	</nav>
	<div id='feed'>
		<span><a href='javascript:void(0)' id='twitter' class='external'>TWITTER</a></span>
		<span><a href='javascript:void(0)' id='reddit' class='external'>REDDIT</a></span>
		<span><a href='https://www.binance.com' class='external' target="_blank">BINANCE</a></span>
		<span><a href='https://www.bittrex.com' class='external' target="_blank">BITTREX</a></span>
		<span><a href='https://www.poloniex.com' class='external' target="_blank">POLONIEX</a></span>

	</div>
</div>
</div>
<!-- <script>document.write('<script src="http://10.0.0.17:35729/livereload.js?snipver=1"></' + 'script>')</script>
 --></body>
</html>
