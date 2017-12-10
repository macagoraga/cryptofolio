<?php
	$limit = $_GET['limit'];
	$symbol = $_GET['symbol'];
    $graph = file_get_contents("https://min-api.cryptocompare.com/data/histoday?fsym=$symbol&tsym=EUR&limit=$limit");
    $graphdata = json_decode($graph, true);

    echo "Date,Price\n";
    foreach($graphdata['Data'] as $key => $value)
    {
    	
    	$ts = $value['time'];
    	$datetime = new DateTime("@$ts"); 
        echo $datetime->format('Ymd').",". $value['low'].";". $value['close'] .";" . $value['high']."\n" ;
    }

?>
 