<?php 

$url = "https://go.ourrevolution.com/page/event/search_results?orderby=day&state=MD&country=US&format=json";
#$message = "This is my IP address".$_SERVER['REMOTE_ADDR'];
$ch = curl_init();
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_URL, $url);
$result = curl_exec($ch);
curl_close($ch);
    
file_put_contents("ore_data.json", $result);   

