INSERT INTO `widgets` (`id`, `id_type_widget`, `source_php_widget`, `min_row`, `max_row`, `min_col`, `max_col`, `widgetType`, `unique_metric`, `numeric_rangeOption`, `number_metrics_widget`, `color_widgetOption`, `dimMap`, `widgetCategory`, `isNodeRedSender`, `domainType`, `defaultParameters`, `hasTimer`, `hasChartColor`, `hasDataLabels`, `hasChartLabels`, `hasTimeRange`, `hasCartesianPlane`, `hasChangeMetric`, `hasAddMode`) VALUES
(59, 'widgetGisWFS', 'widgetGisWFS.php', 1, 50, 1, 50, 'Testuale', 'GisWFS', 0, 1, 1, NULL, 'dataViewer', 'no', '[\'webContent\']', '{}', 'no', 'no', 'no', 'no', 'no', 'no', 'no', 'no');

INSERT INTO `widgetsiconsmap` (`id`, `mainWidget`, `targetWidget`, `snap4CityType`, `icon`, `mono_multi`, `description`, `available`, `sm_based`, `defaultParametersMainWidget`, `defaultParametersTargetWidget`, `hasMainWidgetFactory`, `hasTargetWidgetFactory`, `comboName`, `widgetCategory`) VALUES
(74, 'widgetSelector', 'widgetGisWFS', 'wfs', 'selector-widget_WFS.png', 'Multi', 'Widget showing a list of point of interests categories with a map showing the position of the POIs, a set of sources have to be provided. Using OpenLayers and ArcGIS WFS.', 'true', 'yes', '{\r\n	"actuatorAttribute" : null,\r\n	"actuatorEntity" : null,\r\n	"actuatorTarget" : null,\r\n	"attributeName" : null,\r\n	"cancelDate" : null,\r\n	"canceller" : null,\r\n	"chartColor" : null,\r\n	"chartLabelsFontColor" : "#000000",\r\n	"chartLabelsFontSize" : 12,\r\n	"color_w" : "#FFFFFF",\r\n	"controlsPosition" : null,\r\n	"controlsVisibility" : null,\r\n	"dataLabelsFontColor" : "#000000",\r\n	"dataLabelsFontSize" : 12,\r\n	"defaultTab" : null,\r\n	"enableFullscreenModal" : "no",\r\n	"enableFullscreenTab" : "no",\r\n	"entityJson" : null,\r\n	"fontColor" : null,\r\n	"fontFamily" : "Auto",\r\n	"fontSize" : 16,\r\n	"frame_color_w" : "rgb(51, 204, 255)",\r\n	"frequency_w" : 1200,\r\n	"headerFontColor" : "#FFFFFF",\r\n	"hospitalList" : null,\r\n	"infoJson" : null,\r\n	"infoMessage_w" : null,\r\n	"lastEditDate" : null,\r\n	"lastSeries" : null,\r\n	"link_w" : "none",\r\n	"municipality_w" : null,\r\n	"notificatorEnabled" : "no",\r\n	"notificatorRegistered" : "no",\r\n	"oldParameters" : null,\r\n	"parameters" : "{\\"queries\\":[],\\"targets\\":[]}",\r\n	"scaleX" : null,\r\n	"scaleY" : null,\r\n	"serviceUri" : null,\r\n	"showTitle" : "yes",\r\n	"size_columns" : 4,\r\n	"size_rows" : 10,\r\n	"styleParameters" : "{\\"activeFontColor\\":\\"rgba(0,0,0,1)\\"}",\r\n	"temporal_range_w" : null,\r\n	"udm" : null,\r\n	"udmPos" : null,\r\n	"viewMode" : "list",\r\n	"zoomControlsColor" : null,\r\n	"zoomFactor" : null\r\n}', '[{\r\n	"actuatorAttribute" : null,\r\n	"actuatorEntity" : null,\r\n	"actuatorTarget" : null,\r\n	"attributeName" : null,\r\n	"cancelDate" : null,\r\n	"canceller" : null,\r\n	"chartColor" : null,\r\n	"chartLabelsFontColor" : null,\r\n	"chartLabelsFontSize" : null,\r\n	"color_w" : "#FFFFFF",\r\n	"controlsPosition" : "topLeft",\r\n	"controlsVisibility" : "alwaysVisible",\r\n	"dataLabelsFontColor" : null,\r\n	"dataLabelsFontSize" : null,\r\n	"defaultTab" : null,\r\n	"enableFullscreenModal" : "yes",\r\n	"enableFullscreenTab" : "yes",\r\n	"entityJson" : null,\r\n	"fontColor" : "#ffffff",\r\n	"fontFamily" : "Auto",\r\n	"fontSize" : null,\r\n	"frame_color_w" : "rgb(51, 204, 255)",\r\n	"frequency_w" : null,\r\n	"headerFontColor" : "#FFFFFF",\r\n	"hospitalList" : null,\r\n	"infoJson" : null,\r\n	"infoMessage_w" : null,\r\n	"lastEditDate" : null,\r\n	"lastSeries" : null,\r\n	"link_w" : "gisTarget",\r\n	"municipality_w" : null,\r\n	"notificatorEnabled" : "no",\r\n	"notificatorRegistered" : "no",\r\n	"oldParameters" : null,\r\n	"parameters" : null,\r\n	"scaleX" : 1,\r\n	"scaleY" : 1,\r\n	"serviceUri" : null,\r\n	"showTitle" : "yes",\r\n	"size_columns" : 10,\r\n	"size_rows" : 10,\r\n	"styleParameters" : null,\r\n	"temporal_range_w" : null,\r\n	"udm" : null,\r\n	"udmPos" : null,\r\n	"viewMode" : "map",\r\n	"zoomControlsColor" : null,\r\n	"zoomFactor" : 1\r\n}]', 'yes', '["yes"]', 'SelectorAndGisWFS', 'dataViewer');

INSERT INTO `descriptions` (`id`, `IdMetric`, `description`, `status`, `query`, `query2`, `queryType`, `metricType`, `frequency`, `processType`, `area`, `source`, `description_short`, `dataSource`, `storingData`, `municipalityOption`, `timeRangeOption`, `field1Desc`, `field2Desc`, `field3Desc`, `oldData`, `sameDataAlarmCount`, `oldDataEvalTime`, `hasNegativeValues`, `process`, `threshold`, `thresholdEval`, `boundToMetric`, `status_HTTPRetr`, `username_HTTPRetr`, `password_HTTPRetr`) VALUES
(347, 'GisWFS', 'Visualizzazione di contenuti provenienti da siti esterni.', 'Non attivo', NULL, NULL, 'none', 'Testuale', '60000', 'API', 'Contenuti Esterni', 'Disit', 'Visualizzazione contenuti provenienti da siti esterni', 'none', 1, 0, 0, NULL, NULL, NULL, 0, NULL, NULL, 0, 'DashboardProcess', NULL, NULL, NULL, 'Non Attivo', NULL, NULL);