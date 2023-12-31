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
error_reporting(E_ERROR | E_NOTICE);
date_default_timezone_set('Europe/Rome');

require '../sso/autoload.php';
use Jumbojett\OpenIDConnectClient;

session_start(); 
checkSession('Manager');

$link = mysqli_connect($host, $username, $password);
mysqli_select_db($link, $dbname);

$response = NULL;

if(isset($_REQUEST['dashboardIdToEdit'])&&isset($_REQUEST['currentDashboardTitle'])&&isset($_REQUEST['dashboardUser']))
{
    $dashboardId = escapeForSQL($_REQUEST['dashboardIdToEdit'], $link);
    if (checkVarType($dashboardId, "integer") === false) {
        eventLog("Returned the following ERROR in deleteWidget.php for dashboardId = ".$dashboardId.": ".$dashboardId." is not an integer as expected. Exit from script.");
        exit();
    };
    $username = $_REQUEST['dashboardUser'];
    $dashboardTitle = $_REQUEST['currentDashboardTitle'];	
}
else
{
    $dashboardId = escapeForSQL($_REQUEST['dashboardId'], $link);
    if (checkVarType($dashboardId, "integer") === false) {
        eventLog("Returned the following ERROR in deleteWidget.php for dashboardId = ".$dashboardId.": ".$dashboardId." is not an integer as expected. Exit from script.");
        exit();
    };
    $username = $_REQUEST['username'];
    $dashboardTitle = $_REQUEST['dashboardTitle'];
}

$widgetName = mysqli_real_escape_string($link, $_GET['nameWidget']);
if(!checkWidgetName($link, $widgetName)) {
    eventLog("Invalid request for deleteWidget.php $widgetName user: ".$_SESSION['loggedUsername']);
    exit;
}

try 
{
    $lastEditDateQuery = "UPDATE Dashboard.Config_dashboard SET last_edit_date = CURRENT_TIMESTAMP WHERE Id = '$dashboardId'";
    $lastEditDateResult = mysqli_query($link, $lastEditDateQuery);
} 
catch (Exception $ex) 
{
    $response = "updateDashboardEditTimeKo";
}

$widgetDataQuery = "SELECT * FROM Dashboard.Config_widget_dashboard WHERE name_w = '$widgetName' AND id_dashboard = '$dashboardId'";
$result0 = mysqli_query($link, $widgetDataQuery);

if($result0)
{
  $row0 = mysqli_fetch_assoc($result0);
  $generatorOriginalName = $row0['title_w'];
  $generatorOriginalType = $row0['id_metric'];
  $widgetType = $row0['type_w'];
  $entityJson = $row0['entityJson'];
}
else
{
  $generatorOriginalName = null;
  $generatorOriginalType = null;
}


if($entityJson != null)
{
     mysqli_autocommit($link, true);
     //Cancellazione entità attuatore su broker: la marchiamo come cancellata se non ci sono widget che la puntano
     $canceller = $username;
     $cancelDate = date('Y-m-d H:i:s');
     $entityObj = json_decode($entityJson);
     $entityObj->actuatorDeleted->value = true;
     $entityObj->actuatorDeletionDate->value = $cancelDate;
     $entityObj->actuatorCanceller->value = $canceller;
     $updatedEntityJson = json_encode($entityObj);
     $entityId = $entityObj->id;
     
    //Cancellazione widget
    $delWQ = "DELETE FROM Dashboard.Config_widget_dashboard WHERE name_w = '$widgetName' AND id_dashboard = '$dashboardId'";
    $delWR = mysqli_query($link, $delWQ);

    if($delWR) 
    {
        //Conteggio widget attuatori che puntano l'entità in esame
        $entityIdEsc = escapeForSQL($entityId, $link);
        $cntWQ = "SELECT * FROM Dashboard.Config_widget_dashboard WHERE actuatorTarget = 'broker' AND entityJson LIKE '%$entityIdEsc%'";
        $cntWR = mysqli_query($link, $cntWQ);
        
        if($cntWR)
        {
            if(mysqli_num_rows($cntWR) == 0)
            {
                // NEW !! Non fare piu delete su Orion Broker ma su IOT DIRECTORY !!

                //Se nessun widget punta più l'entità allora la marchiamo come cancellata
            /*    $entityUpdatedAttributes = ['actuatorDeleted' => ["value" => true, "type" => "Boolean"], 'actuatorDeletionDate' => ["value" => $cancelDate, "type" => "String"], 'actuatorCanceller' => ["value" => $username, "type" => "String"]];
                $entityUpdatedAttributesJson = json_encode($entityUpdatedAttributes);

                $orionDeleteEntityUrl = $orionBaseUrl. "/v2/entities/" . urlencode($entityId) . "/attrs";

                $orionCallOptions = array(
                    'http' => array(
                        'header'  => "Content-type: application/json\r\n",
                        'method'  => 'PATCH',
                        'content' => $entityUpdatedAttributesJson,
                        'timeout' => 30
                    )
                );

                try
                {
                   $context  = stream_context_create($orionCallOptions);
                   $callResult = file_get_contents($orionDeleteEntityUrl, false, $context);

                   if(strpos($http_response_header[0], '204 No Content') === false)
                   {
                        $response = "orionDeleteEntityKo";
                   }
                   else
                   {
                        mysqli_close($link);
                        $response = "Ok";
                   }
                }
                catch (Exception $ex) 
                {
                    $response = "orionDeleteEntityKo";
                }*/

                $oidc = new OpenIDConnectClient($ssoEndpoint, $ssoClientId, $ssoClientSecret);
                $oidc->providerConfigParam(array('token_endpoint' => $ssoTokenEndpoint));

                $tkn = $oidc->refreshToken($_SESSION['refreshToken']);
                $accessToken = $tkn->access_token;

                $deviceUrl = "http://www.disit.org/km4city/resource/iot/" . $_SESSION['orgBroker'] . "/" . $_SESSION['loggedOrganization'] . "/" . $widgetName;
                // . urlencode(json_encode($attributes)) . "&id=" . $id . "&type=" . $type . "&kind=" . $kind . "&contextbroker=" . $contextBroker . "&protocol=" . $protocol . "&format=" . $format . "&mac=&model=&producer=&latitude=43&longitude=11&visibility=private&frequency=1&nodered=yes&k1=" . $k1 . "&k2=" .$k2 . "&token=" . $accessToken . "&username=" . urlencode($_SESSION['loggedUsername']) . "&organization=" . $_SESSION['loggedOrganization']
                $iotDirApiUrl = $iotDirectoryBaseApiUrl . "device.php?action=delete&id=" . $widgetName . "&contextbroker=" . $_SESSION['orgBroker'] . "&uri=" . urlencode($deviceUrl) . "&username=" . urlencode($_SESSION['loggedUsername']) . "&organization=" . $_SESSION['loggedOrganization'] . "&dev_organization=" . $_SESSION['loggedOrganization'] . "&token=" . $accessToken;
                eventLog("Chiamata IOT Directory API per cancellazione widget: " . $iotDirApiUrl);
                try
                {
                    $iotDirPayload = file_get_contents($iotDirApiUrl);

                    //if(strpos($http_response_header[0], '201 Created') === false)
                    if(strpos($http_response_header[0], '200') === false)
                    {
                        $response = "orionDeleteEntityKo";
                    }
                    else
                    {
                        mysqli_close($link);
                        $response = "Ok";
                    }
                }
                catch (Exception $ex)
                {
                    $response = "orionDeleteEntityKo";
                }


            }
            else
            {
                mysqli_close($link);
                $response = "Ok";
            }
        }
        else
        {
            mysqli_close($link);
            $response = "cntRemainingWidgetsOnEntityKo";
        }
    }
    else
    {
        mysqli_close($link);
        $response = "delWidgetKo";
    }
}
else
{
     //Cancellazione widget dataviewer
     mysqli_autocommit($link, false);
     mysqli_begin_transaction(MYSQLI_TRANS_START_READ_WRITE); 

     $query1 = "DELETE FROM Dashboard.Config_widget_dashboard WHERE name_w = '$widgetName' AND id_dashboard = '$dashboardId'";
     $result1 = mysqli_query($link, $query1);

      if($result1) 
      {
          //Widget external content: cancellazione del widget eliminato fra gli widget target degli widget che, eventualmente, lo puntano per l'interazione cross widget
          if(strpos($widgetName, 'widgetExternalContent') !== false)
          {
             //Cancellazione dai target dei buttons della stessa dashboard
             $query2 = "SELECT * FROM Dashboard.Config_widget_dashboard WHERE name_w LIKE '%Button%' AND id_dashboard = '$dashboardId'";
             $result2 = mysqli_query($link, $query2);

             if($result2)
             {
                while($row2 = mysqli_fetch_array($result2)) 
                {
                   $targetList = json_decode($row2['parameters'], false);
                   $widgetId = $row2['Id'];

                   if(count($targetList) > 0)
                   {
                      $index = array_search($widgetName, $targetList);

                      if($index !== false)
                      {
                         array_splice($targetList, $index, 1);
                         $updatedTargetList = json_encode($targetList);
                         $query3 = "UPDATE Dashboard.Config_widget_dashboard SET parameters = '$updatedTargetList' WHERE Id = '$widgetId'";
                         $result3 = mysqli_query($link, $query3);

                         if(!$result3)
                         {
                            mysqli_rollback($link);
                            mysqli_close($link);
                            
                            $response = "updateWidgetPropertiesKo";
                         }
                      }
                   }
                }
             }
             else
             {
                mysqli_rollback($link);
                mysqli_close($link);
                
                $response = "delWidgetKo";
             }

             //Cancellazione dai target dei widget events della stessa dashboard
             $query4 = "SELECT * FROM Dashboard.Config_widget_dashboard WHERE name_w LIKE '%widgetEvents%' AND id_dashboard = '$dashboardId'";
             $result4 = mysqli_query($link, $query4);

             if($result4)
             {
                while($row4 = mysqli_fetch_array($result4)) 
                {
                   $targetList = json_decode($row4['parameters'], true);
                   $widgetId = $row4['Id'];

                   if(count($targetList) > 0)
                   {
                      unset($targetList[$widgetName]);
                      $updatedTargetList = json_encode($targetList);
                      $query5 = "UPDATE Dashboard.Config_widget_dashboard SET parameters = '$updatedTargetList' WHERE Id = '$widgetId'";
                      $result5 = mysqli_query($link, $query5);

                      if(!$result5)
                      {
                         mysqli_rollback($link);
                         mysqli_close($link);
                         
                         $response = "updateWidgetPropertiesKo";
                      }
                   }
                }
             }
             else
             {
                mysqli_rollback($link);
                mysqli_close($link);
                
                $response = "delWidgetEventProducersKo";
             }

             //Cancellazione dai target dei widget traffic events della stessa dashboard
             $query6 = "SELECT * FROM Dashboard.Config_widget_dashboard WHERE name_w LIKE '%widgetTrafficEvents%' AND id_dashboard = '$dashboardId'";
             $result6 = mysqli_query($link, $query6);

             if($result6)
             {
                while($row6 = mysqli_fetch_array($result6)) 
                {
                   $targetList = json_decode($row6['parameters'], true);
                   $widgetId = $row6['Id'];

                   if(count($targetList) > 0)
                   {
                      unset($targetList[$widgetName]);
                      $updatedTargetList = json_encode($targetList);

                      //Workaround per riportarlo nella forma giusta quando la lista dei parametri viene svuotata completamente
                      if($updatedTargetList == "[]")
                      {
                         $updatedTargetList = "{}";
                      }

                      $query7 = "UPDATE Dashboard.Config_widget_dashboard SET parameters = '$updatedTargetList' WHERE Id = '$widgetId'";
                      $result7 = mysqli_query($link, $query7);

                      if(!$result7)
                      {
                         $rollbackResult = mysqli_rollback($link);
                         mysqli_close($link);
                         
                         $response = "updateWidgetPropertiesKo";
                      }
                   }
                }
             }
             else
             {
                mysqli_rollback($link);
                mysqli_close($link);
                
                $response = "delWidgetEventProducersKo";
             }

             //Cancellazione dai target dei widget alarm events della stessa dashboard
             $query8 = "SELECT * FROM Dashboard.Config_widget_dashboard WHERE name_w LIKE '%widgetAlarms%' AND id_dashboard = '$dashboardId'";
             $result8 = mysqli_query($link, $query8);

             if($result8)
             {
                while($row8 = mysqli_fetch_array($result8)) 
                {
                   $targetList = json_decode($row8['parameters'], true);
                   $widgetId = $row8['Id'];

                   if(count($targetList) > 0)
                   {
                      unset($targetList[$widgetName]);
                      $updatedTargetList = json_encode($targetList);

                      //Workaround per riportarlo nella forma giusta quando la lista dei parametri viene svuotata completamente
                      if($updatedTargetList == "[]")
                      {
                         $updatedTargetList = "{}";
                      }

                      $query9 = "UPDATE Dashboard.Config_widget_dashboard SET parameters = '$updatedTargetList' WHERE Id = '$widgetId'";
                      $result9 = mysqli_query($link, $query9);

                      if(!$result9)
                      {
                         $rollbackResult = mysqli_rollback($link);
                         mysqli_close($link);
                         
                         $response = "delWidgetEventProducersKo";
                      }
                   }
                }
             }
             else
             {
                mysqli_rollback($link);
                mysqli_close($link);
                
                $response = "delWidgetEventProducersKo";
             }

             //Cancellazione dai target dei widget evacuation plans della stessa dashboard
             $query10 = "SELECT * FROM Dashboard.Config_widget_dashboard WHERE name_w LIKE '%widgetEvacuationPlans%' AND id_dashboard = '$dashboardId'";
             $result10 = mysqli_query($link, $query10);

             if($result10)
             {
                while($row10 = mysqli_fetch_array($result10)) 
                {
                   $targetList = json_decode($row10['parameters'], true);
                   $widgetId = $row10['Id'];

                   if(count($targetList) > 0)
                   {
                      unset($targetList[$widgetName]);
                      $updatedTargetList = json_encode($targetList);

                      //Workaround per riportarlo nella forma giusta quando la lista dei parametri viene svuotata completamente
                      if($updatedTargetList == "[]")
                      {
                         $updatedTargetList = "{}";
                      }

                      $query11 = "UPDATE Dashboard.Config_widget_dashboard SET parameters = '$updatedTargetList' WHERE Id = '$widgetId'";
                      $result11 = mysqli_query($link, $query11);

                      if(!$result11)
                      {
                         $rollbackResult = mysqli_rollback($link);
                         mysqli_close($link);
                         
                         $response = "updateWidgetPropertiesKo";
                      }
                   }
                }
             }
             else
             {
                mysqli_rollback($link);
                mysqli_close($link);
                
                $response = "delWidgetEventProducersKo";
             }

             //Cancellazione dai target dei widget network analysis della stessa dashboard
             $query12 = "SELECT * FROM Dashboard.Config_widget_dashboard WHERE name_w LIKE '%widgetNetworkAnalysis%' AND id_dashboard = '$dashboardId'";
             $result12 = mysqli_query($link, $query12);

             if($result12)
             {
                while($row12 = mysqli_fetch_array($result12)) 
                {
                   $targetList = json_decode($row12['parameters'], true);
                   $widgetId = $row12['Id'];

                   if(count($targetList) > 0)
                   {
                      $index = array_search($widgetName, $targetList);

                      if($index !== false)
                      {
                          array_splice($targetList, $index, 1);
                          $updatedTargetList = json_encode($targetList);

                          $query13 = "UPDATE Dashboard.Config_widget_dashboard SET parameters = '$updatedTargetList' WHERE Id = '$widgetId'";
                          $result13 = mysqli_query($link, $query13);

                          if(!$result13)
                          {
                             $rollbackResult = mysqli_rollback($link);
                             mysqli_close($link);
                             $response = "updateWidgetPropertiesKo";
                          }
                      }
                   }
                }
             }
             else
             {
                mysqli_rollback($link);
                mysqli_close($link);
                
                $response = "delWidgetEventProducersKo";
             }

             //Cancellazione dai target dei widget resources della stessa dashboard
             $query14 = "SELECT * FROM Dashboard.Config_widget_dashboard WHERE name_w LIKE '%widgetResources%' AND id_dashboard = '$dashboardId'";
             $result14 = mysqli_query($link, $query14);

             if($result14)
             {
                while($row14 = mysqli_fetch_array($result14)) 
                {
                   $targetList = json_decode($row14['parameters'], true);
                   $widgetId = $row14['Id'];

                   if(count($targetList) > 0)
                   {
                      $index = array_search($widgetName, $targetList);

                      if($index !== false)
                      {
                          array_splice($targetList, $index, 1);
                          $updatedTargetList = json_encode($targetList);

                          $query15 = "UPDATE Dashboard.Config_widget_dashboard SET parameters = '$updatedTargetList' WHERE Id = '$widgetId'";
                          $result15 = mysqli_query($link, $query15);

                          if(!$result15)
                          {
                             $rollbackResult = mysqli_rollback($link);
                             mysqli_close($link);
                             
                             $response = "updateWidgetPropertiesKo";
                          }
                      }
                   }
                }
             }
             else
             {
                mysqli_rollback($link);
                mysqli_close($link);
                
                $response = "delWidgetEventProducersKo";
             }


             //Cancellazione dai target dei widget GIS della stessa dashboard
             $query20 = "SELECT * FROM Dashboard.Config_widget_dashboard WHERE name_w LIKE '%widgetSelector%' AND id_dashboard = '$dashboardId'";
             $result20 = mysqli_query($link, $query20);

             if($result20)
             {
                while($row20 = mysqli_fetch_array($result20)) 
                {
                   $targetList = json_decode($row20['parameters'], true);
                   $widgetId = $row20['Id'];

                   if(count($targetList["targets"]) > 0)
                   {
                      $index = array_search($widgetName, $targetList["targets"]);

                      if($index !== false)
                      {
                          array_splice($targetList["targets"], $index, 1);
                          $updatedTargetList = json_encode($targetList);

                          $query21 = "UPDATE Dashboard.Config_widget_dashboard SET parameters = '$updatedTargetList' WHERE Id = '$widgetId'";
                          $result21 = mysqli_query($link, $query21);

                          if(!$result21)
                          {
                             $rollbackResult = mysqli_rollback($link);
                             mysqli_close($link);
                             
                             $response = "updateWidgetPropertiesKo";
                          }
                      }
                   }
                }

                mysqli_commit($link);
                mysqli_close($link);
                
                $response = "Ok";

                //"Cancellazione" (validità settata a 0) del generatore dal notificatore
                if(($generatorOriginalName != null)&&($generatorOriginalType != null))
                {
                   $url = $notificatorUrl;
                   $generatorOriginalName = preg_replace('/\s+/', '+', $generatorOriginalName);
                   $generatorOriginalType = preg_replace('/\s+/', '+', $generatorOriginalType);
                   $containerName = preg_replace('/\s+/', '+', $dashboardTitle);
                   $appUsr = preg_replace('/\s+/', '+', $username); 

                 //  $data = '?apiUsr=' . $notificatorApiUsr . '&apiPwd=' . $notificatorApiPwd . '&operation=deleteGenerator&appName=' . $notificatorAppName . '&appUsr=' . urlencode($appUsr) . '&generatorOriginalName=' . $generatorOriginalName . '&generatorOriginalType=' . $generatorOriginalType . '&containerName=' . urlencode($containerName);
                   $data = '?apiUsr=' . $notificatorApiUsr . '&apiPwd=' . $notificatorApiPwd . '&operation=deleteGenerator&appName=' . $notificatorAppName . '&appUsr=' . $appUsr . '&generatorOriginalName=' . $generatorOriginalName . '&generatorOriginalType=' . $generatorOriginalType . '&containerName=' . $containerName;
                   $url = $url.$data;

                   $options = array(
                       'http' => array(
                           'header'  => "Content-type: application/json\r\n",
                           'method'  => 'POST'
                           //'timeout' => 2
                       )
                   );

                   try
                   {
                      $context  = stream_context_create($options);
                      $callResult = @file_get_contents($url, false, $context);
                   }
                   catch (Exception $ex) 
                   {
                      //Non facciamo niente di specifico in caso di mancata risposta dell'host
                   }
                }
             }
             else
             {
                mysqli_rollback($link);
                mysqli_close($link);
                $response = "delWidgetEventProducersKo";
             }
          }//Tutti gli altri widget diversi dall'external content
          else
          {
             //Rimozione dai parametri di eventuali widget button di widget target puntati per cambio metrica 
             $query2 = "SELECT * FROM Dashboard.Config_widget_dashboard WHERE type_w = 'widgetButton' AND id_dashboard = '$dashboardId' AND parameters LIKE '%$widgetName%'";
             $result2 = mysqli_query($link, $query2); 

             if($result2)
             {
                if(mysqli_num_rows($result2) > 0)
                {
                  while($row2 = mysqli_fetch_array($result2)) 
                  {
                     $buttonWidgetId = $row2['Id']; 
                     $parameters = json_decode($row2['parameters'], false);
                     unset($parameters->changeMetricTargetsJson->$widgetName);
                     $updatedParameters = json_encode($parameters);

                     $query3 = "UPDATE Dashboard.Config_widget_dashboard SET parameters = '$updatedParameters' WHERE Id = '$buttonWidgetId'";
                     $result3 = mysqli_query($link, $query3);

                     if(!$result3)
                     {
                        mysqli_rollback($link);
                        mysqli_close($link);
                        $response = "updateWidgetPropertiesKo";
                     }
                  }  
                }
             }
             else
             {
                mysqli_rollback($link);
                mysqli_close($link);
                $response = "delWidgetEventProducersKo";
             }

             mysqli_commit($link);

             if(($widgetType == "widgetButton")&&(file_exists("../img/widgetButtonImages/" . $widgetName)))
             {
                array_map('unlink', glob("../img/widgetButtonImages/" . $widgetName . "/*.*"));
                rmdir("../img/widgetButtonImages/" . $widgetName);
             }

             //Widget selector: cancellazione della relativa cartella delle immagini
             if((strpos($widgetName, 'widgetSelector') !== false)&&(file_exists("../img/widgetSelectorImages/" . $widgetName)))
             {
                 $folderList = scandir("../img/widgetSelectorImages/" . $widgetName);
                 for($j = 0; $j < count($folderList); $j++)
                 {
                     array_map('unlink', glob("../img/widgetSelectorImages/" . $widgetName . "/q" . $j . "/*"));
                     rmdir("../img/widgetSelectorImages/" . $widgetName . "/q" . $j);
                 }

                 rmdir("../img/widgetSelectorImages/" . $widgetName);
                 //array_map('unlink', glob("../img/widgetSelectorImages/" . $widgetName . "/*.*"));
                 //rmdir("../img/widgetSelectorImages/" . $widgetName);
             }

             mysqli_close($link);
             $response = "Ok";

             //"Cancellazione" (validità settata a 0) del generatore dal notificatore
             if(($generatorOriginalName != null)&&($generatorOriginalType != null))
             {
                $url = $notificatorUrl;
                $generatorOriginalName = preg_replace('/\s+/', '+', $generatorOriginalName);
                $generatorOriginalType = preg_replace('/\s+/', '+', $generatorOriginalType);
                $containerName = preg_replace('/\s+/', '+', $dashboardTitle);
                $appUsr = preg_replace('/\s+/', '+', $username); 

              //  $data = '?apiUsr=' . $notificatorApiUsr . '&apiPwd=' . $notificatorApiPwd . '&operation=deleteGenerator&appName=' . $notificatorAppName . '&appUsr=' . urlencode($appUsr) . '&generatorOriginalName=' . $generatorOriginalName . '&generatorOriginalType=' . $generatorOriginalType . '&containerName=' . urlencode($containerName);
                $data = '?apiUsr=' . $notificatorApiUsr . '&apiPwd=' . $notificatorApiPwd . '&operation=deleteGenerator&appName=' . $notificatorAppName . '&appUsr=' . $appUsr . '&generatorOriginalName=' . $generatorOriginalName . '&generatorOriginalType=' . $generatorOriginalType . '&containerName=' . $containerName;
                $url = $url.$data;

                $options = array(
                    'http' => array(
                        'header'  => "Content-type: application/json\r\n",
                        'method'  => 'POST'
                        //'timeout' => 2
                    )
                );

                try
                {
                   $context  = stream_context_create($options);
                   $callResult = @file_get_contents($url, false, $context);
                }
                catch (Exception $ex) 
                {
                   //Non facciamo niente di specifico in caso di mancata risposta dell'host
                }
             }
          }
      } 
      else 
      {
          mysqli_rollback($link);
          mysqli_close($link);
          $response = "delWidgetKo";
      }

}

echo $response;

