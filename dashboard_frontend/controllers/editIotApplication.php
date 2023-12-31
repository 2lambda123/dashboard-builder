<?php
/* Dashboard Builder.
  Copyright (C) 2018 DISIT Lab https://www.disit.org - University of Florence

  This program is free software: you can redistribute it and/or modify
   it under the terms of the GNU Affero General Public License as
   published by the Free Software Foundation, either version 3 of the
   License, or (at your option) any later version.
   This program is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU Affero General Public License for more details.
   You should have received a copy of the GNU Affero General Public License
   along with this program.  If not, see <http://www.gnu.org/licenses/>. */

require '../sso/autoload.php';

use Jumbojett\OpenIDConnectClient;

include '../config.php';
error_reporting(E_ERROR | E_NOTICE);
date_default_timezone_set('Europe/Rome');

session_start();

$response = [];

if(!isset($_REQUEST['id'])) {
  echo '{ "details":"Ko", "error":"missing id"}';
  exit;
}

if(!isset($_REQUEST['name'])) {
  echo '{ "details":"Ko", "error":"missing name"}';
  exit;
}

if (isset($_SESSION['refreshToken'])) {
  $oidc = new OpenIDConnectClient($ssoEndpoint, $ssoClientId, $ssoClientSecret);
  $oidc->providerConfigParam(array('token_endpoint' => $ssoTokenEndpoint));

  $tkn = $oidc->refreshToken($_SESSION['refreshToken']);

  $accessToken = $tkn->access_token;
  $_SESSION['refreshToken'] = $tkn->refresh_token;

  $id = urlencode($_REQUEST['id']);
  $name = $_REQUEST['name'];
  
  $json = http_get($ownershipApiBaseUrl."/v1/list/?type=AppID;DAAppID&elementId=$id&accessToken=" . $accessToken);
  if ($json['httpcode'] == 200 && count($json['result'])>0) {  
    $app=array();
    $app['elementId'] = $id;
    $app['elementName'] = $name;
    $app['elementType'] = $json['result'][0]['elementType'];

    $json = http_post($ownershipApiBaseUrl."/v1/register/?accessToken=" . $accessToken, json_encode($app), "application/json");
    if ($json['httpcode'] == 200) {
      $response['detail'] = 'Ok';
      $response['result'] = $json['result'];
    } else {
      $response['detail'] = 'Ko';
      $response['error'] = $json['result'];
    }
  }
} else {
  $response['detail'] = 'Ko';
  $response['error'] = 'no refresh token';
}

echo json_encode($response);

function http_post($url, $data, $mimetype) {
  $opts = array('http' =>
      array(
          'method'  => 'POST',
          'header'  => 'Content-type: '.$mimetype,
          'content' => $data,
          'ignore_errors' => true 
      )
  );
  
  # Create the context
  $context = stream_context_create($opts);
  # Get the response (you can use this for GET)
  $result = file_get_contents($url, false, $context);
  //echo "result:$result\n";
  //var_dump($http_response_header);
  return array("httpcode"=>explode(" ",$http_response_header[0])[1],"result"=>json_decode($result, true));
}
function http_get($url) {
  $opts = array('http' =>
      array(
          'method' => 'GET',
      )
  );

  # Create the context
  $context = stream_context_create($opts);
  # Get the response (you can use this for GET)
  $result = file_get_contents($url, false, $context);
  $json_result = json_decode($result, true);
  if($json_result===null || $json_result===false)
    $json_result = json_encode($result, true);
  //var_dump($http_response_header);
  return array("httpcode" => explode(" ", $http_response_header[0])[1], "result" => $json_result, "url"=>$url);
}
