<?php

/* Dashboard Builder.
  Copyright (C) 2018 DISIT Lab https://www.disit.org - University of Florence

  This program is free software; you can redistribute it and/or
  modify it under the terms of the GNU General Public License
  as published by the Free Software Foundation; either version 2
  of the License, or (at your option) any later version.
  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.
  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA. */

include '../config.php';
require '../sso/autoload.php';
use Jumbojett\OpenIDConnectClient;

error_reporting(E_ERROR | E_NOTICE);
date_default_timezone_set('Europe/Rome');

session_start();
$link = mysqli_connect($host, $username, $password);
mysqli_select_db($link, $dbname);

$response = [];

if(isset($_SESSION['loggedUsername'])) 
{
    $dashboardId = mysqli_real_escape_string($link, $_REQUEST['dashboardId']);
    $dashboardTitle = mysqli_real_escape_string($link, $_REQUEST['dashboardTitle']);
    $newOwner = mysqli_real_escape_string($link, $_REQUEST['newOwner']);

    // Check if $newOwner exists as a valid LDAP user
    $ldapUsername = "cn=" . $newOwner . "," . $ldapBaseDN;

    $ds = ldap_connect($ldapServer, $ldapPort);
    ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
    $bind = ldap_bind($ds);

    if ($ds && $bind) {
        if (checkLdapMembership($ds, $ldapUsername, $ldapToolName, $ldapBaseDN)) {
            if (checkLdapRole($ds, $ldapUsername, "RootAdmin", $ldapBaseDN)) {
                $ldapRole = "RootAdmin";
                $ldapOk = true;
            }
            else
            {
                if (checkLdapRole($ds, $ldapUsername, "ToolAdmin", $ldapBaseDN)) {
                    $ldapRole = "ToolAdmin";
                    $ldapOk = true;
                } else {
                    if (checkLdapRole($ds, $ldapUsername, "AreaManager", $ldapBaseDN)) {
                        $ldapRole = "AreaManager";
                        $ldapOk = true;
                    } else {
                        if (checkLdapRole($ds, $ldapUsername, "Manager", $ldapBaseDN)) {
                            $ldapRole = "Manager";
                            $ldapOk = true;
                        } else {
                            if (checkLdapRole($ds, $ldapUsername, "Observer", $ldapBaseDN)) {
                                $ldapRole = "Observer";
                                $ldapOk = true;
                            }
                        }
                    }
                }
            }
        }
    }

    if(isset($_SESSION['refreshToken']) && $ldapOk)
    {
        $q = "UPDATE Dashboard.Config_dashboard SET user='$newOwner' WHERE Id = $dashboardId";
        $r = mysqli_query($link, $q);

        if($r) 
        {
            $oidc = new OpenIDConnectClient($ssoEndpoint, $ssoClientId, $ssoClientSecret);
            $oidc->providerConfigParam(array('token_endpoint' => $ssoTokenEndpoint));

            $tkn = $oidc->refreshToken($_SESSION['refreshToken']);
            $accessToken = $tkn->access_token;
            $_SESSION['refreshToken'] = $tkn->refresh_token;

            $callBody = ["elementId" => $_REQUEST['dashboardId'], "elementType" => "DashboardID", "username" => $newOwner, "elementName" => $dashboardTitle];

            $apiUrl = $ownershipApiBaseUrl . "/v1/register/?accessToken=" . $accessToken;

            $options = array(
                  'http' => array(
                          'header'  => "Content-type: application/json\r\n",
                          'method'  => 'POST',
                          'timeout' => 30,
                          'content' => json_encode($callBody),
                          'ignore_errors' => true
                  )
            );

            try
            {
                $context  = stream_context_create($options);
                $callResult = @file_get_contents($apiUrl, false, $context);

                if(strpos($http_response_header[0], '200') !== false) 
                {
                    $response['detail'] = 'Ok';
                    
                    /*$apiUrl = $personalDataApiBaseUrl . "/v1/username/" . $_SESSION['loggedUsername'] . "/delegator?accessToken=" . $accessToken . "&sourceRequest=dashboardmanager";
                        
                    $options = array(
                        'http' => array(
                                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                                'method'  => 'GET',
                                'timeout' => 30,
                                'ignore_errors' => true
                        )
                    );

                    $context  = stream_context_create($options);
                    $delegatedDashboardsJson = file_get_contents($apiUrl, false, $context);

                    $delegatedDashboards = json_decode($delegatedDashboardsJson);

                    for($i = 0; $i < count($delegatedDashboards); $i++) 
                    {
                        if($delegatedDashboards[$i]->elementId == $dashboardId)
                        {
                            //$newDelegation = ["delegationId" => $delegatedDashboards[$i]->id, "delegated" => $delegatedDashboards[$i]->usernameDelegated];
                            //array_push($delegated, $newDelegation);
                            
                            //1) Aggiungiamo nuova delega con nuovo proprietario come delegante
                            $delegatedDashboards[$i]->usernameDelegator = $newOwner;
                            $apiUrl = $personalDataApiBaseUrl . "/v1/username/" . $_SESSION['loggedUsername'] . "/delegation/" . $delegatedDashboards[$i]->id . "?accessToken=" . $accessToken . "&sourceRequest=dashboardmanager";

                            $options = array(
                                  'http' => array(
                                          'header'  => "Content-type: application/json\r\n",
                                          'method'  => 'PUT',
                                          'timeout' => 30,
                                          'content' => json_encode($delegatedDashboards[$i]),
                                          'ignore_errors' => true
                                  )
                            );

                            try
                            {
                                $context  = stream_context_create($options);
                                $callResult = file_get_contents($apiUrl, false, $context);

                                if(strpos($http_response_header[0], '200') !== false) 
                                {
                                    $response['detail'] = 'Ok';
                                }
                                else
                                {
                                    $response['detail'] = 'ApiCallKo0';
                                    $response['detail2'] = $http_response_header[0];
                                    $response['apiUrl'] = $apiUrl;
                                }
                            }
                            catch(Exception $ex) 
                            {
                                $response['detail'] = 'ApiCallKo0';
                            }
                        }
                    }*/
                }
                else
                {
                    $response['detail'] = 'ApiCallKo1';
                    $response['detail2'] = $http_response_header[0];
                }
            }
            catch (Exception $ex) 
            {
                $response['detail'] = 'ApiCallKo2';
                $response['detail2'] = $http_response_header[0];
            }
        }
        else
        {
            $response['detail'] = 'UpdateDbKo';
        }
    } else {
        $response['detail'] = 'checkUserKo';
    }
    
    echo json_encode($response);
}
  
