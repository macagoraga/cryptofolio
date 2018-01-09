<?php
if (empty($_SERVER["HTTP_X_REQUESTED_WITH"]) && $_SERVER["HTTP_X_REQUESTED_WITH"] != "XMLHttpRequest") {
    if (realpath($_SERVER["SCRIPT_FILENAME"]) == __FILE__) { // direct access denied
         header('HTTP/1.0 403 Forbidden');
        exit('Forbidden');
       
    }
}

        include 'config.php';
        if ($_POST['action'] == 'load'){
            

            $string = file_get_contents($configfile, true);

            $localdata = json_decode($string, true); 
            $api = json_encode($localdata);
           print_r($api);
        }


        if (isset($_POST['action']) && $_POST['action'] == 'save'){
           
            $string = file_get_contents($configfile, true);
            $localdata = json_decode($string, true); 

            unset($localdata['api']);
            unset($localdata['investment']);
            unset($localdata['user']);
            $localdata['user']['username']=trim($_POST['username']);
            $localdata['user']['password']=trim($_POST['password']);
            
            $localdata['api']['bittrex']['api']=trim($_POST['bittrexapi']);
            $localdata['api']['bittrex']['secret']=trim($_POST['bittrexsecret']);
            $localdata['api']['kraken']['api']=trim($_POST['krakenapi']);
            $localdata['api']['kraken']['secret']=trim($_POST['krakensecret']);
            $localdata['api']['poloniex']['api']=trim($_POST['poloniexapi']);
            $localdata['api']['poloniex']['secret']=trim($_POST['poloniexsecret']);
            $localdata['api']['binance']['api']=trim($_POST['binanceapi']);
            $localdata['api']['binance']['secret']=trim($_POST['binancesecret']);
            $localdata['api']['kucoin']['api']=trim($_POST['kucoinapi']);
            $localdata['api']['kucoin']['secret']=trim($_POST['kucoinsecret']);

            $localdata['investment']['amount']=trim($_POST['investment']);
            
            $json_data = json_encode($localdata,true);
            
            file_put_contents($configfile, $json_data);
           // header('Location: index.php');
            echo "ok";
            exit;
    }


?>