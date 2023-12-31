<?php
    /* Dashboard Builder.
   Copyright (C) 2016 DISIT Lab https://www.disit.org - University of Florence

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
    if(isset($_REQUEST['action']) && !empty($_REQUEST['action'])) 
    {
        $link = mysqli_connect($host, $username, $password) or die("Failed to connect to server");
        $dbName = 'Dashboard';
        mysqli_set_charset($link, 'utf8');
        mysqli_select_db($link, $dbName);

        $action = addslashes($_REQUEST['action']);
        $jobName = mysqli_real_escape_string($link, $_REQUEST['jobName']);
        $schedulerName = mysqli_real_escape_string($link, $_REQUEST['scheduler']);

        $querySched = "SELECT * FROM Dashboard.Schedulers WHERE name = '$schedulerName'";
        $resultSched = mysqli_query($link, $querySched) or die(mysqli_error($link));
        $resultArraySched = array();

        if($resultSched->num_rows > 0) {
            if ($rowSched = mysqli_fetch_array($resultSched)) {
                $hostSched = $rowSched['ip'];
                $userSched = $rowSched['user'];
                $passSched = $rowSched['pass'];
            }
        }

        $dbName = 'quartz';
        $link = mysqli_connect($hostSched, $userSched, $passSched) or die("Failed to connect to server");
        mysqli_set_charset($link, 'utf8');
        mysqli_select_db($link, $dbName);

        header("Content-type: application/json");
        
        switch ($action)
        {
            case "getSingleStatus":
                
                $query = "SELECT * FROM QRTZ_STATUS WHERE JOB_NAME = '$jobName' ORDER BY DATE DESC LIMIT 1";
                $result = mysqli_query($link, $query) or die(mysqli_error($link));
                $resultArray = array();
                
                if($result->num_rows > 0) 
                {
                    while ($row = mysqli_fetch_array($result)) 
                    {
                        $record = array(
                            "status" => $row['STATUS'],
                            "date" => $row['DATE']
                        );
                        array_push($resultArray, $record);
                    }
                }
                else 
                {
                    $record = array(
                            "status" => "none",
                            "date" => "none"
                        );
                    array_push($resultArray, $record);
                }
                mysqli_close($link);
                echo json_encode($resultArray);
                break;    
            
            default:
                echo 'Action ' . $action . 'is not valid';
                break;
        }
    }