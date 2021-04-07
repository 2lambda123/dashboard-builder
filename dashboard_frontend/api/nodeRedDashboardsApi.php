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

  include '../config.php';
  require '../sso/autoload.php';

  use Jumbojett\OpenIDConnectClient;
   
   /*$http_origin = $_SERVER['HTTP_ORIGIN'];

    if($http_origin == "https://iot-app.snap4city.org'" || $http_origin == "http://iot-app.snap4city.org'" || $http_origin == "http://127.0.0.1:1880")
    {  
        header("Access-Control-Allow-Origin: $http_origin");
    }*/
   
   //header('Access-Control-Allow-Origin: https://iot-app.snap4city.org');
   //header('Access-Control-Allow-Origin: http://127.0.0.1:1880');
   header('Access-Control-Allow-Origin: *');
   header('Access-Control-Allow-Headers: X-Requested-With, content-type, access-control-allow-origin, access-control-allow-methods, access-control-allow-headers');
   
   function returnManagedStringForDb($original)
    {
        if($original == NULL)
        {
            return "NULL";
        }
        else
        {
            return "'" . $original . "'";
        }
    }
    
    function returnManagedNumberForDb($original)
    {
        if($original == NULL)
        {
            return "NULL";
        }
        else
        {
            return $original;
        }
    }
    
   $link = mysqli_connect($host, $username, $password);
   mysqli_select_db($link, $dbname);
   error_reporting(E_ERROR | E_NOTICE);
    
   if(!$link->set_charset("utf8")) 
   {
      exit();
   } 
   
    $envFileContent = parse_ini_file("../conf/environment.ini");
    $activeEnv = $envFileContent["environment"]["value"];

    $response = [];
    
    if(isset($_REQUEST['accessToken'])) {
      $oidc = new OpenIDConnectClient();
      $oidc->providerConfigParam(array('token_endpoint' => $ssoTokenEndpoint));
      $oidc->providerConfigParam(array('userinfo_endpoint' => $ssoUserinfoEndpoint));
      
      $accessToken=$_REQUEST['accessToken'];
      
      $oidc->setAccessToken($accessToken);
      $uinfo = $oidc->requestUserInfo();
      if(isset($uinfo->error)) {
        header("HTTP/1.1 401 Unauthorized");
        echo json_encode($uinfo);
        exit;  
      }

      if(!isset($uinfo->username) && isset($uinfo->preferred_username))
        $uinfo->username = $uinfo->preferred_username;

      if(!isset($uinfo->username)) {
        header("HTTP/1.1 400 BAD REQUEST");
        echo '{"error":"No username found", "user":'.json_encode($uinfo).'}';
        exit;  
      }
      $tokenUsername = $uinfo->username;
      $ROLES = array('RootAdmin','ToolAdmin','AreaManager','Manager','Observer','Public');
      if(isset($uinfo->roles)) {
        foreach($ROLES as $r) {
          if(in_array($r, $uinfo->roles)) {
            $tokenUserRole = $r;
            break;
          }
        }
      }
      
    }
    
    if(isset($tokenUsername) || (isset($_REQUEST['secret'])&&isset($_REQUEST['username'])))
    {
        if($_REQUEST['secret'] === $nrApiSecret || isset($tokenUsername))
        {
            $username = mysqli_real_escape_string($link, isset($tokenUsername) ? $tokenUsername : $_REQUEST['username']);
            
            $filter = '';
            if(isset($tokenUserRole) && $tokenUserRole == "RootAdmin") {
              if(isset($_REQUEST['username']) && isset($_REQUEST['v3']))
                $filter = "user = '".mysqli_real_escape_string($link, $_REQUEST['username'])."' AND";
            } else
              $filter = "user = '$username' AND";

            $q1 = "SELECT title_header,id FROM Dashboard.Config_dashboard WHERE $filter deleted = 'no' order by title_header";
            $r1 = mysqli_query($link, $q1);
            
            if($r1)
            {
                while($row = mysqli_fetch_assoc($r1))
                {
                  if(isset($_REQUEST['v3']))
                    array_push($response, array("title"=>$row['title_header'],"id"=>$row['id']));
                  else if(isset($_REQUEST['v2']))
                    array_push($response, array("title"=>urlencode($row['title_header']),"id"=>$row['id']));
                  else
                    array_push($response, urlencode($row['title_header']));
                }
                
                echo json_encode($response);
            }
        }
    }
