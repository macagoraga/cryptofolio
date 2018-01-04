<div class='coinform'>
 <form action="editcoin.php" method="get">
<fieldset>
	<legend>Add Coin</legend>
	<label>Symbol
	<input type='text' value='' name='symbol' placeholder='BTC'/></label>
	<label>Total
	<input type='text' value='' name='total' placeholder='0.01' /></label>
	<label>Wallet Name
	<input placeholder='Wallet Name' type='text' name='walletname' placeholder='Mist'></label>
	<input type='submit' value='Add' /><input type='button' value='Cancel' class="cancel" /><input type='button' value='Settings' id="apiform" />
	<input type="hidden" name='action' value='addcoin'>
</fieldset>
</form>
</div>
<div class='apiform'>
<form action="editapi.php" method="get">
<fieldset>
	<legend>Settings</legend>
	<label>Amount
		<input type="text" name="investment" placeholder="1000.00">
	</label>

<h1>BitTrex API</h1>
	<label>API</label>
	<input type="text" name="bittrexapi">
	<label>Secret</label>
	<input type="text" name="bittrexsecret">
<h1>Binance API</h1>
	<label>API</label>
	<input type="text" name="binanceapi">
	<label>Secret</label>
	<input type="text" name="binancesecret">
<h1>Kraken API</h1>
	<label>API</label>
	<input type="text" name="krakenapi">
	<label>Secret</label>
	<input type="text" name="krakensecret">
<h1>Poloniex API</h1>
	<label>API</label>
	<input type="text" name="poloniexapi">
	<label>Secret</label>
	<input type="text" name="poloniexsecret"><br/>
<input type='submit' value='Save' /><input type='button' value='Cancel' class="cancel" /><input type='button' value='Add Coin' id="manualform" />
<input type="hidden" name="action" value="save">
</fieldset>
</form>
</div>
