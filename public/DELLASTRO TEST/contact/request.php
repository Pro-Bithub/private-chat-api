<?php

function application_api_call($url, $server, $method = 'GET', $data = null)
{


    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $server['hostname'] . $url);
    curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($curl, CURLOPT_USERPWD, $server['username'] . ':' . $server['password']);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
    if ($data != null) {
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    }
	$userAgent = $_SERVER['HTTP_USER_AGENT'];
curl_setopt($curl, CURLOPT_USERAGENT, $userAgent);

    $result = curl_exec($curl);
    $info = curl_getinfo($curl);
    curl_close($curl);
    return ['status_code' => $info['http_code'], 'data' => json_decode($result, 1)];
}


$domainwithport = $_POST["api_url"];
if (strpos($domainwithport, 'http://') !== false || strpos($domainwithport, 'https://') !== false) {
	$hostname = $domainwithport;
} else {
	$hostname = 'http://' . $domainwithport;
}
if (substr($hostname, -1) === '/') {
	$hostname = substr($hostname, 0, -1); 
}
$application_server = array(
	'hostname' => $hostname,
	'username' => "examplhot.com",
	'password' => "examplhot.com"
);

$profile_id=null;
if (isset($_COOKIE['myData'])) {
    $myData = $_COOKIE['myData'];
	if (property_exists($myDataObject, 'contact')) {
		$profile_id = $myDataObject->contact;

    }
} 


$data=array(
	'firstname' => $_POST["firstname"],
	'lastname' => $_POST["lastname"],
	'mail' => $_POST["mail"],
	'object' => $_POST["object"],
	'details' => $_POST["details"],
	'ip_address' => $_SERVER['REMOTE_ADDR'],
	'account_id'=> $_POST["account_id"],
	'profile_id'=> $profile_id,
	'source' => $_SERVER['HTTP_HOST'],
);

$check=	application_api_call('/support/new/ticket', $application_server, 'POST', $data);


$html = array();
if($check["status_code"]==200){
	$html = $check;
	if($check['data']['success']==true)
	$html['code'] = true;
		else 
		$html['code'] = false;
		}
else {
	
	$html['code'] = false;
}

echo json_encode($html);



?>