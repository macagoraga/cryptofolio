<?php 
header('Content-type: application/csv');
header('Content-Disposition: attachment; filename=graph.csv');
header("Content-Transfer-Encoding: UTF-8");
$f = fopen('php://output', 'a');
date_default_timezone_set('Europe/London');
$limit = $_GET['limit'];
$symbol = $_GET['symbol'];
if($_GET['type']=='minutes'){
	
	$graph = file_get_contents("https://min-api.cryptocompare.com/data/histominute?fsym=$symbol&tsym=EUR&limit=$limit&aggregate=3&e=CCCAGG");
	$graphdata = json_decode($graph, true);

}
if($_GET['type']=='hours'){
    
    $graph = file_get_contents("https://min-api.cryptocompare.com/data/histohour?fsym=$symbol&tsym=EUR&limit=$limit&e=CCCAGG");
    $graphdata = json_decode($graph, true);

}
if($_GET['type']=='days'){

	$graph = file_get_contents("https://min-api.cryptocompare.com/data/histoday?fsym=$symbol&tsym=EUR&limit=$limit&e=CCCAGG");
	$graphdata = json_decode($graph, true);

}
$grapharray = array();
$fields = array('Date,Open,High,Low,Close,Volume');
fwrite($f, implode($fields, ',')."\n");
foreach($graphdata['Data'] as $key => $value)
{

    if($_GET['type'] =='days'){
        $ts = date("Y-m-d",$value['time']); 
    }
    if($_GET['type'] =='hours'){
        $ts = date("Y-m-d H:i:s",$value['time']); 
    }
    if($_GET['type'] =='minutes'){
        $ts = date("Y-m-d H:i:s",$value['time']); 
    }
    

    $open = $value['open'];
    $high = $value['high'];
    $low = $value['low'];
    $close = $value['close'];
    $volume = $value['volumeto'];

fputcsv($f, array("Date"=>$ts, "Open"=>$open, "High"=>$high, "Low"=>$low,"Close"=>$close, "Volume"=>$volume));
}
fclose($f);
?>