<?php

$string = file_get_contents( "lib/data/portfolio.json", true );
$coins = json_decode( $string, true );

?>
<html>
	<head>
		<title>CRYPTOFOLIO</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0">
		<link href="https://fonts.googleapis.com/css?family=Roboto+Mono" rel="stylesheet">
		<link rel="stylesheet" type="text/css" href="lib/css/dygraphs.css">
		<link rel="stylesheet" type="text/css" href="lib/css/styles.css">
		<script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-sparklines/2.1.2/jquery.sparkline.min.js"></script>
		<script src="lib/js/dygraphs.min.js"></script>
		<script src="lib/js/jquery.color.min.js"></script>
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
			<td><span id="profit"></span><span id="profitpct"></span></td>
			<td><span id="currentvalue"></span></td>
		</tr>
	</table>

<div class="frame">
	<div id="coinform">
		<form action="portfolio.php" method="get">
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
		<table>
			<th colspan=2>coin</th>
			<th>price</th>
			<th>24hrs</th>
			<th>P/L</th>
			<?php
			foreach ($coins['coins'] as $symbol => $value) {
				echo "<tr id='". $symbol ."' class='item'>".PHP_EOL;
				echo "<td class='symbol'>".PHP_EOL;
				echo "<a href='portfolio.php?action=remove&symbol=".$symbol."' onclick=\"return confirm('Remove ".$symbol." \\nAre you sure?')\">".PHP_EOL;
				echo "<img class='imgholder' src='#' />".PHP_EOL;
				echo "</a></td>".PHP_EOL;
				echo "<td class='symbolname'><span class='symbolholder'>".$symbol."</span>".PHP_EOL;
				echo "<td class='currentprice'><span class='price'></span><br/>".PHP_EOL;
				echo "<td class='tdchange'><span class='change'></span></td>".PHP_EOL;
				echo "<td class='pl'>".PHP_EOL;
				echo "<span class='updown'></span>".PHP_EOL;
				echo "<span class='owned'>". $value['owned'] . "</span>".PHP_EOL;
				echo "<span class='paid'>". $value['paid'] . "</span>".PHP_EOL;
				echo "</td>".PHP_EOL;
				echo "</tr>".PHP_EOL;
			}
			?>
		</table>
	</div>
</div>
</body>
</html>
