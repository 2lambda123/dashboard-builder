<?php
/* Dashboard Builder.
   Copyright (C) 2017 DISIT Lab https://www.disit.org - University of Florence

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
   include('../config.php');
   header("Cache-Control: private, max-age=$cacheControlMaxAge");
?>

<script type='text/javascript'>
    var colors = {
      GREEN: '#008800',
      ORANGE: '#FF9933',
      LOW_YELLOW: '#ffffcc',
      RED: '#FF0000'
    };
    var parameters = {};

    $(document).ready(function <?= $_GET['name'] ?>(firstLoad, metricNameFromDriver, widgetTitleFromDriver, widgetHeaderColorFromDriver, widgetHeaderFontColorFromDriver, fromGisExternalContent, fromGisExternalContentServiceUri, fromGisExternalContentField, fromGisExternalContentRange, /*randomSingleGeoJsonIndex,*/ fromGisMarker, fromGisMapRef) 
    {
        <?php
            $link = mysqli_connect($host, $username, $password);
            if (checkWidgetNameInDashboard($link, $_REQUEST['name_w'], $_REQUEST['id_dashboard']) === false) {
                eventLog("Returned the following ERROR in widgetTemplate.php for the widget ".escapeForHTML($_REQUEST['name_w'])." is not instantiated or allowed in this dashboard.");
                exit();
            }
        ?> 
        console.log("ENTRA IN WIDGET TEMPLATE !!!");
        var hostFile = "<?= escapeForJS($_GET['hostFile']) ?>";
        var widgetName = "<?= escapeForJS($_GET['name']) ?>";
        var divContainer = $("#<?= $_GET['name'] ?>_content");
        var widgetContentColor = "<?= escapeForJS($_GET['color']) ?>";
        var widgetHeaderColor = "<?= escapeForJS($_GET['frame_color']) ?>";
        var widgetHeaderFontColor = "<?= escapeForJS($_GET['headerFontColor']) ?>";
        var nome_wid = "<?= $_GET['name'] ?>_div";
        var linkElement = $('#<?= $_GET['name'] ?>_link_w');
        var color = '<?= escapeForJS($_GET['color']) ?>';
        var fontSize = "<?= escapeForJS($_GET['fontSize']) ?>";
        var fontColor = "<?= escapeForJS($_GET['fontColor']) ?>";
        var timeToReload = <?= escapeForJS($_GET['freq']) ?>;
        var widgetPropertiesString, widgetProperties, thresholdObject, infoJson, styleParameters, metricType, metricData, pattern, totValues, shownValues, 
            descriptions, udm, threshold, thresholdEval, stopsArray, delta, deltaPerc, seriesObj, dataObj, pieObj, legendLength,
            rangeMin, rangeMax, widgetParameters = null;
        var metricId = "<?= escapeForJS($_GET['metric']) ?>";
        var elToEmpty = $("#<?= $_GET['name'] ?>_chartContainer");
        var url = "<?= escapeForJS($_GET['link_w']) ?>";
        var barColors = new Array();
        var embedWidget = <?= $_GET['embedWidget']=='true'?'true':'false' ?>;
        var embedWidgetPolicy = '<?= escapeForJS($_GET['embedWidgetPolicy']) ?>';	
        var headerHeight = 25;
        var hasTimer = "<?= escapeForJS($_REQUEST['hasTimer']) ?>";
        if(url === "null")
        {
            url = null;
        }
        
        if((embedWidget === true) && (embedWidgetPolicy === 'auto'))
        {
            var showHeader = false;
        }
        else
        {
            //TBD - Vanno gestiti i futuri casi di policy manuale e show/hide header a scelta utente
            var showHeader = true;
        }
        
        //Definizioni di funzione specifiche del widget
        function resizeWidget()
        {
            setWidgetLayout(hostFile, widgetName, widgetContentColor, widgetHeaderColor, widgetHeaderFontColor, showHeader, headerHeight, hasTimer);
        }
        //Fine definizioni di funzione 
        
        setWidgetLayout(hostFile, widgetName, widgetContentColor, widgetHeaderColor, widgetHeaderFontColor, showHeader, headerHeight, hasTimer);
        $('#<?= $_GET['name'] ?>_div').parents('li.gs_w').off('resizeWidgets');
        $('#<?= $_GET['name'] ?>_div').parents('li.gs_w').on('resizeWidgets', resizeWidget);
        
        if(firstLoad === false)
        {
            showWidgetContent(widgetName);
        }
        else
        {
            setupLoadingPanel(widgetName, widgetContentColor, firstLoad);
        }
        console.log("ARRIVA IN WIDGETTEMPLATE !!!!");
        addLink(widgetName, url, linkElement, divContainer, null);
        $("#<?= $_GET['name'] ?>_titleDiv").html("<?= sanitizeTitle($_REQUEST['title_w']) ?>");
        widgetProperties = getWidgetProperties(widgetName);
        
        if((widgetProperties !== null) && (widgetProperties !== 'undefined'))
        {
            //Inizio eventuale codice ad hoc basato sulle proprietà del widget
            styleParameters = getStyleParameters();
            widgetParameters = widgetProperties.param.parameters;
            manageInfoButtonVisibility(widgetProperties.param.infoMessage_w, $('#<?= $_GET['name'] ?>_header'));

            if(widgetParameters !== null)
            {
                //Codice da inserire nel caso si vogliano usare i parametri del widget
            }
            else 
            {
                //Codice da inserire nel caso si vogliano usare i parametri del widget ma essi sono assenti
            }
            
            //Fine eventuale codice ad hoc basato sulle proprietà del widget
            metricData = getMetricData(metricId);
            if(metricData !== null)
            {
                if(metricData.data[0] !== 'undefined')
                {
                    if(metricData.data.length > 0)
                    {
                        //Inizio eventuale codice ad hoc basato sui dati della metrica
                        var metricType = metricData.data[0].commit.author.metricType;
                        
                        
                        
                        
                        
                        if(firstLoad !== false)
                        {
                            showWidgetContent(widgetName);
                        }
                        else
                        {
                            elToEmpty.empty();
                        }
                        //Fine eventuale codice ad hoc basato sui dati della metrica
                    }
                    else
                    {
                        showWidgetContent(widgetName);
                        $('#<?= $_GET['name'] ?>_noDataAlert').show();
                    }  
                }
                else
                {
                    showWidgetContent(widgetName);
                    $('#<?= $_GET['name'] ?>_noDataAlert').show();
                }
            }
            else
            {
                showWidgetContent(widgetName);
                $('#<?= $_GET['name'] ?>_noDataAlert').show();
            } 
        }
        else
        {
            console.log("Errore in caricamento proprietà widget");
        }
        startCountdown(widgetName, timeToReload, <?= $_GET['name'] ?>, metricNameFromDriver, widgetTitleFromDriver, widgetHeaderColorFromDriver, widgetHeaderFontColorFromDriver, fromGisExternalContent, fromGisExternalContentServiceUri, fromGisExternalContentField, fromGisExternalContentRange, /*randomSingleGeoJsonIndex,*/ fromGisMarker, fromGisMapRef);
    });//Fine document ready
</script>

<div class="widget" id="<?= $_GET['name'] ?>_div">
    <div class='ui-widget-content'>
        <div id='<?= $_GET['name'] ?>_header' class="widgetHeader">
            <div id="<?= $_GET['name'] ?>_infoButtonDiv" class="infoButtonContainer">
               <a id ="info_modal" href="#" class="info_source"><i id="source_<?= $_GET['name'] ?>" class="source_button fa fa-info-circle" style="font-size: 22px"></i></a>
            </div>    
            <div id="<?= $_GET['name'] ?>_titleDiv" class="titleDiv"></div>
            <div id="<?= $_GET['name'] ?>_buttonsDiv" class="buttonsContainer">
                <div class="singleBtnContainer"><a class="icon-cfg-widget" href="#"><span class="glyphicon glyphicon-cog glyphicon-modify-widget" aria-hidden="true"></span></a></div>
                    <div class="singleBtnContainer"><a class="icon-remove-widget" href="#"><span class="glyphicon glyphicon-remove glyphicon-modify-widget" aria-hidden="true"></span></a></div>
            </div>
            <div id="<?= $_GET['name'] ?>_countdownContainerDiv" class="countdownContainer">
                <div id="<?= $_GET['name'] ?>_countdownDiv" class="countdown"></div> 
            </div>   
        </div>
        
        <div id="<?= $_GET['name'] ?>_loading" class="loadingDiv">
            <div class="loadingTextDiv">
                <p>Loading data, please wait</p>
            </div>
            <div class ="loadingIconDiv">
                <i class='fa fa-spinner fa-spin'></i>
            </div>
        </div>
        
        <div id="<?= $_GET['name'] ?>_content" class="content">
            <p id="<?= $_GET['name'] ?>_noDataAlert" style='text-align: center; font-size: 18px; display:none'>Nessun dato disponibile</p>
            <div id="<?= $_GET['name'] ?>_chartContainer" class="chartContainer"></div>
        </div>
    </div>	
</div> 