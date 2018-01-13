<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'config.php';
$string = file_get_contents(platformSlashes($configfile), true);
$config = json_decode($string, true);

function act_cmp_function($a, $b) {
        if ($a['name'] > $b['name']) {
                return 1;
        } else if ($a['name'] < $b['name']) {
                return -1;
        } else {
                return 0;
        }
}
function platformSlashes($path) {
    return str_replace('/', DIRECTORY_SEPARATOR, $path);
}

function cache_image($image_url){
    //replace with your cache directory
    $image_path = 'lib/data/';
    //get the name of the file
    $exploded_image_url = explode("/",$image_url);
    $image_filename = end($exploded_image_url);
    $exploded_image_filename = explode(".",$image_filename);
    $extension = end($exploded_image_filename);

    //make sure its an image
    if($extension == "gif" || $extension == "jpg" || $extension == "jpeg" || $extension == "png") {
        //get the remote image
        if(!file_exists($image_path.$image_filename)){

            $image_to_fetch = file_get_contents($image_url);
            $local_image_file = fopen($image_path.$image_filename, 'w+');
            fwrite($local_image_file, $image_to_fetch);
            fclose($local_image_file);
            chmod($image_path.$image_filename,0777);

        }

       return $image_path.$image_filename;
    }
}


function currencyformat($currency)
{

    if ($currency == "EUR")
    {
        return "€ ";
    }
    if ($currency == "USD")
    {
        return "$";
    }

}

function symbol_lookup($symbol)
{
    $coinlist = file_get_contents(platformSlashes("lib/data/coinlist.json"));
    $json = json_decode($coinlist, true);
    $imgurl = "https://www.cryptocompare.com".$json['Data'][$symbol]['ImageUrl'];
    
    return cache_image($imgurl);
}

function price_lookup($symbol, $currency)
{

    $prices = file_get_contents("https://min-api.cryptocompare.com/data/pricehistorical?fsym=$symbol&tsyms=$currency");

}

function graph_data($symbol)
{

    $graph = file_get_contents("https://min-api.cryptocompare.com/data/histohour?fsym=$symbol&tsym=EUR&limit=24");
    $graphdata = json_decode($graph, true);

    foreach($graphdata['Data'] as $key => $value)
    {
        $out .= $value['close'].",";
    }
    return substr($out, 0, strlen($out) - 1);
}

function require_auth() {
    include 'config.php';
    $string = file_get_contents($configfile, true);
    $config = json_decode($string, true);
    if(isset($config['user']['username']) && isset($config['user']['password']) && $config['user']['username'] !=''){
        $AUTH_USER = $config['user']['username'];
        $AUTH_PASS = $config['user']['password'];
        header('Cache-Control: no-cache, must-revalidate, max-age=0');
        $has_supplied_credentials = !(empty($_SERVER['PHP_AUTH_USER']) && empty($_SERVER['PHP_AUTH_PW']));
        $is_not_authenticated = (
            !$has_supplied_credentials ||
            $_SERVER['PHP_AUTH_USER'] != $AUTH_USER ||
            $_SERVER['PHP_AUTH_PW']   != $AUTH_PASS
        );
        if ($is_not_authenticated) {
            header('HTTP/1.1 401 Authorization Required');
            header('WWW-Authenticate: Basic realm="Access denied"');
            exit;
     }
 }

}
?>