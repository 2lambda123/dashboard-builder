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
   include('../config.php');
   header("Cache-Control: private, max-age=$cacheControlMaxAge");
?>

<link rel="stylesheet" href="../css/widgetImpulseButton.css">

<script type='text/javascript'>

  $(document).ready(function <?= $_REQUEST['name_w'] ?>(firstLoad, metricNameFromDriver, widgetTitleFromDriver, widgetHeaderColorFromDriver, widgetHeaderFontColorFromDriver, fromGisExternalContent, fromGisExternalContentServiceUri, fromGisExternalContentField, fromGisExternalContentRange, /*randomSingleGeoJsonIndex,*/ fromGisMarker, fromGisMapRef, fromGisFakeId)  
    {
        <?php
            $titlePatterns = array();
            $titlePatterns[0] = '/_/';
            $titlePatterns[1] = '/\'/';
            $replacements = array();
            $replacements[0] = ' ';
            $replacements[1] = '&apos;';
            $title = $_REQUEST['title_w'];

            $link = mysqli_connect($host, $username, $password);
            if (checkWidgetNameInDashboard($link, $_REQUEST['name_w'], $_REQUEST['id_dashboard']) === false) {
                eventLog("Returned the following ERROR in widgetImpulseButton.php for the widget ".escapeForHTML($_REQUEST['name_w'])." is not instantiated or allowed in this dashboard.");
                exit();
            }

            $genFileContent = parse_ini_file("../conf/environment.ini");
            $wsServerContent = parse_ini_file("../conf/webSocketServer.ini");
            $env = $genFileContent['environment']['value'];
            $wsServerAddress = $wsServerContent["wsServerAddressWidgets"][$env];
            $wsServerPort = $wsServerContent["wsServerPort"][$env];
            $wsPath = $wsServerContent["wsServerPath"][$env];
            $wsProtocol = $wsServerContent["wsServerProtocol"][$env];
            $wsRetryActive = $wsServerContent["wsServerRetryActive"][$env];
            $wsRetryTime = $wsServerContent["wsServerRetryTime"][$env];
            $useActuatorWS = $wsServerContent["wsServerActuator"][$env]; ?>
                
        var headerHeight = 25;
        var hostFile = "<?= escapeForJS($_REQUEST['hostFile']) ?>";
        var widgetName = "<?= $_REQUEST['name_w'] ?>";
        var divContainer = $("#<?= $_REQUEST['name_w'] ?>_content");
        var widgetContentColor = "<?= escapeForJS($_REQUEST['color_w']) ?>";
        var nome_wid = "<?= $_REQUEST['name_w'] ?>_div";
        var linkElement = $('#<?= $_REQUEST['name_w'] ?>_link_w');
        var color = '<?= escapeForJS($_REQUEST['color_w']) ?>';
        var fontSize = "<?= escapeForJS($_REQUEST['fontSize']) ?>";
        var fontColor = "<?= escapeForJS($_REQUEST['fontColor']) ?>";
        var embedWidget = <?= $_REQUEST['embedWidget']=='true'?'true':'false' ?>;
        var embedWidgetPolicy = '<?= escapeForJS($_REQUEST['embedWidgetPolicy']) ?>';
        var showTitle = "<?= escapeForJS($_REQUEST['showTitle']) ?>";
        var hasTimer = "<?= escapeForJS($_REQUEST['hasTimer']) ?>";
        var widgetProperties, styleParameters, metricType, metricName, widgetParameters, nrInputId,
            sizeRowsWidget, widgetTitle, widgetHeaderColor, 
            widgetHeaderFontColor, showHeader, minDim, minDimCells, minDimName, offset, dashboardId,
            widgetWidthCells, widgetHeightCells,
            entityJson, attributeName, updateMsgFontSize, setUpdatingMsgIndex, setUpdatingMsgInterval, 
            dataType, displayColor, currentValue, fontFamily, targetCurrentStatus, oldValue,
            onOffButtonPercentWidth, onOffButtonPercentHeight, onOffButtonRadius, buttonClickColor, textOnNeonEffect, textOffNeonEffect,
            buttonColor, offValue, impulseValue, updateRequestStartTime, symbolColor, symbolClickColor, textClickColor, textColor,
            symbolOnNeonEffect, symbolOffNeonEffect, symbolOnNeonEffectSetting, viewMode, textFontSize, displayFontSize,
            displayFontColor, displayFontClickColor, displayRadius, displayColor, displayWidth, displayHeight, displayOffNeonEffect, 
            displayOnNeonEffect, impulseMode, targetEntity, targetEntityAttribute, baseValue, sequenceEntityUpdateInterval,
            actuatorTarget, username, endPointHost, endPointPort, nodeRedInputName = null;
        var useWebSocket = <?= $useActuatorWS ?>;
        var code = null;
        if(Window.webSockets == undefined)
          Window.webSockets = {};

        console.log("<?= $_REQUEST['name_w'] ?>");

        if(((embedWidget === true)&&(embedWidgetPolicy === 'auto'))||((embedWidget === true)&&(embedWidgetPolicy === 'manual')&&(showTitle === "no"))||((embedWidget === false)&&(showTitle === "no")))
        {
            showHeader = false;
        }
        else
        {
            showHeader = true;
        } 
            
        if((metricNameFromDriver === "undefined")||(metricNameFromDriver === undefined)||(metricNameFromDriver === "null")||(metricNameFromDriver === null))
        {
            metricName = "<?= escapeForJS($_REQUEST['id_metric']) ?>";
            widgetTitle = "<?= sanitizeTitle($_REQUEST['title_w']) ?>";
            widgetHeaderColor = "<?= escapeForJS($_REQUEST['frame_color_w']) ?>";
            widgetHeaderFontColor = "<?= escapeForJS($_REQUEST['headerFontColor']) ?>"; 
        }
        else
        {
            metricName = metricNameFromDriver;
            widgetTitleFromDriver.replace(/_/g, " ");
            widgetTitleFromDriver.replace(/\'/g, "&apos;");
            widgetTitle = widgetTitleFromDriver;
            $("#" + widgetName).css("border-color", widgetHeaderColorFromDriver);
            widgetHeaderColor = widgetHeaderColorFromDriver;
            widgetHeaderFontColor = widgetHeaderFontColorFromDriver;
        }
        
        var elToEmpty = $("#<?= $_REQUEST['name_w'] ?>_chartContainer");
        elToEmpty.css("font-family", "Verdana");
        var url = "<?= escapeForJS($_REQUEST['link_w']) ?>";
        $('#<?= $_REQUEST['name_w'] ?>_countdownDiv').hide();
        
        //Definizioni di funzione specifiche del widget
        
        //Restituisce il JSON delle info se presente, altrimenti NULL
        function getInfoJson()
        {
            var infoJson = null;
            if(jQuery.parseJSON(widgetProperties.param.infoJson !== null))
            {
                infoJson = jQuery.parseJSON(widgetProperties.param.infoJson); 
            }
            
            return infoJson;
        }
        
        //Restituisce il JSON delle info se presente, altrimenti NULL
        function getStyleParameters() {
            var styleParameters = null;
            if(jQuery.parseJSON(widgetProperties.param.styleParameters !== null))
            {
                styleParameters = jQuery.parseJSON(widgetProperties.param.styleParameters); 
            }
            
            return styleParameters;
        }
        
        function populateWidget() {
            showWidgetContent(widgetName);
            $('#<?= $_REQUEST['name_w'] ?>_noDataAlert').hide();
            $("#<?= $_REQUEST['name_w'] ?>_loadErrorAlert").hide();
            $("#<?= $_REQUEST['name_w'] ?>_chartContainer").show();
            
            if($("#<?= $_REQUEST['name_w'] ?>_chartContainer").width() > $("#<?= $_REQUEST['name_w'] ?>_chartContainer").height())
            {
                minDim = $("#<?= $_REQUEST['name_w'] ?>_chartContainer").height();
                minDimCells = widgetHeightCells;
                minDimName = "height";
            }
            else
            {
                minDim = $("#<?= $_REQUEST['name_w'] ?>_chartContainer").width();
                minDimCells = widgetWidthCells;
                minDimName = "width";
            }
            
            $('#<?= $_REQUEST['name_w'] ?>_chartContainer').css("position", "relative");
            
            if(showHeader)
            {
                if((2*widgetWidthCells) === widgetHeightCells)
                {
                    onOffButtonPercentHeight = ($('#<?= $_REQUEST['name_w'] ?>_chartContainer').height() - 15)*100/$('#<?= $_REQUEST['name_w'] ?>_chartContainer').height();
                    onOffButtonPercentWidth = ($('#<?= $_REQUEST['name_w'] ?>_chartContainer').height()-15)*100/$('#<?= $_REQUEST['name_w'] ?>_chartContainer').width();
                }
                else
                {
                    onOffButtonPercentHeight = ($('#<?= $_REQUEST['name_w'] ?>_chartContainer').height()-15)*100/$('#<?= $_REQUEST['name_w'] ?>_chartContainer').height();
                    onOffButtonPercentWidth = ($('#<?= $_REQUEST['name_w'] ?>_chartContainer').width()-15)*100/$('#<?= $_REQUEST['name_w'] ?>_chartContainer').width();
                }
            }
            else
            {
                onOffButtonPercentHeight = ($('#<?= $_REQUEST['name_w'] ?>_chartContainer').height()-15)*100/$('#<?= $_REQUEST['name_w'] ?>_chartContainer').height();
                onOffButtonPercentWidth = ($('#<?= $_REQUEST['name_w'] ?>_chartContainer').width()-15)*100/$('#<?= $_REQUEST['name_w'] ?>_chartContainer').width();
            }
            
            $('#<?= $_REQUEST['name_w'] ?>_onOffButton').css("width", onOffButtonPercentWidth + "%");
            $('#<?= $_REQUEST['name_w'] ?>_onOffButton').css("height", onOffButtonPercentHeight + "%");
            $('#<?= $_REQUEST['name_w'] ?>_onOffButton').css("left", parseFloat(100 - onOffButtonPercentWidth)/2 + "%");
            $('#<?= $_REQUEST['name_w'] ?>_onOffButton').css("top", parseFloat(100 - onOffButtonPercentHeight)/2 + "%");
            $('#<?= $_REQUEST['name_w'] ?>_onOffButton').css("font-size", minDim*0.3 + "px");
            $('#<?= $_REQUEST['name_w'] ?>_onOffButton').css("border-radius", minDim*onOffButtonRadius/200);
            
            switch(viewMode)
            {
                case "emptyButton":
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').hide();
                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css('display', 'none');
                    $('#<?= $_REQUEST['name_w'] ?>_display').css('display', 'none');
                    break;
                    
                case "iconOnly":
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton').addClass('centerWithFlex');
                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css('display', 'none');
                    $('#<?= $_REQUEST['name_w'] ?>_display').css('display', 'none');
                    break;
                    
                case "textOnly":
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').hide();
                    $('#<?= $_REQUEST['name_w'] ?>_display').css('display', 'none');
                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("width", "100%");
                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("height", "100%");
                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("font-size", textFontSize + "px");
                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer span').html(widgetTitle);
                    if(fontFamily !== 'Auto')
                    {
                        $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("font-family", fontFamily);
                    }
                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css('display', 'flex');
                    
                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer').textfill({
                        maxFontPixels: -20
                    });

                    if(textFontSize < parseInt($('#<?= $_REQUEST['name_w'] ?>_txtContainer span').css('font-size').replace('px', '')))
                    {
                        $("#<?= $_REQUEST['name_w'] ?>_txtContainer span").css('font-size', textFontSize + 'px');
                    }
                    break;
                    
                case "displayOnly":
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').hide();
                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css('display', 'none');
                    $('#<?= $_REQUEST['name_w'] ?>_display').css('display', 'flex');
                    $('#<?= $_REQUEST['name_w'] ?>_display').css('width', displayWidth + '%');
                    $('#<?= $_REQUEST['name_w'] ?>_display').css('height', displayHeight + '%');
                    $('#<?= $_REQUEST['name_w'] ?>_display').css('margin-left', parseInt((100 - displayWidth)/2) + '%');
                    $('#<?= $_REQUEST['name_w'] ?>_display').css('margin-top', parseInt(($('#<?= $_REQUEST['name_w'] ?>_onOffButton').height() - $('#<?= $_REQUEST['name_w'] ?>_display').height())/2) + 'px');
                    $('#<?= $_REQUEST['name_w'] ?>_display').css('border-radius', parseInt(minDim*displayRadius/100) + 'px');
                    $('#<?= $_REQUEST['name_w'] ?>_display').css('background-color', displayColor);
                    $('#<?= $_REQUEST['name_w'] ?>_display span').html(currentValue);
                    $('#<?= $_REQUEST['name_w'] ?>_display span').css('font-size', displayFontSize + 'px');
                    $('#<?= $_REQUEST['name_w'] ?>_display').textfill({
                        maxFontPixels: -20
                    });

                    if(displayFontSize < parseInt($('#<?= $_REQUEST['name_w'] ?>_display span').css('font-size').replace('px', '')))
                    {
                        $("#<?= $_REQUEST['name_w'] ?>_display span").css('font-size', displayFontSize + 'px');
                    }
                    break;    
                    
                case "iconAndText":
                    $('#<?= $_REQUEST['name_w'] ?>_display').css('display', 'none');
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').css('display', 'flex');
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').css('width', '100%');
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').css('height', '60%');
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').css('border-top-left-radius', 'inherit');
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').css('border-top-right-radius', 'inherit');
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').css('float', 'left');
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').addClass('centerWithFlex');
                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css('display', 'flex');
                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css('width', '100%');
                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css('height', '40%');
                    if(fontFamily !== 'Auto')
                    {
                        $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("font-family", fontFamily);
                    }
                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer span').html(widgetTitle);
                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css('display', 'flex');
                    
                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer').textfill({
                        maxFontPixels: -20
                    });

                    if(textFontSize < parseInt($('#<?= $_REQUEST['name_w'] ?>_txtContainer span').css('font-size').replace('px', '')))
                    {
                        $("#<?= $_REQUEST['name_w'] ?>_txtContainer span").css('font-size', textFontSize + 'px');
                    }
                    break;    
                    
                case "iconAndDisplay":
                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css('display', 'none');
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').css('display', 'flex');
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').css('width', '100%');
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').css('height', parseInt(100 - displayHeight - 10) + '%');
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').css('border-top-left-radius', 'inherit');
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').css('border-top-right-radius', 'inherit');
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').css('float', 'left');
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').addClass('centerWithFlex');
                    $('#<?= $_REQUEST['name_w'] ?>_display').css('display', 'flex');
                    $('#<?= $_REQUEST['name_w'] ?>_display').css('width', displayWidth + '%');
                    $('#<?= $_REQUEST['name_w'] ?>_display').css('height', displayHeight + '%');
                    $('#<?= $_REQUEST['name_w'] ?>_display').css('margin-left', parseInt((100 - displayWidth)/2) + '%');
                    $('#<?= $_REQUEST['name_w'] ?>_display').css('margin-top', '2%');
                    $('#<?= $_REQUEST['name_w'] ?>_display').css('border-radius', parseInt(minDim*displayRadius/100) + 'px');
                    $('#<?= $_REQUEST['name_w'] ?>_display').css('background-color', displayColor);
                    $('#<?= $_REQUEST['name_w'] ?>_display span').html(currentValue);
                    $('#<?= $_REQUEST['name_w'] ?>_display').textfill({
                        maxFontPixels: -20
                    });

                    if(displayFontSize < parseInt($('#<?= $_REQUEST['name_w'] ?>_display span').css('font-size').replace('px', '')))
                    {
                        $("#<?= $_REQUEST['name_w'] ?>_display span").css('font-size', displayFontSize + 'px');
                    }
                    break;
                    
                case "displayAndText":
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').hide();
                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css('display', 'flex');
                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css('width', '100%');
                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer span').html(widgetTitle);
                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css('display', 'flex');
                    
                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer').textfill({
                        maxFontPixels: -20
                    });

                    if(textFontSize < parseInt($('#<?= $_REQUEST['name_w'] ?>_txtContainer span').css('font-size').replace('px', '')))
                    {
                        $("#<?= $_REQUEST['name_w'] ?>_txtContainer span").css('font-size', textFontSize + 'px');
                    }
                    
                    if(fontFamily !== 'Auto')
                    {
                        $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("font-family", fontFamily);
                    }
                    $('#<?= $_REQUEST['name_w'] ?>_display').css('display', 'flex');
                    $('#<?= $_REQUEST['name_w'] ?>_display').css('width', displayWidth + '%');
                    $('#<?= $_REQUEST['name_w'] ?>_display').css('height', displayHeight + '%');
                    $('#<?= $_REQUEST['name_w'] ?>_display').css('margin-left', parseInt((100 - displayWidth)/2) + '%');
                    $('#<?= $_REQUEST['name_w'] ?>_display').css('margin-top', '15%');
                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css('height', parseInt(100 - displayHeight - 15) + '%');
                    $('#<?= $_REQUEST['name_w'] ?>_display').css('border-radius', parseInt(minDim*displayRadius/100) + 'px');
                    $('#<?= $_REQUEST['name_w'] ?>_display span').html(currentValue);
                    $('#<?= $_REQUEST['name_w'] ?>_display').textfill({
                        maxFontPixels: -20
                    });

                    if(displayFontSize < parseInt($('#<?= $_REQUEST['name_w'] ?>_display span').css('font-size').replace('px', '')))
                    {
                        $("#<?= $_REQUEST['name_w'] ?>_display span").css('font-size', displayFontSize + 'px');
                    }
                    $('#<?= $_REQUEST['name_w'] ?>_display').css('background-color', displayColor);
                    $('#<?= $_REQUEST['name_w'] ?>_display').insertBefore('#<?= $_REQUEST['name_w'] ?>_txtContainer');
                    break;
                    
                case "all":
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').css('display', 'flex');
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').css('width', '100%');
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').css('margin-top', '6%');
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').css('border-top-left-radius', 'inherit');
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').css('border-top-right-radius', 'inherit');
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').css('float', 'left');
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').addClass('centerWithFlex');
                    
                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css('display', 'flex');
                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css('width', '100%');
                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer span').html(widgetTitle);
                    if(fontFamily !== 'Auto')
                    {
                        $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("font-family", fontFamily);
                    }
                    
                    $('#<?= $_REQUEST['name_w'] ?>_display').css('display', 'flex');
                    $('#<?= $_REQUEST['name_w'] ?>_display').css('width', displayWidth + '%');
                    $('#<?= $_REQUEST['name_w'] ?>_display').css('height', displayHeight + '%');
                    $('#<?= $_REQUEST['name_w'] ?>_display').css('margin-left', parseInt((100 - displayWidth)/2) + '%');
                    $('#<?= $_REQUEST['name_w'] ?>_display').css('margin-top', '9%');
                    $('#<?= $_REQUEST['name_w'] ?>_display').css('margin-bottom', '3%');
                    $('#<?= $_REQUEST['name_w'] ?>_display').css('border-radius', parseInt(minDim*displayRadius/100) + 'px');
                    $('#<?= $_REQUEST['name_w'] ?>_display span').html(currentValue);
                    $('#<?= $_REQUEST['name_w'] ?>_display').textfill({
                        maxFontPixels: -20
                    });

                    if(displayFontSize < parseInt($('#<?= $_REQUEST['name_w'] ?>_display span').css('font-size').replace('px', '')))
                    {
                        $("#<?= $_REQUEST['name_w'] ?>_display span").css('font-size', displayFontSize + 'px');
                    }
                    $('#<?= $_REQUEST['name_w'] ?>_display').css('background-color', displayColor);
                    $('#<?= $_REQUEST['name_w'] ?>_display').insertBefore('#<?= $_REQUEST['name_w'] ?>_txtContainer');
                    
                    var txtContainerHeight = $('#<?= $_REQUEST['name_w'] ?>_onOffButton').height() - ($('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').height() + $('#<?= $_REQUEST['name_w'] ?>_display').height() + 0.18*($('#<?= $_REQUEST['name_w'] ?>_onOffButton').height()));
                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css('height', txtContainerHeight + 'px');
                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer').textfill({
                        maxFontPixels: -20
                    });

                    if(textFontSize < parseInt($('#<?= $_REQUEST['name_w'] ?>_txtContainer span').css('font-size').replace('px', '')))
                    {
                        $("#<?= $_REQUEST['name_w'] ?>_txtContainer span").css('font-size', textFontSize + 'px');
                    }
                    break;        
            }
            
            $('#<?= $_REQUEST['name_w'] ?>_onOffButton').css("background-color", buttonColor);
                
            switch(viewMode)
            {
                case "emptyButton":
                    break;

                case "iconOnly":
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton').css("color", symbolColor);
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton').css("text-shadow", symbolOffNeonEffect);
                    break;

                case "textOnly":
                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("color", textColor);
                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("text-shadow", textOffNeonEffect);
                    break;

                case "displayOnly":
                    $('#<?= $_REQUEST['name_w'] ?>_display').css('color', displayFontColor);
                    $('#<?= $_REQUEST['name_w'] ?>_display').css("box-shadow", displayOffNeonEffect);
                    break;    

                case "iconAndText":
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton').css("color", symbolColor);
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton').css("text-shadow", symbolOffNeonEffect);
                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("color", textColor);
                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("text-shadow", "none");
                    break;    

                case "iconAndDisplay":
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton').css("color", symbolColor);
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton i').css("text-shadow", symbolOffNeonEffect);
                    $('#<?= $_REQUEST['name_w'] ?>_display').css('color', displayFontColor);
                    $('#<?= $_REQUEST['name_w'] ?>_display').css("box-shadow", displayOffNeonEffect);
                    break;

                case "displayAndText":
                    $('#<?= $_REQUEST['name_w'] ?>_display').css('color', displayFontColor);
                    $('#<?= $_REQUEST['name_w'] ?>_display').css("box-shadow", displayOffNeonEffect);
                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("color", textColor);
                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("text-shadow", "none");
                    break;    

                case "all":
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton').css("color", symbolColor);
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton i').css("text-shadow", symbolOffNeonEffect);
                    $('#<?= $_REQUEST['name_w'] ?>_display').css('color', displayFontColor);
                    $('#<?= $_REQUEST['name_w'] ?>_display').css("box-shadow", displayOffNeonEffect);
                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("color", textColor);
                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("text-shadow", "none");
                    break;        
            }
            
			
            //$('#<?= $_REQUEST['name_w'] ?>_onOffButton').off('mousedown');
            //$('#<?= $_REQUEST['name_w'] ?>_onOffButton').off('mouseup');
            $('#<?= $_REQUEST['name_w'] ?>_onOffButton').off().mousedown(handleMouseDown).mouseup(handleMouseUp);
        }
        
        function sequenceEntityUpdate() {
            switch(actuatorTarget)
            {
                case 'broker':
                    $.ajax({
                        url: "../widgets/actuatorUpdateValue.php",
                        type: "POST",
                        data: {
                            "dashboardId": dashboardId,
                            "entityId": JSON.parse(entityJson).id,
                            "entityJson": entityJson,
                            "attributeName": attributeName,
                            "attributeType": JSON.parse(entityJson)[attributeName].type,
                            "value": currentValue,
                            "dashboardUsername": $('#authForm #hiddenUsername').val()
                        },
                        async: true,
                        dataType: 'json',
                        success: function(data) 
                        {
                            //Per ora non diamo notizie sull'esito degli update in sequenza
                            /*requestComplete = true;
                            clearInterval(setUpdatingMsgInterval);
                            switch(data.result)
                            {
                                case "insertQueryKo":
                                    showUpdateResult("DB KO");
                                    break;

                                case "updateEntityKo":
                                    showUpdateResult("Device KO");
                                    break;

                                case "updateEntityAndUpdateQueryKo":
                                    showUpdateResult("DB and device KO");
                                    break;

                                case "updateQueryKo":
                                    showUpdateResult("DB KO");
                                    break;

                                case "Ok":
                                    showUpdateResult("Device OK");
                                    break;    
                            }*/
                        },
                        error: function(errorData)
                        {
                            //Per ora non diamo notizie sull'esito degli update in sequenza
                            /*requestComplete = true;
                            clearInterval(setUpdatingMsgInterval);
                            showUpdateResult("API KO");
                            console.log("Update value KO");
                            console.log(JSON.stringify(errorData));*/
                        }       
                    });
                    break;
                    
                case 'app':
                    $.ajax({
                        url: "../widgets/actuatorUpdateValuePersonalApps.php",
                        type: "POST",
                        data: {
                            "inputName": nodeRedInputName,
                            "dashboardId": dashboardId,
                            "widgetName": "<?= $_REQUEST['name_w'] ?>",
                            "username" : $('#authForm #hiddenUsername').val(),
                            "value": currentValue,
                            "endPointPort": "<?= escapeForJS($_REQUEST['endPointPort']) ?>",
                            "httpRoot": "<?= escapeForJS($_REQUEST['httpRoot']) ?>",
                            "nrInputId": nrInputId
                        },
                        async: true,
                        dataType: 'json',
                        success: function(data) 
                        {
                            /*requestComplete = true;
                            clearInterval(setUpdatingMsgInterval);
                            switch(data.result)
                            {
                                case "insertQueryKo":
                                    showUpdateResult("DB KO");
                                    break;

                                case "updateBlockKo":
                                    showUpdateResult("Device KO");
                                    break;

                                case "updateBlockAndUpdateQueryKo":
                                    showUpdateResult("DB and device KO");
                                    break;

                                case "updateQueryKo":
                                    showUpdateResult("DB KO");
                                    break;

                                case "Ok":
                                    showUpdateResult("Device OK");
                                    break;    
                            }*/
                        },
                        error: function(errorData)
                        {
                            /*requestComplete = true;
                            clearInterval(setUpdatingMsgInterval);
                            showUpdateResult("API KO");
                            console.log("Update value KO");
                            console.log(JSON.stringify(errorData));*/
                        }
                    });
                    break;
            }
        }
        
        function handleMouseDown()
        {
            $('#<?= $_REQUEST['name_w'] ?>_onOffButton').addClass('onOffButtonActive');
            $('#<?= $_REQUEST['name_w'] ?>_onOffButton').css("background-color", buttonClickColor);

            switch(viewMode)
            {
                case "emptyButton":

                    break;

                case "iconOnly":
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton').css("color", symbolColor);
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton').css("text-shadow", symbolOnNeonEffect);
                    break;

                case "textOnly":
                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("color", textClickColor);
                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("text-shadow", textOnNeonEffect);
                    break;

                case "displayOnly":
                    $('#<?= $_REQUEST['name_w'] ?>_display').css('color', displayFontClickColor);
                    $('#<?= $_REQUEST['name_w'] ?>_display').css("box-shadow", displayOnNeonEffect);
                    break;

                case "iconAndText":
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton').css("color", symbolColor);
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton').css("text-shadow", symbolOnNeonEffect);
                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("color", textClickColor);
                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("text-shadow", "none");
                    break;

                case "iconAndDisplay":
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton').css("color", symbolColor);
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton i').css("text-shadow", symbolOnNeonEffect);
                    $('#<?= $_REQUEST['name_w'] ?>_display').css('color', displayFontClickColor);
                    $('#<?= $_REQUEST['name_w'] ?>_display').css("box-shadow", displayOnNeonEffect);
                    break;

                case "displayAndText":
                    $('#<?= $_REQUEST['name_w'] ?>_display').css('color', displayFontClickColor);
                    $('#<?= $_REQUEST['name_w'] ?>_display').css("box-shadow", displayOnNeonEffect);
                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("color", textClickColor);
                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("text-shadow", "none");
                    break;

                case "all":
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton').css("color", symbolColor);
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton i').css("text-shadow", symbolOnNeonEffect);
                    $('#<?= $_REQUEST['name_w'] ?>_display').css('color', displayFontClickColor);
                    $('#<?= $_REQUEST['name_w'] ?>_display').css("box-shadow", displayOnNeonEffect);
                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("color", textClickColor);
                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("text-shadow", "none");
                    break;
            }

            switch(impulseMode)
            {
                case "singlePress":
                    break;

                case "sequence":
                    currentValue = impulseValue;
                    switch(viewMode)
                    {
                        case "displayOnly": case "iconAndDisplay": case "displayAndText": case "all":
                            $('#<?= $_REQUEST['name_w'] ?>_display span').html(impulseValue);
                            break;

                        default:
                            break;
                    }
                    sequenceEntityUpdateInterval = setInterval(sequenceEntityUpdate, 500);
                    break;

                default:
                    break;
            }

            if (code != null && code != '') {
                //execute();
                var functionName = "execute_" + "<?= $_REQUEST['name_w'] ?>";
                window[functionName]();
            }
        }

        function handleMouseUp() {
            $('#<?= $_REQUEST['name_w'] ?>_onOffButton').removeClass('onOffButtonActive');
            $('#<?= $_REQUEST['name_w'] ?>_onOffButton').css("background-color", buttonColor);

            switch(viewMode)
            {
                case "emptyButton":

                    break;

                case "iconOnly":
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton').css("color", symbolClickColor);
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton').css("text-shadow", symbolOffNeonEffect);
                    break;

                case "textOnly":
                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("color", textColor);
                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("text-shadow", textOffNeonEffect);
                    break;

                case "displayOnly":
                    $('#<?= $_REQUEST['name_w'] ?>_display').css('color', displayFontColor);
                    $('#<?= $_REQUEST['name_w'] ?>_display').css("box-shadow", displayOffNeonEffect);
                    break;

                case "iconAndText":
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton').css("color", symbolClickColor);
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton').css("text-shadow", symbolOffNeonEffect);
                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("color", textColor);
                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("text-shadow", "none");
                    break;

                case "iconAndDisplay":
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton').css("color", symbolClickColor);
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton i').css("text-shadow", symbolOffNeonEffect);
                    $('#<?= $_REQUEST['name_w'] ?>_display').css('color', displayFontColor);
                    $('#<?= $_REQUEST['name_w'] ?>_display').css("box-shadow", displayOffNeonEffect);
                    break;

                case "displayAndText":
                    $('#<?= $_REQUEST['name_w'] ?>_display').css('color', displayFontColor);
                    $('#<?= $_REQUEST['name_w'] ?>_display').css("box-shadow", displayOffNeonEffect);
                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("color", textColor);
                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("text-shadow", "none");
                    break;

                case "all":
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton').css("color", symbolClickColor);
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton i').css("text-shadow", symbolOffNeonEffect);
                    $('#<?= $_REQUEST['name_w'] ?>_display').css('color', displayFontColor);
                    $('#<?= $_REQUEST['name_w'] ?>_display').css("box-shadow", displayOffNeonEffect);
                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("color", textColor);
                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("text-shadow", "none");
                    break;
            }

            switch(impulseMode)
            {
                case "singlePress":

                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').hide();
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-times-circle-o').css("display", "none");
                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("display", "none");
                    $('#<?= $_REQUEST['name_w'] ?>_display').css("display", "none");
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-refresh').css("display", "block");
                    var loadingIconMarginLeft = parseInt(($('#<?= $_REQUEST['name_w'] ?>_onOffButton').width()- $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-refresh').width())/2);
                    var loadingIconMarginTop = parseInt(($('#<?= $_REQUEST['name_w'] ?>_onOffButton').height()- $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-refresh').height())/2);
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-refresh').css("top", loadingIconMarginTop + "px");
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-refresh').css("left", loadingIconMarginLeft + "px");

                    //$('#<?= $_REQUEST['name_w'] ?>_onOffButton').off('mousedown', handleMouseDown);
                    //$('#<?= $_REQUEST['name_w'] ?>_onOffButton').off('mouseup', handleMouseUp);

                    switch(actuatorTarget)
                    {
                        case 'broker':
                            $.ajax({
                                url: "../widgets/actuatorUpdateValue.php",
                                type: "POST",
                                data: {
                                    "dashboardId": dashboardId,
                                    "entityId": JSON.parse(entityJson).id,
                                    "entityJson": entityJson,
                                    "attributeName": attributeName,
                                    "attributeType": JSON.parse(entityJson)[attributeName].type,
                                    "value": impulseValue,
                                    "dashboardUsername": $('#authForm #hiddenUsername').val()
                                },
                                async: true,
                                dataType: 'json',
                                success: function(data)
                                {
                                    switch(data.result)
                                    {
                                        case "Ok":
                                            setTimeout(function(){
                                                $.ajax({
                                                    url: "../widgets/actuatorUpdateValue.php",
                                                    type: "POST",
                                                    data: {
                                                        "dashboardId": dashboardId,
                                                        "entityId": JSON.parse(entityJson).id,
                                                        "entityJson": entityJson,
                                                        "attributeName": attributeName,
                                                        "attributeType": JSON.parse(entityJson)[attributeName].type,
                                                        "value": baseValue,
                                                        "dashboardUsername": $('#authForm #hiddenUsername').val()
                                                    },
                                                    async: true,
                                                    dataType: 'json',
                                                    success: function(data)
                                                    {
                                                        switch(data.result)
                                                        {
                                                            case "Ok":
                                                                $('#<?= $_REQUEST['name_w'] ?>_onOffButton').off().mousedown(handleMouseDown).mouseup(handleMouseUp);
                                                                $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-refresh').css("display", "none");

                                                                switch(viewMode)
                                                                {
                                                                    case "emptyButton":
                                                                        break;

                                                                    case "iconOnly":
                                                                        $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').show();
                                                                        break;

                                                                    case "textOnly":
                                                                        $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css('display', 'flex');
                                                                        break;

                                                                    case "displayOnly":
                                                                        $('#<?= $_REQUEST['name_w'] ?>_display').css('display', 'flex');
                                                                        $('#<?= $_REQUEST['name_w'] ?>_display span').html(currentValue);
                                                                        break;

                                                                    case "iconAndText":
                                                                        $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').show();
                                                                        $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css('display', 'flex');
                                                                        break;

                                                                    case "iconAndDisplay":
                                                                        $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').show();
                                                                        $('#<?= $_REQUEST['name_w'] ?>_display').css('display', 'flex');
                                                                        $('#<?= $_REQUEST['name_w'] ?>_display span').html(currentValue);
                                                                        $('#<?= $_REQUEST['name_w'] ?>_display').textfill({
                                                                            maxFontPixels: -20
                                                                        });

                                                                        if(displayFontSize < parseInt($('#<?= $_REQUEST['name_w'] ?>_display span').css('font-size').replace('px', '')))
                                                                        {
                                                                            $("#<?= $_REQUEST['name_w'] ?>_display span").css('font-size', displayFontSize + 'px');
                                                                        }
                                                                        break;

                                                                    case "displayAndText":
                                                                        $('#<?= $_REQUEST['name_w'] ?>_display').css('display', 'flex');
                                                                        $('#<?= $_REQUEST['name_w'] ?>_display span').html(currentValue);
                                                                        $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css('display', 'flex');
                                                                        break;

                                                                    case "all":
                                                                        $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').show();
                                                                        $('#<?= $_REQUEST['name_w'] ?>_display').css('display', 'flex');
                                                                        $('#<?= $_REQUEST['name_w'] ?>_display span').html(currentValue);
                                                                        $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css('display', 'flex');
                                                                        break;
                                                                }
                                                                break;

                                                            default:
                                                                $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-refresh').css("display", "none");
                                                                $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-times-circle-o').css("display", "block");
                                                                var errorIconMarginLeft = parseInt(($('#<?= $_REQUEST['name_w'] ?>_onOffButton').width()- $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-times-circle-o').width())/2);
                                                                var errorIconMarginTop = parseInt(($('#<?= $_REQUEST['name_w'] ?>_onOffButton').height()- $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-times-circle-o').height())/2);
                                                                $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-times-circle-o').css("top", errorIconMarginTop + "px");
                                                                $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-times-circle-o').css("left", errorIconMarginLeft + "px");

                                                                setTimeout(function(){
                                                                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-refresh').css("display", "none");
                                                                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-times-circle-o').css("display", "none");

                                                                    currentValue = baseValue;

                                                                    switch(viewMode)
                                                                    {
                                                                        case "emptyButton":

                                                                            break;

                                                                        case "iconOnly":
                                                                            $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').show();
                                                                            break;

                                                                        case "textOnly":
                                                                            $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css('display', 'flex');
                                                                            break;

                                                                        case "displayOnly":
                                                                            $('#<?= $_REQUEST['name_w'] ?>_display').css('display', 'flex');
                                                                            $('#<?= $_REQUEST['name_w'] ?>_display span').html(currentValue);
                                                                            break;

                                                                        case "iconAndText":
                                                                            $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').show();
                                                                            $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css('display', 'flex');
                                                                            break;

                                                                        case "iconAndDisplay":
                                                                            $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').show();
                                                                            $('#<?= $_REQUEST['name_w'] ?>_display').css('display', 'flex');
                                                                            $('#<?= $_REQUEST['name_w'] ?>_display span').html(currentValue);
                                                                            $('#<?= $_REQUEST['name_w'] ?>_display').textfill({
                                                                                maxFontPixels: -20
                                                                            });

                                                                            if(displayFontSize < parseInt($('#<?= $_REQUEST['name_w'] ?>_display span').css('font-size').replace('px', '')))
                                                                            {
                                                                                $("#<?= $_REQUEST['name_w'] ?>_display span").css('font-size', displayFontSize + 'px');
                                                                            }
                                                                            break;

                                                                        case "displayAndText":
                                                                            $('#<?= $_REQUEST['name_w'] ?>_display').css('display', 'flex');
                                                                            $('#<?= $_REQUEST['name_w'] ?>_display span').html(currentValue);
                                                                            $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css('display', 'flex');
                                                                            break;

                                                                        case "all":
                                                                            $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').show();
                                                                            $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css('display', 'flex');
                                                                            $('#<?= $_REQUEST['name_w'] ?>_display').css('display', 'flex');
                                                                            $('#<?= $_REQUEST['name_w'] ?>_display span').html(currentValue);
                                                                            break;
                                                                    }

                                                                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton').off().mousedown(handleMouseDown).mouseup(handleMouseUp);
                                                                }, 1500);
                                                                break;
                                                        }
                                                    },
                                                    error: function(data)
                                                    {
                                                        $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-refresh').css("display", "none");
                                                        $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-times-circle-o').css("display", "block");
                                                        var errorIconMarginLeft = parseInt(($('#<?= $_REQUEST['name_w'] ?>_onOffButton').width()- $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-times-circle-o').width())/2);
                                                        var errorIconMarginTop = parseInt(($('#<?= $_REQUEST['name_w'] ?>_onOffButton').height()- $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-times-circle-o').height())/2);
                                                        $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-times-circle-o').css("top", errorIconMarginTop + "px");
                                                        $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-times-circle-o').css("left", errorIconMarginLeft + "px");

                                                        setTimeout(function(){
                                                            $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-refresh').css("display", "none");
                                                            $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-times-circle-o').css("display", "none");

                                                            currentValue = baseValue;

                                                            switch(viewMode)
                                                            {
                                                                case "emptyButton":

                                                                    break;

                                                                case "iconOnly":
                                                                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').show();
                                                                    break;

                                                                case "textOnly":
                                                                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css('display', 'flex');
                                                                    break;

                                                                case "displayOnly":
                                                                    $('#<?= $_REQUEST['name_w'] ?>_display').css('display', 'flex');
                                                                    $('#<?= $_REQUEST['name_w'] ?>_display span').html(currentValue);
                                                                    break;

                                                                case "iconAndText":
                                                                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').show();
                                                                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css('display', 'flex');
                                                                    break;

                                                                case "iconAndDisplay":
                                                                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').show();
                                                                    $('#<?= $_REQUEST['name_w'] ?>_display').css('display', 'flex');
                                                                    $('#<?= $_REQUEST['name_w'] ?>_display span').html(currentValue);
                                                                    $('#<?= $_REQUEST['name_w'] ?>_display').textfill({
                                                                        maxFontPixels: -20
                                                                    });

                                                                    if(displayFontSize < parseInt($('#<?= $_REQUEST['name_w'] ?>_display span').css('font-size').replace('px', '')))
                                                                    {
                                                                        $("#<?= $_REQUEST['name_w'] ?>_display span").css('font-size', displayFontSize + 'px');
                                                                    }
                                                                    break;

                                                                case "displayAndText":
                                                                    $('#<?= $_REQUEST['name_w'] ?>_display').css('display', 'flex');
                                                                    $('#<?= $_REQUEST['name_w'] ?>_display span').html(currentValue);
                                                                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css('display', 'flex');
                                                                    break;

                                                                case "all":
                                                                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').show();
                                                                    $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css('display', 'flex');
                                                                    $('#<?= $_REQUEST['name_w'] ?>_display').css('display', 'flex');
                                                                    $('#<?= $_REQUEST['name_w'] ?>_display span').html(currentValue);
                                                                    break;
                                                            }

                                                            $('#<?= $_REQUEST['name_w'] ?>_onOffButton').off().mousedown(handleMouseDown).mouseup(handleMouseUp);
                                                        }, 1500);
                                                        console.log("Impulse ko:");
                                                        console.log(data);
                                                    }
                                                });
                                            }, 1000);
                                            break;

                                        default:
                                            $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-refresh').css("display", "none");
                                            $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-times-circle-o').css("display", "block");
                                            var errorIconMarginLeft = parseInt(($('#<?= $_REQUEST['name_w'] ?>_onOffButton').width()- $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-times-circle-o').width())/2);
                                            var errorIconMarginTop = parseInt(($('#<?= $_REQUEST['name_w'] ?>_onOffButton').height()- $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-times-circle-o').height())/2);
                                            $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-times-circle-o').css("top", errorIconMarginTop + "px");
                                            $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-times-circle-o').css("left", errorIconMarginLeft + "px");

                                            setTimeout(function(){
                                                $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-refresh').css("display", "none");
                                                $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-times-circle-o').css("display", "none");

                                                currentValue = baseValue;

                                                switch(viewMode)
                                                {
                                                    case "emptyButton":

                                                        break;

                                                    case "iconOnly":
                                                        $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').show();
                                                        break;

                                                    case "textOnly":
                                                        $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css('display', 'flex');
                                                        break;

                                                    case "displayOnly":
                                                        $('#<?= $_REQUEST['name_w'] ?>_display').css('display', 'flex');
                                                        $('#<?= $_REQUEST['name_w'] ?>_display span').html(currentValue);
                                                        break;

                                                    case "iconAndText":
                                                        $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').show();
                                                        $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css('display', 'flex');
                                                        break;

                                                    case "iconAndDisplay":
                                                        $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').show();
                                                        $('#<?= $_REQUEST['name_w'] ?>_display').css('display', 'flex');
                                                        $('#<?= $_REQUEST['name_w'] ?>_display span').html(currentValue);
                                                        $('#<?= $_REQUEST['name_w'] ?>_display').textfill({
                                                            maxFontPixels: -20
                                                        });

                                                        if(displayFontSize < parseInt($('#<?= $_REQUEST['name_w'] ?>_display span').css('font-size').replace('px', '')))
                                                        {
                                                            $("#<?= $_REQUEST['name_w'] ?>_display span").css('font-size', displayFontSize + 'px');
                                                        }
                                                        break;

                                                    case "displayAndText":
                                                        $('#<?= $_REQUEST['name_w'] ?>_display').css('display', 'flex');
                                                        $('#<?= $_REQUEST['name_w'] ?>_display span').html(currentValue);
                                                        $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css('display', 'flex');
                                                        break;

                                                    case "all":
                                                        $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').show();
                                                        $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css('display', 'flex');
                                                        $('#<?= $_REQUEST['name_w'] ?>_display').css('display', 'flex');
                                                        $('#<?= $_REQUEST['name_w'] ?>_display span').html(currentValue);
                                                        break;
                                                }

                                                $('#<?= $_REQUEST['name_w'] ?>_onOffButton').off().mousedown(handleMouseDown).mouseup(handleMouseUp);
                                            }, 1500);
                                            break;
                                    }
                                },
                                error: function(data)
                                {
                                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-refresh').css("display", "none");
                                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-times-circle-o').css("display", "block");
                                    var errorIconMarginLeft = parseInt(($('#<?= $_REQUEST['name_w'] ?>_onOffButton').width()- $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-times-circle-o').width())/2);
                                    var errorIconMarginTop = parseInt(($('#<?= $_REQUEST['name_w'] ?>_onOffButton').height()- $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-times-circle-o').height())/2);
                                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-times-circle-o').css("top", errorIconMarginTop + "px");
                                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-times-circle-o').css("left", errorIconMarginLeft + "px");

                                    setTimeout(function(){
                                        $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-refresh').css("display", "none");
                                        $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-times-circle-o').css("display", "none");

                                        currentValue = baseValue;

                                        switch(viewMode)
                                        {
                                            case "emptyButton":

                                                break;

                                            case "iconOnly":
                                                $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').show();
                                                break;

                                            case "textOnly":
                                                $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css('display', 'flex');
                                                break;

                                            case "displayOnly":
                                                $('#<?= $_REQUEST['name_w'] ?>_display').css('display', 'flex');
                                                $('#<?= $_REQUEST['name_w'] ?>_display span').html(currentValue);
                                                break;

                                            case "iconAndText":
                                                $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').show();
                                                $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css('display', 'flex');
                                                break;

                                            case "iconAndDisplay":
                                                $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').show();
                                                $('#<?= $_REQUEST['name_w'] ?>_display').css('display', 'flex');
                                                $('#<?= $_REQUEST['name_w'] ?>_display span').html(currentValue);
                                                $('#<?= $_REQUEST['name_w'] ?>_display').textfill({
                                                    maxFontPixels: -20
                                                });

                                                if(displayFontSize < parseInt($('#<?= $_REQUEST['name_w'] ?>_display span').css('font-size').replace('px', '')))
                                                {
                                                    $("#<?= $_REQUEST['name_w'] ?>_display span").css('font-size', displayFontSize + 'px');
                                                }
                                                break;

                                            case "displayAndText":
                                                $('#<?= $_REQUEST['name_w'] ?>_display').css('display', 'flex');
                                                $('#<?= $_REQUEST['name_w'] ?>_display span').html(currentValue);
                                                $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css('display', 'flex');
                                                break;

                                            case "all":
                                                $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').show();
                                                $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css('display', 'flex');
                                                $('#<?= $_REQUEST['name_w'] ?>_display').css('display', 'flex');
                                                $('#<?= $_REQUEST['name_w'] ?>_display span').html(currentValue);
                                                break;
                                        }

                                        $('#<?= $_REQUEST['name_w'] ?>_onOffButton').off().mousedown(handleMouseDown).mouseup(handleMouseUp);
                                    }, 1500);
                                    console.log("Impulse ko:");
                                    console.log(data);
                                }
                            });
                            break;

                        case 'app':
                            var valueToSend = null;
                            if(impulseValue === 'currentPosition')
                            {
                                if(navigator.geolocation)
                                {
                                    navigator.geolocation.getCurrentPosition(function(position) {
                                        valueToSend = {
                                               latitude: position.coords.latitude,
                                               longitude: position.coords.longitude
                                            };
                                        valueToSend = JSON.stringify(valueToSend);
                                    });
                                }
                            }
                            else
                            {
                                valueToSend = impulseValue;
                            }
                            if(useWebSocket) {
                                var data = {
                                      "msgType": "SendToEmitter",
                                      "widgetUniqueName": widgetName,
                                      "value": valueToSend,
                                      "inputName": nodeRedInputName,
                                      "dashboardId": dashboardId,
                                      "username" : $('#authForm #hiddenUsername').val(),
                                      "nrInputId": nrInputId
                                };
                                var webSocket = Window.webSockets[widgetName];
                                webSocket.ackReceived=false;
                                webSocket.onAck = onSuccess1;
                                console.log(widgetName+" SEND ackReceived:"+webSocket.ackReceived)
                                if(webSocket.readyState==webSocket.OPEN) {
                                    webSocket.send(JSON.stringify(data));
                                    webSocket.timeout = setTimeout(function() {
                                      if(!webSocket.ackReceived) {
                                        console.log(widgetName+" ERR1 ackReceived:"+webSocket.ackReceived)
                                        onError1({});
                                      }
                                    },60000)
                                } else {
                                    console.log(widgetName+" ERR1 socket not OPEN");
                                    onError1({});
                                }
                            } else {
                                $.ajax({
                                    url: "../widgets/actuatorUpdateValuePersonalApps.php",
                                    type: "POST",
                                    data: {
                                        "inputName": nodeRedInputName,
                                        "dashboardId": dashboardId,
                                        "widgetName": "<?= $_REQUEST['name_w'] ?>",
                                        "username" : $('#authForm #hiddenUsername').val(),
                                        "value": valueToSend,
                                        "endPointPort": "<?= escapeForJS($_REQUEST['endPointPort']) ?>",
                                        "httpRoot": "<?= escapeForJS($_REQUEST['httpRoot']) ?>",
                                        "nrInputId": nrInputId
                                    },
                                    async: true,
                                    dataType: 'json',
                                    success: onSuccess1,
                                    error: onError1
                                });
                            }
                    break;

                case "sequence":
                    clearInterval(sequenceEntityUpdateInterval);
                    $.ajax({
                        url: "../widgets/actuatorUpdateValue.php",
                        type: "POST",
                        data: {
                            "dashboardId": dashboardId,
                            "entityId": JSON.parse(entityJson).id,
                            "entityJson": entityJson,
                            "attributeName": attributeName,
                            "attributeType": JSON.parse(entityJson)[attributeName].type,
                            "value": baseValue,
                            "dashboardUsername": $('#authForm #hiddenUsername').val()
                        },
                        async: true,
                        dataType: 'json',
                        success: function(data)
                        {
                            /*switch(data.result)
                            {
                                case "insertQueryKo":
                                    showUpdateResult("DB KO");
                                    break;

                                case "updateEntityKo":
                                    showUpdateResult("Device KO");
                                    break;

                                case "updateEntityAndUpdateQueryKo":
                                    showUpdateResult("DB and device KO");
                                    break;

                                case "updateQueryKo":
                                    showUpdateResult("DB KO");
                                    break;

                                case "Ok":
                                    showUpdateResult("Device OK");
                                    break;
                            }*/
                        },
                        error: function(errorData)
                        {
                            //showUpdateResult("API KO");
                            console.log("Update value KO");
                            console.log(JSON.stringify(errorData));
                        },
                        complete: function()
                        {
                            switch(viewMode)
                            {
                                case "displayOnly": case "iconAndDisplay": case "displayAndText": case "all":
                                    $('#<?= $_REQUEST['name_w'] ?>_display span').html(baseValue);
                                    break;

                                default:
                                    break;
                            }
                        }
                    });
                    break;

                default:

                    break;
            }
        }
        }

        var onSuccess1 = function (data) {
            switch(data.result)
            {
                case "Ok":
                    if(useWebSocket) {
                        var webSocket = Window.webSockets[data.widgetName];
                        console.log(data.widgetName+" SUCC1 ackReceived: "+webSocket.ackReceived)
                    }
                    setTimeout(function(){
                        if(useWebSocket) {
                            var dataToEmitter = {
                                "msgType": "SendToEmitter",
                                "widgetUniqueName": data.widgetName,
                                "value": baseValue,
                                "inputName": nodeRedInputName,
                                "dashboardId": dashboardId,
                                "username" : $('#authForm #hiddenUsername').val(),
                                "nrInputId": nrInputId
                            };
                            webSocket.ackReceived=false
                            webSocket.onAck = onSuccess2
                            webSocket.send(JSON.stringify(dataToEmitter));
                            webSocket.timeout=setTimeout(function() {
                              console.log(data.widgetName+" ERR2 ackReceived:"+webSocket.ackReceived)
                              if(!webSocket.ackReceived)
                                onError2({widgetName: data.widgetName})
                            },3000);
                        } else {
                            $.ajax({
                                url: "../widgets/actuatorUpdateValuePersonalApps.php",
                                type: "POST",
                                data: {
                                    "inputName": nodeRedInputName,
                                    "dashboardId": dashboardId,
                                    "widgetName": "<?= $_REQUEST['name_w'] ?>",
                                    "username" : $('#authForm #hiddenUsername').val(),
                                    "value": baseValue,
                                    "endPointPort": "<?= escapeForJS($_REQUEST['endPointPort']) ?>",
                                    "httpRoot": "<?= escapeForJS($_REQUEST['httpRoot']) ?>",
                                    "nrInputId": nrInputId
                                },
                                async: true,
                                dataType: 'json',
                                success: onSuccess2,
                                error: onError2
                            });
                        }
                    }, 1000);
                    break;
                default:
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-refresh').css("display", "none");
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-times-circle-o').css("display", "block");
                    var errorIconMarginLeft = parseInt(($('#<?= $_REQUEST['name_w'] ?>_onOffButton').width()- $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-times-circle-o').width())/2);
                    var errorIconMarginTop = parseInt(($('#<?= $_REQUEST['name_w'] ?>_onOffButton').height()- $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-times-circle-o').height())/2);
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-times-circle-o').css("top", errorIconMarginTop + "px");
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-times-circle-o').css("left", errorIconMarginLeft + "px");

                    setTimeout(function(){
                        $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-refresh').css("display", "none");
                        $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-times-circle-o').css("display", "none");

                        currentValue = baseValue;

                        switch(viewMode)
                        {
                            case "emptyButton":
                                break;
                            case "iconOnly":
                                $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').show();
                                break;
                            case "textOnly":
                                $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css('display', 'flex');
                                break;
                            case "displayOnly":
                                $('#<?= $_REQUEST['name_w'] ?>_display').css('display', 'flex');
                                $('#<?= $_REQUEST['name_w'] ?>_display span').html(currentValue);
                                break;
                            case "iconAndText":
                                $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').show();
                                $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css('display', 'flex');
                                break;
                            case "iconAndDisplay":
                                $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').show();
                                $('#<?= $_REQUEST['name_w'] ?>_display').css('display', 'flex');
                                $('#<?= $_REQUEST['name_w'] ?>_display span').html(currentValue);
                                $('#<?= $_REQUEST['name_w'] ?>_display').textfill({
                                    maxFontPixels: -20
                                });

                                if(displayFontSize < parseInt($('#<?= $_REQUEST['name_w'] ?>_display span').css('font-size').replace('px', '')))
                                {
                                    $("#<?= $_REQUEST['name_w'] ?>_display span").css('font-size', displayFontSize + 'px');
                                }
                                break;
                            case "displayAndText":
                                $('#<?= $_REQUEST['name_w'] ?>_display').css('display', 'flex');
                                $('#<?= $_REQUEST['name_w'] ?>_display span').html(currentValue);
                                $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css('display', 'flex');
                                break;
                            case "all":
                                $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').show();
                                $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css('display', 'flex');
                                $('#<?= $_REQUEST['name_w'] ?>_display').css('display', 'flex');
                                $('#<?= $_REQUEST['name_w'] ?>_display span').html(currentValue);
                                break;
                        }

                        $('#<?= $_REQUEST['name_w'] ?>_onOffButton').off().mousedown(handleMouseDown).mouseup(handleMouseUp);
                    }, 1500);
                    break;
            }
        }

        var onError1 = function(data) {
            $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-refresh').css("display", "none");
            $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-times-circle-o').css("display", "block");
            var errorIconMarginLeft = parseInt(($('#<?= $_REQUEST['name_w'] ?>_onOffButton').width()- $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-times-circle-o').width())/2);
            var errorIconMarginTop = parseInt(($('#<?= $_REQUEST['name_w'] ?>_onOffButton').height()- $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-times-circle-o').height())/2);
            $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-times-circle-o').css("top", errorIconMarginTop + "px");
            $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-times-circle-o').css("left", errorIconMarginLeft + "px");

            setTimeout(function(){
                $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-refresh').css("display", "none");
                $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-times-circle-o').css("display", "none");

                currentValue = baseValue;

                switch(viewMode)
                {
                    case "emptyButton":

                        break;

                    case "iconOnly":
                        $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').show();
                        break;

                    case "textOnly":
                        $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css('display', 'flex');
                        break;

                    case "displayOnly":
                        $('#<?= $_REQUEST['name_w'] ?>_display').css('display', 'flex');
                        $('#<?= $_REQUEST['name_w'] ?>_display span').html(currentValue);
                        break;

                    case "iconAndText":
                        $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').show();
                        $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css('display', 'flex');
                        break;

                    case "iconAndDisplay":
                        $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').show();
                        $('#<?= $_REQUEST['name_w'] ?>_display').css('display', 'flex');
                        $('#<?= $_REQUEST['name_w'] ?>_display span').html(currentValue);
                        $('#<?= $_REQUEST['name_w'] ?>_display').textfill({
                            maxFontPixels: -20
                        });

                        if(displayFontSize < parseInt($('#<?= $_REQUEST['name_w'] ?>_display span').css('font-size').replace('px', '')))
                        {
                            $("#<?= $_REQUEST['name_w'] ?>_display span").css('font-size', displayFontSize + 'px');
                        }
                        break;

                    case "displayAndText":
                        $('#<?= $_REQUEST['name_w'] ?>_display').css('display', 'flex');
                        $('#<?= $_REQUEST['name_w'] ?>_display span').html(currentValue);
                        $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css('display', 'flex');
                        break;

                    case "all":
                        $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').show();
                        $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css('display', 'flex');
                        $('#<?= $_REQUEST['name_w'] ?>_display').css('display', 'flex');
                        $('#<?= $_REQUEST['name_w'] ?>_display span').html(currentValue);
                        break;
                }

                $('#<?= $_REQUEST['name_w'] ?>_onOffButton').off().mousedown(handleMouseDown).mouseup(handleMouseUp);
            }, 1500);
            console.log("Impulse ko:");
            console.log(data);
        }

        var onSuccess2=function(data) {
            switch(data.result)
            {
                case "Ok":
                    if(useWebSocket) {
                        console.log(data.widgetName+" SUCC2 ackReceived:"+Window.webSockets[data.widgetName].ackReceived)
                    }

                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton').off().mousedown(handleMouseDown).mouseup(handleMouseUp);
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-refresh').css("display", "none");

                    switch(viewMode)
                    {
                        case "emptyButton":
                            break;

                        case "iconOnly":
                            $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').show();
                            break;

                        case "textOnly":
                            $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css('display', 'flex');
                            break;

                        case "displayOnly":
                            $('#<?= $_REQUEST['name_w'] ?>_display').css('display', 'flex');
                            $('#<?= $_REQUEST['name_w'] ?>_display span').html(currentValue);
                            break;

                        case "iconAndText":
                            $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').show();
                            $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css('display', 'flex');
                            break;

                        case "iconAndDisplay":
                            $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').show();
                            $('#<?= $_REQUEST['name_w'] ?>_display').css('display', 'flex');
                            $('#<?= $_REQUEST['name_w'] ?>_display span').html(currentValue);
                            $('#<?= $_REQUEST['name_w'] ?>_display').textfill({
                                maxFontPixels: -20
                            });

                            if(displayFontSize < parseInt($('#<?= $_REQUEST['name_w'] ?>_display span').css('font-size').replace('px', '')))
                            {
                                $("#<?= $_REQUEST['name_w'] ?>_display span").css('font-size', displayFontSize + 'px');
                            }
                            break;

                        case "displayAndText":
                            $('#<?= $_REQUEST['name_w'] ?>_display').css('display', 'flex');
                            $('#<?= $_REQUEST['name_w'] ?>_display span').html(currentValue);
                            $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css('display', 'flex');
                            break;

                        case "all":
                            $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').show();
                            $('#<?= $_REQUEST['name_w'] ?>_display').css('display', 'flex');
                            $('#<?= $_REQUEST['name_w'] ?>_display span').html(currentValue);
                            $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css('display', 'flex');
                            break;
                    }
                    break;

                default:
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-refresh').css("display", "none");
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-times-circle-o').css("display", "block");
                    var errorIconMarginLeft = parseInt(($('#<?= $_REQUEST['name_w'] ?>_onOffButton').width()- $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-times-circle-o').width())/2);
                    var errorIconMarginTop = parseInt(($('#<?= $_REQUEST['name_w'] ?>_onOffButton').height()- $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-times-circle-o').height())/2);
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-times-circle-o').css("top", errorIconMarginTop + "px");
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-times-circle-o').css("left", errorIconMarginLeft + "px");

                    setTimeout(function(){
                        $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-refresh').css("display", "none");
                        $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-times-circle-o').css("display", "none");

                        currentValue = baseValue;

                        switch(viewMode)
                        {
                            case "emptyButton":

                                break;

                            case "iconOnly":
                                $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').show();
                                break;

                            case "textOnly":
                                $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css('display', 'flex');
                                break;

                            case "displayOnly":
                                $('#<?= $_REQUEST['name_w'] ?>_display').css('display', 'flex');
                                $('#<?= $_REQUEST['name_w'] ?>_display span').html(currentValue);
                                break;

                            case "iconAndText":
                                $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').show();
                                $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css('display', 'flex');
                                break;

                            case "iconAndDisplay":
                                $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').show();
                                $('#<?= $_REQUEST['name_w'] ?>_display').css('display', 'flex');
                                $('#<?= $_REQUEST['name_w'] ?>_display span').html(currentValue);
                                $('#<?= $_REQUEST['name_w'] ?>_display').textfill({
                                    maxFontPixels: -20
                                });

                                if(displayFontSize < parseInt($('#<?= $_REQUEST['name_w'] ?>_display span').css('font-size').replace('px', '')))
                                {
                                    $("#<?= $_REQUEST['name_w'] ?>_display span").css('font-size', displayFontSize + 'px');
                                }
                                break;

                            case "displayAndText":
                                $('#<?= $_REQUEST['name_w'] ?>_display').css('display', 'flex');
                                $('#<?= $_REQUEST['name_w'] ?>_display span').html(currentValue);
                                $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css('display', 'flex');
                                break;

                            case "all":
                                $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').show();
                                $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css('display', 'flex');
                                $('#<?= $_REQUEST['name_w'] ?>_display').css('display', 'flex');
                                $('#<?= $_REQUEST['name_w'] ?>_display span').html(currentValue);
                                break;
                        }

                        $('#<?= $_REQUEST['name_w'] ?>_onOffButton').off().mousedown(handleMouseDown).mouseup(handleMouseUp);
                    }, 1500);
                    break;
            }
        }

        var onError2 = function(data) {
            $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-refresh').css("display", "none");
            $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-times-circle-o').css("display", "block");
            var errorIconMarginLeft = parseInt(($('#<?= $_REQUEST['name_w'] ?>_onOffButton').width()- $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-times-circle-o').width())/2);
            var errorIconMarginTop = parseInt(($('#<?= $_REQUEST['name_w'] ?>_onOffButton').height()- $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-times-circle-o').height())/2);
            $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-times-circle-o').css("top", errorIconMarginTop + "px");
            $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-times-circle-o').css("left", errorIconMarginLeft + "px");

            setTimeout(function(){
                $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-refresh').css("display", "none");
                $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-times-circle-o').css("display", "none");

                currentValue = baseValue;

                switch(viewMode)
                {
                    case "emptyButton":

                        break;

                    case "iconOnly":
                        $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').show();
                        break;

                    case "textOnly":
                        $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css('display', 'flex');
                        break;

                    case "displayOnly":
                        $('#<?= $_REQUEST['name_w'] ?>_display').css('display', 'flex');
                        $('#<?= $_REQUEST['name_w'] ?>_display span').html(currentValue);
                        break;

                    case "iconAndText":
                        $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').show();
                        $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css('display', 'flex');
                        break;

                    case "iconAndDisplay":
                        $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').show();
                        $('#<?= $_REQUEST['name_w'] ?>_display').css('display', 'flex');
                        $('#<?= $_REQUEST['name_w'] ?>_display span').html(currentValue);
                        $('#<?= $_REQUEST['name_w'] ?>_display').textfill({
                            maxFontPixels: -20
                        });

                        if(displayFontSize < parseInt($('#<?= $_REQUEST['name_w'] ?>_display span').css('font-size').replace('px', '')))
                        {
                            $("#<?= $_REQUEST['name_w'] ?>_display span").css('font-size', displayFontSize + 'px');
                        }
                        break;

                    case "displayAndText":
                        $('#<?= $_REQUEST['name_w'] ?>_display').css('display', 'flex');
                        $('#<?= $_REQUEST['name_w'] ?>_display span').html(currentValue);
                        $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css('display', 'flex');
                        break;

                    case "all":
                        $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').show();
                        $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css('display', 'flex');
                        $('#<?= $_REQUEST['name_w'] ?>_display').css('display', 'flex');
                        $('#<?= $_REQUEST['name_w'] ?>_display span').html(currentValue);
                        break;
                }

                $('#<?= $_REQUEST['name_w'] ?>_onOffButton').off().mousedown(handleMouseDown).mouseup(handleMouseUp);
            }, 1500);
            console.log("Impulse ko:");
            console.log(data);
        }

        function showUpdateResult(msg) {
            //msg = "Pippo";
            if(msg !== "Device OK")
            {
                $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-refresh').css("display", "none");
                $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-times-circle-o').css("display", "block");
                var errorIconMarginLeft = parseInt(($('#<?= $_REQUEST['name_w'] ?>_onOffButton').width()- $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-times-circle-o').width())/2);
                var errorIconMarginTop = parseInt(($('#<?= $_REQUEST['name_w'] ?>_onOffButton').height()- $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-times-circle-o').height())/2);
                $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-times-circle-o').css("top", errorIconMarginTop + "px");
                $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-times-circle-o').css("left", errorIconMarginLeft + "px");

                setTimeout(function(){
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton').toggleClass('onOffButtonActive');
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-refresh').css("display", "none");
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-times-circle-o').css("display", "none");

                    currentValue = baseValue;
                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton').css("background-color", buttonColor);

                    switch(viewMode)
                    {
                        case "emptyButton":

                            break;

                        case "iconOnly":
                            $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').show();
                            $('#<?= $_REQUEST['name_w'] ?>_onOffButton').css("color", symbolClickColor);
                            $('#<?= $_REQUEST['name_w'] ?>_onOffButton').css("text-shadow", symbolOffNeonEffect);
                            break;

                        case "textOnly":
                            $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css('display', 'flex');
                            $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("color", textColor);
                            $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("text-shadow", textOffNeonEffect);
                            break;

                        case "displayOnly":
                            $('#<?= $_REQUEST['name_w'] ?>_display').css('display', 'flex');
                            $('#<?= $_REQUEST['name_w'] ?>_display').css("color", displayFontColor);
                            $('#<?= $_REQUEST['name_w'] ?>_display span').html(currentValue);
                            $('#<?= $_REQUEST['name_w'] ?>_display').css("box-shadow", displayOffNeonEffect);
                            break;

                        case "iconAndText":
                            $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').show();
                            $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css('display', 'flex');
                            $('#<?= $_REQUEST['name_w'] ?>_onOffButton').css("color", symbolClickColor);
                            $('#<?= $_REQUEST['name_w'] ?>_onOffButton').css("text-shadow", symbolOffNeonEffect);
                            $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("color", textColor);
                            $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("text-shadow", "none");
                            break;

                        case "iconAndDisplay":
                            $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').show();
                            $('#<?= $_REQUEST['name_w'] ?>_onOffButton').css("color", symbolClickColor);
                            $('#<?= $_REQUEST['name_w'] ?>_onOffButton i').css("text-shadow", symbolOffNeonEffect);
                            $('#<?= $_REQUEST['name_w'] ?>_display').css('display', 'flex');
                            $('#<?= $_REQUEST['name_w'] ?>_display').css("color", displayFontColor);
                            $('#<?= $_REQUEST['name_w'] ?>_display span').html(currentValue);
                            $('#<?= $_REQUEST['name_w'] ?>_display').textfill({
                                maxFontPixels: -20
                            });

                            if(displayFontSize < parseInt($('#<?= $_REQUEST['name_w'] ?>_display span').css('font-size').replace('px', '')))
                            {
                                $("#<?= $_REQUEST['name_w'] ?>_display span").css('font-size', displayFontSize + 'px');
                            }
                            $('#<?= $_REQUEST['name_w'] ?>_display').css("box-shadow", displayOffNeonEffect);
                            break;

                        case "displayAndText":
                            $('#<?= $_REQUEST['name_w'] ?>_display').css('display', 'flex');
                            $('#<?= $_REQUEST['name_w'] ?>_display').css("color", displayFontColor);
                            $('#<?= $_REQUEST['name_w'] ?>_display span').html(currentValue);
                            $('#<?= $_REQUEST['name_w'] ?>_display').css("box-shadow", displayOffNeonEffect);
                            $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css('display', 'flex');
                            $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("color", textColor);
                            $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("text-shadow", "none");
                            break;

                        case "all":
                            $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').show();
                            $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css('display', 'flex');
                            $('#<?= $_REQUEST['name_w'] ?>_display').css('display', 'flex');
                            $('#<?= $_REQUEST['name_w'] ?>_display span').html(currentValue);
                            $('#<?= $_REQUEST['name_w'] ?>_onOffButton').css("color", symbolColor);
                            $('#<?= $_REQUEST['name_w'] ?>_onOffButton i').css("text-shadow", symbolOnNeonEffect);
                            $('#<?= $_REQUEST['name_w'] ?>_display').css('color', displayFontClickColor);
                            $('#<?= $_REQUEST['name_w'] ?>_display').css("box-shadow", displayOnNeonEffect);
                            $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("color", textClickColor);
                            $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css("text-shadow", "none");
                            break;
                    }

                    $('#<?= $_REQUEST['name_w'] ?>_onOffButton').off().mousedown(handleMouseDown).mouseup(handleMouseUp);
                }, 1500);
            }
            else
            {
                $('#<?= $_REQUEST['name_w'] ?>_onOffButton').off().mousedown(handleMouseDown).mouseup(handleMouseUp);
                $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-refresh').css("display", "none");

                switch(viewMode)
                {
                    case "emptyButton":
                        break;

                    case "iconOnly":
                        $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').show();
                        break;

                    case "textOnly":
                        $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css('display', 'flex');
                        break;

                    case "displayOnly":
                        $('#<?= $_REQUEST['name_w'] ?>_display').css('display', 'flex');
                        $('#<?= $_REQUEST['name_w'] ?>_display span').html(currentValue);
                        break;

                    case "iconAndText":
                        $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').show();
                        $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css('display', 'flex');
                        break;

                    case "iconAndDisplay":
                        $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').show();
                        $('#<?= $_REQUEST['name_w'] ?>_display').css('display', 'flex');
                        $('#<?= $_REQUEST['name_w'] ?>_display span').html(currentValue);
                        $('#<?= $_REQUEST['name_w'] ?>_display').textfill({
                            maxFontPixels: -20
                        });

                        if(displayFontSize < parseInt($('#<?= $_REQUEST['name_w'] ?>_display span').css('font-size').replace('px', '')))
                        {
                            $("#<?= $_REQUEST['name_w'] ?>_display span").css('font-size', displayFontSize + 'px');
                        }
                        break;

                    case "displayAndText":
                        $('#<?= $_REQUEST['name_w'] ?>_display').css('display', 'flex');
                        $('#<?= $_REQUEST['name_w'] ?>_display span').html(currentValue);
                        $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css('display', 'flex');
                        break;

                    case "all":
                        $('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').show();
                        $('#<?= $_REQUEST['name_w'] ?>_display').css('display', 'flex');
                        $('#<?= $_REQUEST['name_w'] ?>_display span').html(currentValue);
                        $('#<?= $_REQUEST['name_w'] ?>_txtContainer').css('display', 'flex');
                        break;
                }
            }
        }

        function resizeWidget(){
				setWidgetLayout(hostFile, widgetName, widgetContentColor, widgetHeaderColor, widgetHeaderFontColor, showHeader, headerHeight, hasTimer);

				if($("#<?= $_REQUEST['name_w'] ?>_chartContainer").width() > $("#<?= $_REQUEST['name_w'] ?>_chartContainer").height())
				{
					minDim = $("#<?= $_REQUEST['name_w'] ?>_chartContainer").height();
					minDimCells = widgetHeightCells;
					minDimName = "height";
				}
				else
				{
					minDim = $("#<?= $_REQUEST['name_w'] ?>_chartContainer").width();
					minDimCells = widgetWidthCells;
					minDimName = "width";
				}
				$('#<?= $_REQUEST['name_w'] ?>_onOffButton').css("border-radius", minDim*onOffButtonRadius/200);
				$('#<?= $_REQUEST['name_w'] ?>_onOffButton').css("font-size", minDim*0.3 + "px");

				switch(viewMode)
				{
					case "emptyButton":
							break;

					case "iconOnly":

						break;

					case "textOnly":
						$('#<?= $_REQUEST['name_w'] ?>_txtContainer').textfill({
							maxFontPixels: -20
						});

						if(textFontSize < parseInt($('#<?= $_REQUEST['name_w'] ?>_txtContainer span').css('font-size').replace('px', '')))
						{
							$("#<?= $_REQUEST['name_w'] ?>_txtContainer span").css('font-size', textFontSize + 'px');
						}
						break;

					case "displayOnly":
						$('#<?= $_REQUEST['name_w'] ?>_display').css('margin-top', parseInt(($('#<?= $_REQUEST['name_w'] ?>_onOffButton').height() - $('#<?= $_REQUEST['name_w'] ?>_display').height())/2) + 'px');
						$('#<?= $_REQUEST['name_w'] ?>_display').textfill({
							maxFontPixels: -20
						});

						if(displayFontSize < parseInt($('#<?= $_REQUEST['name_w'] ?>_display span').css('font-size').replace('px', '')))
						{
							$("#<?= $_REQUEST['name_w'] ?>_display span").css('font-size', displayFontSize + 'px');
						}
						break;

					case "iconAndText":
						$('#<?= $_REQUEST['name_w'] ?>_txtContainer').textfill({
							maxFontPixels: -20
						});

						if(textFontSize < parseInt($('#<?= $_REQUEST['name_w'] ?>_txtContainer span').css('font-size').replace('px', '')))
						{
							$("#<?= $_REQUEST['name_w'] ?>_txtContainer span").css('font-size', textFontSize + 'px');
						}
						break;

					case "iconAndDisplay":
						$('#<?= $_REQUEST['name_w'] ?>_display').textfill({
							maxFontPixels: -20
						});

						if(displayFontSize < parseInt($('#<?= $_REQUEST['name_w'] ?>_display span').css('font-size').replace('px', '')))
						{
							$("#<?= $_REQUEST['name_w'] ?>_display span").css('font-size', displayFontSize + 'px');
						}
						break;

					case "displayAndText":
						$('#<?= $_REQUEST['name_w'] ?>_display').textfill({
							maxFontPixels: -20
						});

						if(displayFontSize < parseInt($('#<?= $_REQUEST['name_w'] ?>_display span').css('font-size').replace('px', '')))
						{
							$("#<?= $_REQUEST['name_w'] ?>_display span").css('font-size', displayFontSize + 'px');
						}

						$('#<?= $_REQUEST['name_w'] ?>_txtContainer').textfill({
							maxFontPixels: -20
						});

						if(textFontSize < parseInt($('#<?= $_REQUEST['name_w'] ?>_txtContainer span').css('font-size').replace('px', '')))
						{
							$("#<?= $_REQUEST['name_w'] ?>_txtContainer span").css('font-size', textFontSize + 'px');
						}
						break;

					case "all":
						$('#<?= $_REQUEST['name_w'] ?>_display').textfill({
							maxFontPixels: -20
						});

						if(displayFontSize < parseInt($('#<?= $_REQUEST['name_w'] ?>_display span').css('font-size').replace('px', '')))
						{
							$("#<?= $_REQUEST['name_w'] ?>_display span").css('font-size', displayFontSize + 'px');
						}

						var txtContainerHeight = $('#<?= $_REQUEST['name_w'] ?>_onOffButton').height() - ($('#<?= $_REQUEST['name_w'] ?>_onOffButton i.fa-power-off').height() + $('#<?= $_REQUEST['name_w'] ?>_display').height() + 0.18*($('#<?= $_REQUEST['name_w'] ?>_onOffButton').height()));
						$('#<?= $_REQUEST['name_w'] ?>_txtContainer').css('height', txtContainerHeight + 'px');

						$('#<?= $_REQUEST['name_w'] ?>_txtContainer').textfill({
							maxFontPixels: -20
						});

						if(textFontSize < parseInt($('#<?= $_REQUEST['name_w'] ?>_txtContainer span').css('font-size').replace('px', '')))
						{
							$("#<?= $_REQUEST['name_w'] ?>_txtContainer span").css('font-size', textFontSize + 'px');
						}
						break;
				}
		}
        //Fine definizioni di funzione

        setWidgetLayout(hostFile, widgetName, widgetContentColor, widgetHeaderColor, widgetHeaderFontColor, showHeader, headerHeight, hasTimer);
        $('#<?= $_REQUEST['name_w'] ?>_div').parents('li.gs_w').off('resizeWidgets');
        $('#<?= $_REQUEST['name_w'] ?>_div').parents('li.gs_w').on('resizeWidgets', resizeWidget);
        if(firstLoad === false)
        {
            showWidgetContent(widgetName);
        }
        else
        {
            setupLoadingPanel(widgetName, widgetContentColor, firstLoad);
        }

        //$("#<?= $_REQUEST['name_w'] ?>_titleDiv").html(widgetTitle);

        $.ajax({
            url: getParametersWidgetUrl,
            type: "GET",
            data: {"nomeWidget": [widgetName]},
            async: true,
            dataType: 'json',
            success: function (data)
            {
                widgetProperties = data;
                if((widgetProperties !== null) && (widgetProperties !== ''))
                {
                    dashboardId = widgetProperties.param.id_dashboard;
                    styleParameters = getStyleParameters();
                    widgetParameters = JSON.parse(widgetProperties.param.parameters);
                    sizeRowsWidget = parseInt(widgetProperties.param.size_rows);
                    widgetWidthCells = parseInt(widgetProperties.param.size_columns);
                    widgetHeightCells = parseInt(widgetProperties.param.size_rows);
                    onOffButtonRadius = parseInt(styleParameters.buttonRadius);
                    fontFamily = widgetProperties.param.fontFamily;
                    buttonColor = styleParameters.color;
                    buttonClickColor = styleParameters.clickColor;
                    actuatorTarget = widgetProperties.param.actuatorTarget;
                    code = widgetProperties.param.code;
                    if(actuatorTarget === 'broker')
                    {
                        entityJson = widgetProperties.param.entityJson;
                        attributeName = widgetProperties.param.attributeName;
                        if (entityJson && attributeName)
                            dataType = JSON.parse(entityJson)[attributeName].type;
                        targetEntity = widgetParameters.targetEntity;
                        targetEntityAttribute = widgetParameters.targetEntityAttribute;
                        impulseValue = widgetParameters.impulseValue;
                        baseValue = widgetParameters.baseValue;
                    }
                    else
                    {
                        nrInputId = widgetProperties.param.nrInputId;
                        nodeRedInputName = widgetProperties.param.name;
                        dataType = widgetProperties.param.valueType;
                        baseValue = widgetProperties.param.offValue;
                        impulseValue = widgetProperties.param.onValue;
                        username = widgetProperties.param.creator;
                        endPointHost = widgetProperties.param.endPointHost;
                        endPointPort = widgetProperties.param.endPointPort;
                        if(useWebSocket)
                            openWs(widgetName);
                    }

                    switch(dataType)
                    {
                        case "Integer": case "integer":
                            currentValue = parseInt(widgetProperties.param.currentValue);
                            baseValue = parseInt(baseValue);
                            impulseValue = parseInt(impulseValue);
                            break;

                        case "Float": case "float":
                            currentValue = parseFloat(widgetProperties.param.currentValue);
                            baseValue = parseFloat(baseValue);
                            impulseValue = parseFloat(impulseValue);
                            break;

                        case "String": case "string":
                            currentValue = widgetProperties.param.currentValue;
                            break;

                        case "Boolean": case "boolean":
                            if((widgetProperties.param.currentValue === true)||(widgetProperties.param.currentValue === 'true')||(widgetProperties.param.currentValue === 'True'))
                            {
                                currentValue = true;
                            }
                            else
                            {
                                currentValue = false;
                            }
                            baseValue = Boolean(baseValue);
                            impulseValue = Boolean(impulseValue);
                            break;
                    }

                    impulseMode = widgetParameters.impulseMode;
                    viewMode = styleParameters.viewMode;
                    symbolColor = styleParameters.symbolColor;
                    symbolClickColor = styleParameters.symbolClickColor;
                    textClickColor = styleParameters.textClickColor;
                    textColor = styleParameters.textColor;
                    textFontSize = styleParameters.textFontSize;
                    displayFontSize = styleParameters.displayFontSize;
                    displayFontColor = styleParameters.displayFontColor;
                    displayFontClickColor = styleParameters.displayFontClickColor;
                    displayRadius = styleParameters.displayRadius;
                    displayColor = styleParameters.displayColor;
                    displayWidth = styleParameters.displayWidth;
                    displayHeight = styleParameters.displayHeight;
                    symbolOnNeonEffectSetting = styleParameters.neonEffect;

                    switch(symbolOnNeonEffectSetting)
                    {
                        case "never":
                            symbolOnNeonEffect = "none";
                            symbolOffNeonEffect = "none";
                            textOnNeonEffect = "none";
                            textOffNeonEffect = "none";
                            displayOffNeonEffect = "none";
                            displayOnNeonEffect = "none";
                            break;

                        case "onStatus":
                            symbolOnNeonEffect = "0 0 1px #fff, 0 0 2px #fff, 0 0 4px " + symbolColor + ", 0 0 8px " + symbolColor + ", 0 0 14px " + symbolColor + ", 0 0 18px ";
                            symbolOffNeonEffect = "none";
                            textOnNeonEffect = "0 0 2px #fff, 0 0 4px #fff, 0 0 6px #fff, 0 0 8px " + textClickColor + ", 0 0 14px " + textClickColor + ", 0 0 20px " + textClickColor + ", 0 0 24px " + textClickColor + ", 0 0 28px " + textClickColor;
                            textOffNeonEffect = "none";
                            displayOnNeonEffect = displayFontClickColor + " 2px 2px 16px, " + displayFontClickColor + " 2px -2px 16px, " + displayFontClickColor + " -2px 2px 16px, " + displayFontClickColor + " -2px -2px 16px";
                            displayOffNeonEffect = "none";
                            break;

                        case "offStatus":
                            symbolOnNeonEffect = "none";
                            symbolOffNeonEffect = "0 0 1px #fff, 0 0 2px #fff, 0 0 4px " + symbolClickColor + ", 0 0 8px " + symbolClickColor + ", 0 0 14px " + symbolClickColor + ", 0 0 18px ";
                            textOnNeonEffect = "none";
                            textOffNeonEffect = "0 0 2px #fff, 0 0 4px #fff, 0 0 6px #fff, 0 0 8px " + textColor + ", 0 0 14px " + textColor + ", 0 0 20px " + textColor + ", 0 0 24px " + textColor + ", 0 0 28px " + textColor;
                            displayOnNeonEffect = "none";
                            displayOffNeonEffect = displayFontColor + " 2px 2px 16px, " + displayFontColor + " 2px -2px 16px, " + displayFontColor + " -2px 2px 16px, " + displayFontColor + " -2px -2px 16px";
                            break;

                        case "always":
                            symbolOnNeonEffect = "0 0 1px #fff, 0 0 2px #fff, 0 0 4px " + symbolColor + ", 0 0 8px " + symbolColor + ", 0 0 14px " + symbolColor + ", 0 0 18px ";
                            symbolOffNeonEffect = "0 0 1px #fff, 0 0 2px #fff, 0 0 4px " + symbolClickColor + ", 0 0 8px " + symbolClickColor + ", 0 0 14px " + symbolClickColor + ", 0 0 18px ";
                            textOnNeonEffect = "0 0 2px #fff, 0 0 4px #fff, 0 0 6px #fff, 0 0 8px " + textClickColor + ", 0 0 14px " + textClickColor + ", 0 0 20px " + textClickColor + ", 0 0 24px " + textClickColor + ", 0 0 28px " + textClickColor;
                            textOffNeonEffect = "0 0 2px #fff, 0 0 4px #fff, 0 0 6px #fff, 0 0 8px " + textColor + ", 0 0 14px " + textColor + ", 0 0 20px " + textColor + ", 0 0 24px " + textColor + ", 0 0 28px " + textColor;
                            displayOnNeonEffect = displayFontClickColor + " 2px 2px 16px, " + displayFontClickColor + " 2px -2px 16px, " + displayFontClickColor + " -2px 2px 16px, " + displayFontClickColor + " -2px -2px 16px";
                            displayOffNeonEffect = displayFontColor + " 2px 2px 16px, " + displayFontColor + " 2px -2px 16px, " + displayFontColor + " -2px 2px 16px, " + displayFontColor + " -2px -2px 16px";
                            break;
                    }
                    $('#<?= $_REQUEST['name_w'] ?>_infoButtonDiv i.gisDriverPin').hide();
                    $('#<?= $_REQUEST['name_w'] ?>_infoButtonDiv a.info_source').show();

                    if (widgetProperties.param.code != null && widgetProperties.param.code != "null") {
                        let code = widgetProperties.param.code;
                        var text_ck_area = document.createElement("text_ck_area");
                        text_ck_area.innerHTML = code;
                        var newInfoDecoded = text_ck_area.innerText;
                        newInfoDecoded = newInfoDecoded.replaceAll("function execute()","function execute_" + "<?= $_REQUEST['name_w'] ?>()");

                        var elem = document.createElement('script');
                        elem.type = 'text/javascript';
                        // elem.id = "<?= $_REQUEST['name_w'] ?>_code";
                        // elem.src = newInfoDecoded;
                        elem.innerHTML = newInfoDecoded;
                        $('#<?= $_REQUEST['name_w'] ?>_code').append(elem);

                        $('#<?= $_REQUEST['name_w'] ?>_code').css("display", "none");
                    }

                    populateWidget();
                }
                else
                {
                    console.log("Errore in caricamento proprietà widget");
                    showWidgetContent(widgetName);
                    if(firstLoad !== false)
                    {
                        $("#<?= $_REQUEST['name_w'] ?>_chartContainer").hide();
                        $('#<?= $_REQUEST['name_w'] ?>_noDataAlert').show();
                    }
                }
            },
            error: function(errorData)
            {
               console.log("Errore in caricamento proprietà widget");
               console.log(JSON.stringify(errorData));
               showWidgetContent(widgetName);
               if(firstLoad !== false)
               {
                  $("#<?= $_REQUEST['name_w'] ?>_chartContainer").hide();
                  $('#<?= $_REQUEST['name_w'] ?>_noDataAlert').show();
               }
            },
            complete: function()
            {

            }
        });

        $(document).off('resizeHighchart_' + widgetName);
        $(document).on('resizeHighchart_' + widgetName, function(event)
        {
            showHeader = event.showHeader;
            populateWidget();
        });

        $("#<?= $_REQUEST['name_w'] ?>").on('customResizeEvent', function(event){
            resizeWidget();
        });

        //Web socket

        var openWs = function(widget)
        {
            try
            {
                <?php
                    echo 'wsRetryActive = "' . $wsRetryActive . '";'."\n";
                    echo 'wsRetryTime = ' . $wsRetryTime . ';'."\n";
                    echo 'wsUrl="' . $wsProtocol . '://' . $wsServerAddress . ':' . $wsServerPort . '/' . $wsPath . '";'."\n";
                ?>
                //webSocket = new WebSocket(wsUrl);
                initWebsocket(widget, wsUrl, null, wsRetryTime*1000, function(socket){
                    console.log('socket initialized!');
                    //do something with socket...
                    //Window.webSockets["<?= $_REQUEST['name_w'] ?>"] = socket;
                    openWsConn(widget);
                }, function(){
                    console.log('init of socket failed!');
                });
                /*webSocket.addEventListener('open', openWsConn);
                webSocket.addEventListener('close', wsClosed);*/
            }
            catch(e)
            {
                wsClosed();
            }
        };

        var manageIncomingWsMsg = function(msg)
        {
            var msgObj = JSON.parse(msg.data);
            console.log(msgObj);
            if(msgObj.msgType=="DataToEmitterAck") {
              var webSocket = Window.webSockets[msgObj.widgetUniqueName];
              if(! webSocket.ackReceived) {
                clearTimeout(webSocket.timeout);
                webSocket.ackReceived = true;
                console.log(msgObj.widgetUniqueName+" ACK ackReceived:"+webSocket.ackReceived)
                webSocket.onAck({result:"Ok", widgetName:msgObj.widgetUniqueName});
              }
            }
        };

        timeToReload=200;
        var openWsConn = function(widget) {
            var webSocket = Window.webSockets[widget];
            /*setTimeout(function(){
                var webSocket = Window.webSockets[widget];
                webSocket.removeEventListener('message', manageIncomingWsMsg);
                webSocket.close();
            }, (timeToReload - 2)*1000);*/

            webSocket.addEventListener('message', manageIncomingWsMsg);
        };

        var wsClosed = function(e)
        {
            var webSocket = Window.webSockets["<?= $_REQUEST['name_w'] ?>"];
            webSocket.removeEventListener('message', manageIncomingWsMsg);
            if(wsRetryActive === 'yes')
            {
                setTimeout(openWs, parseInt(wsRetryTime*1000));
            }
        };

        function initWebsocket(widget, url, existingWebsocket, retryTimeMs, success, failed) {
          if (!existingWebsocket || existingWebsocket.readyState != existingWebsocket.OPEN) {
              if (existingWebsocket) {
                  existingWebsocket.close();
              }
              var websocket = new WebSocket(url);
              websocket.widget = widget;
              console.log("store websocket for "+widget)
              Window.webSockets[widget] = websocket;
              websocket.onopen = function () {
                  console.info('websocket opened! url: ' + url);
                  success(websocket);
              };
              websocket.onclose = function () {
                  console.info('websocket closed! url: ' + url + " reconnect in "+retryTimeMs+"ms");
                  //reconnect after a retryTime
                  setTimeout(function(){
                    initWebsocket(widget, url, existingWebsocket, retryTimeMs, success, failed);
                  }, retryTimeMs);
              };
              websocket.onerror = function (e) {
                  console.info('websocket error! url: ' + url);
                  console.info(e);
              };
          } else {
              success(existingWebsocket);
          }

          return;
      };

/*
        function initWebsocket(widget, url, existingWebsocket, timeoutMs, numberOfRetries) {
          timeoutMs = timeoutMs ? timeoutMs : 1500;
          numberOfRetries = numberOfRetries ? numberOfRetries : 0;
          var hasReturned = false;
          var promise = new Promise((resolve, reject) => {
              setTimeout(function () {
                  if(!hasReturned) {
                      console.info('opening websocket timed out: ' + url);
                      rejectInternal();
                  }
              }, timeoutMs);
              if (!existingWebsocket || existingWebsocket.readyState != existingWebsocket.OPEN) {
                  if (existingWebsocket) {
                      existingWebsocket.close();
                  }
                  var websocket = new WebSocket(url);
                  websocket.widget = widget;
                  console.log("store websocket for "+widget)
                  Window.webSockets[widget] = websocket;
                  websocket.onopen = function () {
                      if(hasReturned) {
                          websocket.close();
                      } else {
                          console.info('websocket to opened! url: ' + url);
                          resolve(websocket);
                      }
                  };
                  websocket.onclose = function () {
                      console.info('websocket closed! url: ' + url);
                      hasReturned = false;
                      rejectInternal();
                  };
                  websocket.onerror = function () {
                      console.info('websocket error! url: ' + url);
                      rejectInternal();
                  };
              } else {
                  resolve(existingWebsocket);
              }

              function rejectInternal() {
                  if(numberOfRetries <= 0) {
                      console.log("reject "+numberOfRetries)
                      reject();
                  } else if(!hasReturned) {
                      hasReturned = true;
                      console.info('retrying connection to websocket! url: ' + url + ', remaining retries: ' + (numberOfRetries-1));
                      initWebsocket(widget, url, null, timeoutMs, numberOfRetries-1).then(resolve, reject);
                  }
              }
          });
          promise.then(function () {hasReturned = true;}, function () {hasReturned = true;});
          return promise;
      };
*/
});//Fine document ready
</script>

<div class="widget" id="<?= $_REQUEST['name_w'] ?>_div">
    <div class='ui-widget-content'>
        <?php include '../widgets/widgetHeader.php'; ?>
        <?php include '../widgets/widgetCtxMenu.php'; ?>

        <div id="<?= $_REQUEST['name_w'] ?>_loading" class="loadingDiv">
            <div class="loadingTextDiv">
                <p>Loading data, please wait</p>
            </div>
            <div class ="loadingIconDiv">
                <i class='fa fa-spinner fa-spin'></i>
            </div>
        </div>

        <div id="<?= $_REQUEST['name_w'] ?>_content" class="content">
            <?php include '../widgets/commonModules/widgetDimControls.php'; ?>
            <div id="<?= $_REQUEST['name_w'] ?>_noDataAlert" class="noDataAlert">
                <div id="<?= $_REQUEST['name_w'] ?>_noDataAlertText" class="noDataAlertText">
                    No data available
                </div>
                <div id="<?= $_REQUEST['name_w'] ?>_noDataAlertIcon" class="noDataAlertIcon">
                    <i class="fa fa-times"></i>
                </div>
            </div>
            <div id="<?= $_REQUEST['name_w'] ?>_chartContainer" ondragstart="return false;" ondrop="return false;" class="chartContainer">
                <div id="<?= $_REQUEST['name_w'] ?>_onOffButton" class="onOffButton">
                    <div id="<?= $_REQUEST['name_w'] ?>_onOffButtonBefore" class="onOffButtonBefore"></div>
                    <i class="fa fa-power-off"></i>
                    <i class="fa fa-refresh fa-spin"></i>
                    <i class="fa fa-times-circle-o"></i>
                    <div id="<?= $_REQUEST['name_w'] ?>_txtContainer" class="onOffButtonTxtContainer centerWithFlex">
                        <span></span>
                    </div>
                    <div id="<?= $_REQUEST['name_w'] ?>_display" class="onOffButtonDisplay centerWithFlex">
                        <span></span>
                    </div>
                    <div id="<?= $_REQUEST['name_w'] ?>_onOffButtonAfter" class="onOffButtonAfter"></div>
                    <div id="<?= $_REQUEST['name_w'] ?>_code"></div>
                </div>
            </div>
        </div>
    </div>	
</div> 
