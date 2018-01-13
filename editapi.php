<?php

include 'functions.php';


// if (empty($_SERVER["HTTP_X_REQUESTED_WITH"]) && $_SERVER["HTTP_X_REQUESTED_WITH"] != "XMLHttpRequest") {
//     if (realpath($_SERVER["SCRIPT_FILENAME"]) == __FILE__) { // direct access denied
//          header('HTTP/1.0 403 Forbidden');
//         exit('Forbidden');
       
//     }
// }


if ($_GET['action'] == 'load'){
    
    $string = file_get_contents(platformSlashes($configfile), true);
    $api = json_decode($string, true); 

    if(sizeof($api['api'])>0){
        echo "<ul class='exchange'>";
    foreach ($api['api'] as $key => $value) {
       echo '<li onclick="deleteApi(\''.$key.'\')"><i class="fa fa-minus-circle" aria-hidden="true"></i>'.$key.'</li>';
    }
     echo "</ul>";
    }
    else{
        echo "-";
    }

}
if ($_POST['action'] == 'addapi'){

    $exchange = $_POST['exchange'];
    $api =  $_POST['api'];
    $secret =  $_POST['secret'];
    
    $string = file_get_contents(platformSlashes($configfile), true);
    $localdata = json_decode($string, true); 
    
    $localdata['api'][$exchange]['api'] = $api ;
    $localdata['api'][$exchange]['secret'] = $secret ;

    $json_data = json_encode($localdata,true);
    file_put_contents(platformSlashes($configfile), $json_data);
}


if ($_POST['action'] == 'removeapi'){
    
    $exchange = $_POST['exchange'];

    $string = file_get_contents(platformSlashes($configfile), true);
    $localdata = json_decode($string, true); 
    
    unset($localdata['api'][$exchange]);

    $json_data = json_encode($localdata,true);
    file_put_contents(platformSlashes($configfile), $json_data);
}

if (isset($_POST['action']) && $_POST['action'] == 'save'){
   
    $string = file_get_contents(platformSlashes($configfile), true);
    $localdata = json_decode($string, true); 

   
    unset($localdata['investment']);
    unset($localdata['user']);
    $localdata['user']['username']=trim($_POST['username']);
    $localdata['user']['password']=trim($_POST['password']);
    $localdata['investment']['amount']=trim($_POST['investment']);
    
    $json_data = json_encode($localdata,true);
    
    file_put_contents(platformSlashes($configfile), $json_data);

    echo "ok";
    exit;
}




?>