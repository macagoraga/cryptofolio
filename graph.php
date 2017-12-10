<?php
date_default_timezone_set('Europe/London');
	$limit = $_GET['limit'];
	$symbol = $_GET['symbol'];

	if($_GET['type']=='today'){
		
    	$graph = file_get_contents("https://min-api.cryptocompare.com/data/histominute?fsym=$symbol&tsym=EUR&limit=$limit");
    	$graphdata = json_decode($graph, true);
    
    }
    
    if($_GET['type']=='history'){
    
    	$graph = file_get_contents("https://min-api.cryptocompare.com/data/histoday?fsym=$symbol&tsym=EUR&limit=$limit");
    	$graphdata = json_decode($graph, true);
    
    }
    
    $grapharray = array();
    foreach($graphdata['Data'] as $key => $value)
    {
    	
    	$ts = date("Y-m-d H:i:s",$value['time']);

       $grapharray[] = array("TIMESTAMP"=>$ts, "OPEN"=>$value['open'], "CLOSE"=>$value['close'], "HIGH"=>$value['high'],"LOW"=>$value['low']);
    }
    echo json_encode($grapharray);
?>
 