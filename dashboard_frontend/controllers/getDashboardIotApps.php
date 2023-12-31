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

error_reporting(E_ERROR | E_NOTICE);
date_default_timezone_set('Europe/Rome');

session_start();
checkSession('Manager');

$link = mysqli_connect($host, $username, $password);
mysqli_select_db($link, $dbname);

if(isset($_SESSION['loggedUsername'])) 
{
    $dashboardId = mysqli_real_escape_string($link, $_REQUEST['dashboardId']);

    if(!checkDashboardId($link, $dashboardId)) {
        eventLog("invalid request for getDashboardIotApps.php for dashboardId = $dashboardId user: ".$_SESSION['loggedUsername']);
        exit;
    }
    //$dashboardAuthor = mysqli_fetch_assoc($r)['user'];

    if(isset($_SESSION['refreshToken'])) 
    {
        $oidc = new OpenIDConnectClient($ssoEndpoint, $ssoClientId, $ssoClientSecret);
        $oidc->providerConfigParam(array('token_endpoint' => $ssoTokenEndpoint));

        $tkn = $oidc->refreshToken($_SESSION['refreshToken']);

        $accessToken = $tkn->access_token;
        $_SESSION['refreshToken'] = $tkn->refresh_token;

        $q1 = "SELECT distinct(NodeRedMetrics.appId) FROM NodeRedMetrics WHERE NodeRedMetrics.appId IS NOT NULL AND NodeRedMetrics.appId <> '' AND NodeRedMetrics.name IN(SELECT distinct(id_metric) FROM Dashboard.Config_widget_dashboard WHERE id_dashboard = '$dashboardId') " .
              "UNION " .
              "SELECT distinct(NodeRedInputs.appId) FROM NodeRedInputs WHERE NodeRedInputs.appId IS NOT NULL AND NodeRedInputs.appId <> '' AND NodeRedInputs.name IN(SELECT distinct(id_metric) FROM Dashboard.Config_widget_dashboard WHERE id_dashboard = '$dashboardId')";

        $r1 = mysqli_query($link, $q1);

        $appsFromQuery = 0;
        $appsFromOwnership = 0;

        if($r1)
        {
            $appsFromQuery = mysqli_num_rows($r1);
            $myFilteredApps = [];

            while($row = mysqli_fetch_assoc($r1))
            {
                $apiUrl = $ownershipApiBaseUrl . "/v1/list/?type=AppID&accessToken=" . $accessToken;

                $options = array(
                    'http' => array(
                            'header'  => "Content-type: application/json\r\n",
                            'method'  => 'GET',
                            'timeout' => 30,
                            'ignore_errors' => true
                    )
                );

                $context  = stream_context_create($options);
                $myAppsJson = file_get_contents($apiUrl, false, $context);
                $myApps = json_decode($myAppsJson);

                for($i = 0; $i < count($myApps); $i++) 
                {
                    if($myApps[$i]->elementId == $row['appId'])
                    {
                        $myFilteredApps[$row['appId']] = array('url'=>$myApps[$i]->elementUrl,'name'=>$myApps[$i]->elementName);
                        $appsFromOwnership++;
                    }
                } 
            }

            echo json_encode(["result" => "Ok", "appsFromQuery" => $appsFromQuery, "appsFromOwnership" => $appsFromOwnership, "applications" => $myFilteredApps, /*"sql" => $q1, "apiUrl" => $apiUrl,*/ "myApps" => $myApps]);
        }
        else
        {
            echo json_encode(["result" => "q1 KO"]);
        }
    }
    else
    {
        echo json_encode(["result" => "token KO"]);
    }
}
  
