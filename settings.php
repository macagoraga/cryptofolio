<?php 
include 'functions.php'; 
include 'lib/ccxt.php';
$investment = $config['investment']['amount'];
$username = $config['username'];
$password = $config['password'];

?>

<script type="text/javascript">

function loadapis(){
	$("#activeexchanges").html("").load("editapi.php?action=load");
}

$("#watch").click(function(){
	
	if($(this).prop('checked')===true ){
		$("#walletname,#total").prop("disabled",true)
		$("#walletname,#total").val('')
		
		$(this).val('watch')
	}
	if($(this).prop('checked')==false){
	$("#walletname,#total").prop("disabled",false)
	$("#walletname,#total").val('')
		$(this).val('')
	}
})

$("#savecoin").click(function(event) {
	event.preventDefault()
	if($("#coinlist").val()==""){
		console.log('no coin')
		alert('No coin selected.')
		return false;
	}
if($("#watch").val()=='watch'){
		$("#walletname").val('watch')
		$("#total").val(0)
		
	}
	
	if(isNaN($("#total").val()) || $("#total").val()==''  ){
	alert('Enter total coins.')
	return false
	
	}else{
		 $("#walletname,#total").prop("disabled",false)
		// var str = $("#editcoin").serialize();
		 $("#editcoin").submit()
	}


});
$("#saveapi").click(function(e){
	e.preventDefault()
	
	$.post( "editapi.php", $( "#apidetails" ).serialize() )
	.done(function( data ) {
 if(data=='ok'){
 	$(".cancel").trigger('click')
 	setTimeout(function(){ location.reload() },500);
 }
	});
})

function deleteApi(e){
    var r = confirm("Remove "+e+" from API's ?");
    if (r == true) {
        //console.log('editapi.php?action=removeapi&api='+e)
    	$.post('editapi.php',{ action : 'removeapi', exchange : e },function(){
    		loadapis()
    	})
    } else {
        console.log('cancelled')
    }
}

function addApi(){
	if($("#api").val() == '' || $("#secret").val() == '' || $("#exchange").val() == '' || $("#exchange").val() == 'Select'){
		alert('Missing Exchange name, API key or Secret')
	}
	else{

		var e = $("#exchange").val()
		var api = $("#secret").val()
		var secret = $("#api").val()
		console.log('posting:'+e+' api:'+api+ ' secret:'+ secret)
		$.post('editapi.php',{ action : 'addapi', exchange : e, api : api, secret : secret },function(){
			$("#api,#secret").val("")
			$("#exchange").val('Select');
	    	loadapis()
	    })
    }

}

loadapis();

</script>
<div class='coinform'>
 <form action="editcoin.php" method="get" id="editcoin">
<fieldset>
	<legend>Add Coin</legend>
	
	<label>Symbol</label>
	<select id='coinlist' name="symbol" placeholder="Select Coin">
		<option></option>
		<?php 

		$coinlist = file_get_contents(platformSlashes("lib/data/coinlist.json"));
	    $json = json_decode($coinlist, true);
	   
	    usort($json['Data'], "cmp");
		foreach ($json['Data'] as $key => $jsons) { // This will search in the 2 jsons
		    echo "<option value='".$jsons['Symbol']."'>".$jsons['FullName'] ."</option>";	
		}
	 
		?>	
	</select>
	
	<label>Total</label>
	<input type='text' value='' name='total' placeholder='0.01' id="total" />
	
	<label>Wallet Name</label>
	<input placeholder='Wallet Name' type='text' name='walletname' id="walletname"  />
	<label><input type="checkbox" id="watch" /> Watch only</label>
	<input type='button' value='Add' id='savecoin' /><input type='button' value='Cancel' class="cancel" /><input type='button' value='Settings' id="apiform" />
	<input type="hidden" name='action' value='addcoin'>
</fieldset>
</form>
<script>$("#coinlist").selectize({});</script>
</div>

<div class='apiform'>
<form action="editapi.php" method="get" id="apidetails">
<fieldset>
	<legend>Settings</legend>
	<h1>Investment</h1>
	<input type="text" name="investment" placeholder="1000.00" value="<?php echo $investment; ?>">
	
	<h1>Set username/password</h1>
	
	<label>Username</label>
	<input type="text" name="username" value="<?php echo $username; ?>">
	
	<label>Password</label>
	<input type="password" name="password" value="<?php echo $password; ?>">
	
	<h1>Exchange</h1>
	<select id='exchange' name='exchange'>
		<option value='Select'>Select Exchange</option>
		<?php 

		$exchanges = \ccxt\Exchange::$exchanges;
	   	foreach ($exchanges as $value) { // This will search in the 2 jsons
		    echo "<option value='".$value."'>".$value ."</option>";	
		}
	 
		?>	
	</select>
	
	<label>API KEY</label>
	<input type="text" name="" id="api">
	
	<label>SECRET</label>
	<input type="text" name="" id="secret"><br/>
	
	<input type="button" id="addapi" value="Add API" onclick="addApi()" /><br/>
	
	<h1>Active API's</h1>
	<div id="activeexchanges"></div>

	<br/>
	<hr/>
<input type='submit' value='Save' id="saveapi" /><input type='button' value='Cancel' class="cancel" /><input type='button' value='Add Coin' id="manualform" />
<input type="hidden" name="action" value="save">
</fieldset>
</form>
<script>$("#exchange").selectize({});</script>
</div>
