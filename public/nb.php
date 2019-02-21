<?php

$access_token='<token>';
$s_url = 'https://masmadrid.nationbuilder.com/';

$s_url = $s_url.'api/v1/people/search?access_token='.$access_token.'&limit=1&custom_values%5Bhash_participa%5D=';



if(@$_GET['hash'] != ''){
        $hash = $_GET['hash'];
        if(strlen($hash) == 32){
                $ch = curl_init();
                //print_r($s_url.$hash);
                curl_setopt($ch, CURLOPT_URL, $s_url.$hash);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                $data = curl_exec($ch);
                curl_close($ch);
                //echo '<pre>';
                $result = json_decode($data, true);
                $result = $result['results'][0];
                print_r(json_encode($result));
                die();
        }
}

header('Location: /');
