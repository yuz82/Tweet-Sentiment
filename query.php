<?php
header("Content-type: text/html; charset=utf-8"); 
define('CONSUMER_KEY', "Z9wLxCpBpXydUHRoNwAUlBdg5");
define('CONSUMER_SECRET', "lm5sSWpWKxEEYKl6MQwtCCAZgCIsfcLDyBbGA1YXlzILpBntxe");
//authentication
$x = base64_encode(rawurlencode(CONSUMER_KEY). ":".rawurlencode(CONSUMER_SECRET));
$url = "https://api.twitter.com/oauth2/token"; 
$opts = array(
'http'=>array(
'method'=>"POST", 'header'=>"Authorization:Basic $x".
"content_type:application/x-www-form-urlencoded;charset=UTF-8.",
'content'=>"grant_type=client_credentials" )
);
$context = stream_context_create($opts);
$output = file_get_contents($url,false,$context); 
$result = json_decode($output,true); 
$access_token = $result['access_token'];
$query = urlencode("guncontrol"); //include your query
//retrieve 5 most recent tweets
$search_url = "https://api.twitter.com/1.1/search/tweets.json?q=$query&result_type=recent&retweeted=false&lang=en&count=10"; 
$output = file_get_contents($search_url,false,stream_context_create(array(
	'http'=>array(
		'method'=>"GET",
		'header'=>"Authorization:Bearer $access_token"))));
$result = json_decode($output,true); //results returned as JSON
$result = $result['statuses'];
for ($i=0;$i<count($result);$i++) {
$id = $result[$i]['id_str']; //tweet id
$username = $result[$i]['user']['name']; //user posting tweet $text = $result[$i]['text']; //tweet text
$created_at = $result[$i]['created_at']; //tweet post time
$text = $result[$i]['text'];
echo $i."\t".$created_at->format()

//parse time
// $date = new DateTime($created_at);
// $date->setTimezone(new DateTimeZone('America/New_York'));
// $hour = $date->format('H');

$senurl = "https://community-sentiment.p.mashape.com/text/";
$sendata = array("txt"=>$text);
$header = array();
$header[] = 'X-Mashape-Key: 4Fm5eqeyvtmsh8I2ucnqoX9b202Zp1VfphHjsn7M5T5EfV3jcE';
$header[] ='Content-Type: application/x-www-form-urlencoded'; 
$header[] = 'Accept: application/json';
foreach($sendata as $key=>$value){ 
	$fields_string .= $key.'='.$value.'&'; 
} 
rtrim($fields_string, '&');
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $senurl); 
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); 
curl_setopt($curl, CURLOPT_HTTPHEADER, $header); 
curl_setopt($curl,CURLOPT_POST, count($sendata)); 
curl_setopt($curl,CURLOPT_POSTFIELDS, $fields_string); 
$sentimentdata = curl_exec($curl);
curl_close($curl);
$sentimentdata= json_decode($sentimentdata,true); 
$confidence = $sentimentdata['result']['confidence']; 
$sentiment = $sentimentdata['result']['sentiment'];
//echo $i."\t".$id."\t".$created_at."\t".$sentiment."<br/>";
//echo $i."\t".$created_at."\t".$username."\t".$sentiment."<br/>";
}

?>
