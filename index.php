<?php
/* latest JSON file with names of radar images, times etc. (updated every 5 minutes)
Path to transparent png radar image with datetime url:
http://www.meteo.si/uploads/probase/www/nowcast/inca/inca_si0zm_20210706-0830+0000.png
http://www.meteo.si/uploads/probase/www/nowcast/inca/inca_si0zm_20210706-0835+0000.png
http://www.meteo.si/uploads/probase/www/nowcast/inca/inca_si0zm_20210706-0840+0000.png
http://www.meteo.si/uploads/probase/www/nowcast/inca/inca_si0zm_20210706-0845+0000.png
 */

$filename = 'https://www.meteolab.si/work/radar/si0-zm.json';
$json = file_get_contents($filename);
$data = json_decode($json, true);
$last = end($data);
// now echo
?>
<!DOCTYPE html>
<html>

<head>
  <title>OSM map</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <!-- Latest compiled and minified CSS -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <!-- jQuery library -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

  <!-- Latest compiled JavaScript -->
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

  <link rel="stylesheet" href="new.css">
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" integrity="sha512-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC5CXdbqCmblAshOMAS6/keqq/sMZMZ19scR4PsZChSR7A==" crossorigin="" />
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.js" integrity="sha512-n/4gHW3atM3QqRcbCn6ewmpxcLAHGaDjpEBu4xZd47N0W2oQ+6q7oc3PXstrJYXcbNU1OHdQ1T7pAP+gi5Yu8g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
  <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js" integrity="sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA==" crossorigin=""></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet-easybutton@2/src/easy-button.css">
  <script src="https://cdn.jsdelivr.net/npm/leaflet-easybutton@2/src/easy-button.js"></script>
  <script src="new.js"></script>

</head>

<body>
  <div id="info" class="row">
    <div class="col-md-12 ">
      <div class="form-group">
        <b>Select Day:</b>
        <select class="form-control formControlWIdth" name="datedp" id="fpdropdown">
          <option selected disabled value="0" style="width:100%;">--Filter Data by Date & Time--</option>

        </select>
      </div>
      <div class="form-group">
        <b>Parameter:</b>
        <div id="param"></div>
      </div>
      <div class="form-group">
        <b>Radar Layer:</b>
        <div id="parentRadarInfo">
          <div id="radarInfo"></div>
        </div>
      </div>
    </div>
  </div>

  <div id="map"></div>
  <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet-groupedlayercontrol/0.6.1/leaflet.groupedlayercontrol.js" integrity="sha512-2OAO6Vw7QqbRSoHqfdIhur/B7urhzltUGHOufhmGJRScSz8S0ZUyBp1ixI9BB0pLXIKqyQZ/cOwS4PgBTviT6Q==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet-groupedlayercontrol/0.6.1/leaflet.groupedlayercontrol.css" integrity="sha512-PpKEvRG//V8hN9idekL4WOjknpMTPFH3MnWpVbVBmlzXpoUfbBSr054U/TUmOzUnCOM9PAPiLhRgq0i00B4q3w==" crossorigin="anonymous" referrerpolicy="no-referrer" /> -->
  <script src="leaflet-rainfall.js"></script>
  <script src="index.js"></script>
</body>

</html>