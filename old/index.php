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

<!-- jQuery library -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

<!-- Latest compiled JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

<link rel="stylesheet" href="new.css">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" integrity="sha512-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC5CXdbqCmblAshOMAS6/keqq/sMZMZ19scR4PsZChSR7A==" crossorigin=""/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.js" integrity="sha512-n/4gHW3atM3QqRcbCn6ewmpxcLAHGaDjpEBu4xZd47N0W2oQ+6q7oc3PXstrJYXcbNU1OHdQ1T7pAP+gi5Yu8g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js" integrity="sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA==" crossorigin=""></script>
<script src="new.js"></script>

<style>
        body {
            margin: 0;
        }
        #info_text {
          font-weight: bold;
          font-size: 16px;

        }
        .leaflet-rainfall {
            /* from leaflet-control-layers */
            border-radius: 5px;
            width: 170px;
            background: #fff;
            border: 2px solid rgba(0, 0, 0, 0.2);
            background-clip: padding-box;
            padding: 5px;
            height: 67.5px;
            margin-bottom: 0px !important;
        }

        .leaflet-rainfall .leaflet-rainfall-timestamp {
            text-align: center;
        }


        #filtersdiv {
          margin-top: 5px;
          margin-left : 80px;
          z-index: 2000;
          position: relative;
          /* display: flex;
          background-color: rgba(255, 255, 255, 0.281); */
          max-width: 300px;
          max-height: 200px;
          text-align: center;
          /* min-h: 330px; */
          /* justify-content: space-between; */
          /* justify-content: center; */
        }
</style>


</head>
<body>
<div id="map" style="height:100vh;width:100%;">
  <div class="row" >
    <div class="col-md-3 " id="filtersdiv">
        <div class=" panel panel-default">
          <select class="form-control formControlWIdth" name="datedp" id="fpdropdown">
              <option selected disabled value="0" style="">--Filter Data by Date & Time--</option>
          </select>
        </div>
    </div>
  </div>
</div>
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet-groupedlayercontrol/0.6.1/leaflet.groupedlayercontrol.js" integrity="sha512-2OAO6Vw7QqbRSoHqfdIhur/B7urhzltUGHOufhmGJRScSz8S0ZUyBp1ixI9BB0pLXIKqyQZ/cOwS4PgBTviT6Q==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet-groupedlayercontrol/0.6.1/leaflet.groupedlayercontrol.css" integrity="sha512-PpKEvRG//V8hN9idekL4WOjknpMTPFH3MnWpVbVBmlzXpoUfbBSr054U/TUmOzUnCOM9PAPiLhRgq0i00B4q3w==" crossorigin="anonymous" referrerpolicy="no-referrer" /> -->
<script>

var layerControl
var map
var dateString
var groups
var rainfallControl
var textbox_control
    var maxBounds = [[44.67,12.1], [47.42,17.44]];
    var imageBounds = [[44.67,12.1], [47.42,17.44]];
    var dataurl = "https://www.meteolab.si/amp_arso/json/data/ams_data_latest.json";



    
      // //Styled elements in layer control - could add images here as well

      map = L.map('map', {
      center: [46.155, 14.843],
      //  layers: [ExampleData.LayerGroups.temperature],
      zoom: 8,
      attributionControl: false
    });
 


      var gray = L.tileLayer('https://cartodb-basemaps-{s}.global.ssl.fastly.net/light_all/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a> &copy; <a href="http://cartodb.com/attributions">CartoDB</a>',
        subdomains: 'abcd',
        maxZoom: 19
      });

      var fire = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="http://www.thunderforest.com/">Thunderforest</a>, &copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
      }).addTo(map);

      var pioneer = L.tileLayer('https://{s}.tile.thunderforest.com/pioneer/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="http://www.thunderforest.com/">Thunderforest</a>, &copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
      });

      map.fitBounds(maxBounds);


      

    add_data_to_map(dataurl)



function add_data_to_map(dataurl){
  $.getJSON(dataurl, function(json) {
  // console.log(json)
   groups = {

    name : new L.layerGroup(),
    altitude : new L.layerGroup(),
    date_time : new L.layerGroup(),
    

    temperature : new L.layerGroup(),
    dewpoint : new L.layerGroup(),
    Relative_Humidity : new L.layerGroup(),

    Wind_Speed : new L.layerGroup(),
    Wind_Direction : new L.layerGroup(),
    Wind_Gusts : new L.layerGroup(),

    Qff_Pressure : new L.layerGroup(),
    Station_Pressure : new L.layerGroup(),
    Solar_Radiation : new L.layerGroup(),

    One_hour_Precipitation : new L.layerGroup(),
    twelve_hour_Precipitation : new L.layerGroup(),
    Visiblity : new L.layerGroup(),
  };


  geoLayer = L.geoJson(json, {
    style: function(feature) {
      var mag = feature.properties.id;
    },
    onEachFeature: function(feature, layer) {
      var popupText = "<b>Name:</b> " + feature.properties.title +
      "<br><b>Date/Time:</b> " + feature.properties.days[0].timeline[0].valid +
      "<br><b>Altitude:</b> " + feature.properties.altitude+
      
      "<br><b>Temperature:</b> " + feature.properties.days[0].timeline[0].tavg +
      "<br><b>Dewpoint:</b> " + feature.properties.days[0].timeline[0].td +
      "<br><b>Relative Humidity:</b> " + feature.properties.days[0].timeline[0].rh +

      "<br><b>Wind Speed:</b> " + feature.properties.days[0].timeline[0].ffavg_val +
      "<br><b>Wind Direction:</b> " + feature.properties.days[0].timeline[0].ddavg_shortText +
      "<br><b>Wind Gusts:</b> " + feature.properties.days[0].timeline[0].ffmax_val +

      "<br><b>Qff Pressure:</b> " + feature.properties.days[0].timeline[0].mslavg +
      "<br><b>Station Pressure:</b> " + feature.properties.days[0].timeline[0].pavg +
      "<br><b>Solar Radiation:</b> " + feature.properties.days[0].timeline[0].gSunRadavg+

      "<br><b>1 hour Precipitation:</b> " + feature.properties.days[0].timeline[0].tp_1h_acc +
      "<br><b>12 hour Precipitation:</b> " + feature.properties.days[0].timeline[0].tp_12h_acc +
      "<br><b>Visiblity:</b> " + feature.properties.days[0].timeline[0].vis_val;
      let popup = layer.bindPopup(popupText, {
        closeButton: true,
        offset: L.point(-0, -14)
      });
      layer.on('click', function() {
        layer.openPopup();
      });
    },
    pointToLayer: function(feature, latlng) {
      var d1 =  feature.properties.days[0].timeline[0].valid;
      // console.log(d1);

      const d2 = new Date(d1);
      
      var de = d2.getUTCDate();
      var me = d2.getUTCMonth() + 1;
      var y = d2.getUTCFullYear();
      var v = d2.toLocaleTimeString('sl-SI', {hour: '2-digit', minute: '2-digit'});

      dateString = (de<= 9 ? '0' + de : de) + '.' + (me <= 9 ? '0' + me : me) + '.' + y + " "  + v;
    
      // console.log(dateString);
    
      L.Control.textbox = L.Control.extend({
        onAdd: function(map) {
          
        var text = L.DomUtil.create('div');
        text.id = "info_text";
        text.innerHTML = "<div class='map-label-title'> Podatki iz samodejnih meteoroloskih postaj" + " " +  dateString  + " " + "CEST </div>"
        return text;
        },

        onRemove: function(map) {
          // Nothing to do here
        }
      });

      var popupText = "<b>Postaja:</b> " + feature.properties.title +
        "<br><b>Nadmorska vi&#154;ina:</b> " + feature.properties.altitude +" m"+
        "<br><b>Datum:</b> " + dateString  +
        
        "<br><b>Temperatura zraka:</b> " + feature.properties.days[0].timeline[0].tavg +"&#176;C"+
        "<br><b>Temp. rosi&#154;&#269;a:</b> " + feature.properties.days[0].timeline[0].td +"&#176;C"+
        "<br><b>Re. zracna vlaznost:</b> " + feature.properties.days[0].timeline[0].rh +" %"+

        "<br><b>Hitrost vetra:</b> " + feature.properties.days[0].timeline[0].ffavg_val +" km/h"+
        "<br><b>Smer vetra:</b> " + feature.properties.days[0].timeline[0].ddavg_shortText +
        "<br><b>Sunki vetra:</b> " + feature.properties.days[0].timeline[0].ffmax_val +" km/h"+

        "<br><b>QFF zra&#269;ni tlak:</b> " + feature.properties.days[0].timeline[0].mslavg +" hPa"+
        "<br><b>Zra&#269;ni tlak:</b> " + feature.properties.days[0].timeline[0].pavg +" hPa"+
        "<br><b>Glob. son&#269;no sevanje:</b> " + feature.properties.days[0].timeline[0].gSunRadavg +" W/m&#178;"+

        "<br><b>12 urne padavine:</b> " + feature.properties.days[0].timeline[0].tp_12h_acc +" mm"+
        "<br><b>Vidljivost:</b> " + feature.properties.days[0].timeline[0].vis_val +" km";
      
        
      



          var icontext = feature.properties.days[0].timeline[0].tavg;
          var icon = L.divIcon({
          iconSize:null,
          html:'<div class="map-label"><div class="map-label-content">'+icontext+'</div><div class="map-label-arrow"></div></div>'
          });
        let a = L.marker(latlng, {icon: icon}).bindPopup(popupText, {
        closeButton: true,
        offset: L.point(-0, -14)
        }).addTo(groups.temperature);


        var icontext = feature.properties.days[0].timeline[0].td;
        var icon = L.divIcon({
        iconSize:null,
        html:'<div class="map-label"><div class="map-label-content">'+icontext+'</div><div class="map-label-arrow"></div></div>'
        });
        let b = L.marker(latlng, {icon: icon}).bindPopup(popupText, {
          closeButton: true,
          offset: L.point(-0, -14)
        }).addTo(groups.dewpoint);

      var icontext = feature.properties.days[0].timeline[0].rh;
        var icon = L.divIcon({
        iconSize:null,
        html:'<div class="map-label"><div class="map-label-content">'+icontext+'</div><div class="map-label-arrow"></div></div>'
        });
      let c = L.marker(latlng, {icon: icon}).bindPopup(popupText, {
        closeButton: true,
        offset: L.point(-0, -14)
      }).addTo(groups.Relative_Humidity);

      var icontext = feature.properties.days[0].timeline[0].ffavg_val;
      var icon = L.divIcon({
      iconSize:null,
      html:'<div class="map-label"><div class="map-label-content">'+icontext+'</div><div class="map-label-arrow"></div></div>'
      });
      let d = L.marker(latlng, {icon: icon}).bindPopup(popupText, {
          closeButton: true,
          offset: L.point(-0, -14)
        }).addTo(groups.Wind_Speed);

        var icontext = feature.properties.days[0].timeline[0].ddavg_shortText;
          var icon = L.divIcon({
          iconSize:null,
          html:'<div class="map-label"><div class="map-label-content">'+icontext+'</div><div class="map-label-arrow"></div></div>'
        });
      let e = L.marker(latlng, {icon: icon}).bindPopup(popupText, {
          closeButton: true,
          offset: L.point(-0, -14)
        }).addTo(groups.Wind_Direction);

        var icontext = feature.properties.days[0].timeline[0].ffmax_val;
          var icon = L.divIcon({
          iconSize:null,
          html:'<div class="map-label"><div class="map-label-content">'+icontext+'</div><div class="map-label-arrow"></div></div>'
        });
      let f = L.marker(latlng, {icon: icon}).bindPopup(popupText, {
          closeButton: true,
          offset: L.point(-0, -14)
        }).addTo(groups.Wind_Gusts);

        var icontext = feature.properties.days[0].timeline[0].mslavg;
          var icon = L.divIcon({
          iconSize:null,
          html:'<div class="map-label"><div class="map-label-content">'+icontext+'</div><div class="map-label-arrow"></div></div>'
        });
      let g = L.marker(latlng, {icon: icon}).bindPopup(popupText, {
          closeButton: true,
          offset: L.point(-0, -14)
        }).addTo(groups.Qff_Pressure);

        var icontext = feature.properties.days[0].timeline[0].pavg;
          var icon = L.divIcon({
          iconSize:null,
          html:'<div class="map-label"><div class="map-label-content">'+icontext+'</div><div class="map-label-arrow"></div></div>'
        });
      let h = L.marker(latlng, {icon: icon}).bindPopup(popupText, {
          closeButton: true,
          offset: L.point(-0, -14)
        }).addTo(groups.Station_Pressure);

        var icontext = feature.properties.days[0].timeline[0].gSunRadavg;
          var icon = L.divIcon({
          iconSize:null,
          html:'<div class="map-label"><div class="map-label-content">'+icontext+'</div><div class="map-label-arrow"></div></div>'
        });
      let i = L.marker(latlng, {icon: icon}).bindPopup(popupText, {
          closeButton: true,
          offset: L.point(-0, -14)
        }).addTo(groups.Solar_Radiation);

        var icontext = feature.properties.days[0].timeline[0].tp_1h_acc;
          var icon = L.divIcon({
          iconSize:null,
          html:'<div class="map-label"><div class="map-label-content">'+icontext+'</div><div class="map-label-arrow"></div></div>'
        });
      let j = L.marker(latlng, {icon: icon}).bindPopup(popupText, {
          closeButton: true,
          offset: L.point(-0, -14)
        }).addTo(groups.One_hour_Precipitation);

        var icontext = feature.properties.days[0].timeline[0].tp_12h_acc;
          var icon = L.divIcon({
          iconSize:null,
          html:'<div class="map-label"><div class="map-label-content">'+icontext+'</div><div class="map-label-arrow"></div></div>'
        });
      let k = L.marker(latlng, {icon: icon}).bindPopup(popupText, {
          closeButton: true,
          offset: L.point(-0, -14)
        }).addTo(groups.twelve_hour_Precipitation);

        var icontext = feature.properties.days[0].timeline[0].vis_val;
          var icon = L.divIcon({
          iconSize:null,
          html:'<div class="map-label"><div class="map-label-content">'+icontext+'</div><div class="map-label-arrow"></div></div>'
        });
      let l = L.marker(latlng, {icon: icon}).bindPopup(popupText, {
          closeButton: true,
          offset: L.point(-0, -14)
        }).addTo(groups.Visiblity);

        var icontext = feature.properties.title;
          var icon = L.divIcon({
          iconSize:null,
          html:'<div class="map-label"><div class="map-label-content">'+icontext+'</div><div class="map-label-arrow"></div></div>'
        });
      let m = L.marker(latlng, {icon: icon}).bindPopup(popupText, {
          closeButton: true,
          offset: L.point(-0, -14)
        }).addTo(groups.name);

        var icontext = dateString;
          var icon = L.divIcon({
          iconSize:null,
          html:'<div class="map-label"><div class="map-label-content">'+icontext+'</div><div class="map-label-arrow"></div></div>'
        });
      let n = L.marker(latlng, {icon: icon}).bindPopup(popupText, {
          closeButton: true,
          offset: L.point(-0, -14)
        }).addTo(groups.date_time);

        var icontext = feature.properties.altitude;
          var icon = L.divIcon({
          iconSize:null,
          html:'<div class="map-label"><div class="map-label-content">'+icontext+'</div><div class="map-label-arrow"></div></div>'
      });
      let o = L.marker(latlng, {icon: icon}).bindPopup(popupText, {
          closeButton: true,
          offset: L.point(-0, -14)
        }).addTo(groups.altitude);
            

      
    },
    });

    window.ExampleData = {
        LayerGroups: groups,
        //Basemaps: basemaps
    };

   
    


      



      L.control.textbox = function(opts) { return new L.Control.textbox(opts);}
      textbox_control=L.control.textbox({ position: 'topright' }).addTo(map);

        rainfallControl = L.control.rainfall(
        {
              position: 'topright',
              data:[],
              transitionMs:750,
              opacity:0.7,

          }
      ).addTo(map);

      console.log(rainfallControl);
      function loadData() {
          fetch('https://www.meteolab.si/work/radar/si0-zm.json')
          .then(res => res.json())
          .then(data => {                
              rainfallControl.setData(data);
              rainfallControl.refreshAnimation();
              // rainfallControl.runAnimation();

              // console.log(rainfallControl);
              // console.log(data);
          })
          .catch(error => console.error);
      };
      loadData();



      function updateData() {
          setTimeout(() => {
              loadData();         
              updateData();
          }, 5 * 60 * 1000);
      }

      updateData();



      var baseLayers = {
    
        "Name": ExampleData.LayerGroups.name,
        "Altitude": ExampleData.LayerGroups.altitude,
        "Date/Time": ExampleData.LayerGroups.date_time,
        "Temperature": ExampleData.LayerGroups.temperature,
        "Dewpoint": ExampleData.LayerGroups.dewpoint,
        "Relative Humidity": ExampleData.LayerGroups.Relative_Humidity,
        "Wind Speed": ExampleData.LayerGroups.Wind_Speed,
        "Wind Direction": ExampleData.LayerGroups.Wind_Direction,
        "Wind Gusts": ExampleData.LayerGroups.Wind_Gusts,
        "Qff Pressure": ExampleData.LayerGroups.Qff_Pressure,
        "Station Pressure": ExampleData.LayerGroups.Station_Pressure,
        "Solar Radiation": ExampleData.LayerGroups.Solar_Radiation,
        "1 Hour Precipitation": ExampleData.LayerGroups.One_hour_Precipitation,
        "12 Hour Precipitation": ExampleData.LayerGroups.twelve_hour_Precipitation,
        "Visiblity": ExampleData.LayerGroups.Visiblity,
        // "Rainfall": rainfallControl,

      };




      var overlays = {
        // "rainfal":L.control.rainfall
      };


      //   var options = {
      //   // Make the "Landmarks" group exclusive (use radio inputs)
      //   exclusiveGroups: ["Parameters"],

      //   // enable basic collapsability
      //   groupsCollapsable: false,
      
      // };

      // var layerControl = new L.control.layers(basemaps).addTo(map);

      // var scale = L.control.scale().addTo(map);

      //L.control.rainfall({}).addTo(map);



      layerControl=L.control.layers( baseLayers,undefined,{
        collapsed: false
      }).addTo(map);

      ExampleData.LayerGroups.temperature.addTo(map)

  });
}

var today = new Date();
var today_date = today.getFullYear()+'-'+(today.getMonth()+1)+'-'+today.getDate();
// console.log(typeof today_date)
  // $.getJSON(data, function(json) {
  //   for(var i=0;i<json.length;i++){
  //    var lopdate=json[0].features[0].properties.days[0].date
  //     if(lopdate==predate){
  //         $('select[name="datedp"]').append('<option value="+'+lopdate+'">'+lopdate+'</option>');
  //     }
  //     predate=lopdate
  //     console.log(lopdate)
  //   }
  // });

  function fillDropDown(){ 
    $.ajax({
      url: "file_names_read.php",
      type: "GET",
      dataType: "json",
      //data: JSON.stringify(geom,layer.geometry),
      contentType: "application/json; charset=utf-8",
      success: function callback(data) {
          // console.log(data)
          // var r=JSON.parse(response)
          for(var i=0;i<data.length;i++){
            var filename=data[i]
            var filename_for_show = filename.replace(".json", "");
            var cuted_filename = filename.replace("ams_data_", "");
            var date_time = cuted_filename.substring(0, 15);
            var index = date_time.indexOf('_');
            var [first, second] = [date_time.slice(0, index), date_time.slice(index + 1)];
            // console.log(first+','+second)
            var time = date_time.substring(date_time.indexOf('_') + 1);
            // console.log(date_time)
            // 2022-04-21_2220
            var time_h = second.substring(0, 2);
            var time_m = second.substring(2, 4);
            var date_yy = first.substring(0, 4);
            var date_mm = first.substring(7, 5);
            var date_dd = first.substring(10, 8);
            var date_with_ft=date_dd+"."+date_mm+"."+date_yy+" "+time_h+":"+time_m;
            // console.log(date_with_ft)
           
            var file_date = cuted_filename.substring(0, 10);
            
            var date2=new Date(today_date)
            var date1=new Date(file_date)
            var date_difference = difference(date1,date2)
            // console.log(data[i])
            if(date_difference<=8){
              $('select[name="datedp"]').append('<option value="'+filename_for_show+'">'+date_with_ft+'</option>');
            }
            
            // $('select[name="datedp"]').append('<option value="+'+lopdate+'">'+lopdate+'</option>');
          }
      
      }
    });
  }
  fillDropDown()

  

  function difference(date1, date2) {  
    const date1utc = Date.UTC(date1.getFullYear(), date1.getMonth(), date1.getDate());
    const date2utc = Date.UTC(date2.getFullYear(), date2.getMonth(), date2.getDate());
      day = 1000*60*60*24;
    return(date2utc - date1utc)/day
  }
  // var a="2022-4-08"
  // var b="2022-4-21"
  // var date1 = new Date(a),
  //     date2 = new Date(b),
  //     time_difference = difference(date1,date2)
  //     console.log(time_difference)
 



// Overlay layers are grouped








</script>
<script src="leaflet-rainfall.js"></script>

<script>
  $('select[name="datedp"]').on('change',function(e){
    // alert("drp change")
    var dvalue= $(this).val();
    console.log(dvalue)
    var ext = ".json";
    var filename = dvalue.concat(ext);
    var dataurl = "https://www.meteolab.si/amp_arso/json/data/";
    var final_url = dataurl.concat(filename);
    removeall_layers()
    // map.off();
    // map.remove();
    add_data_to_map(final_url)
  });


  

  function removeall_layers(){
    map.removeControl(textbox_control)
    map.removeControl(rainfallControl)
    map.removeControl(layerControl)
    
    if (map.hasLayer(groups.name)) {
      map.removeLayer(groups.name)
    }
    if (map.hasLayer(groups.altitude)) {
      map.removeLayer(groups.altitude)
    }
    if (map.hasLayer(groups.date_time)) {
      map.removeLayer(groups.date_time)
    }
    if (map.hasLayer(groups.temperature)) {
      map.removeLayer(groups.temperature)
    }
    if (map.hasLayer(groups.dewpoint)) {
      map.removeLayer(groups.dewpoint)
    }
    if (map.hasLayer(groups.Relative_Humidity)) {
      map.removeLayer(groups.Relative_Humidity)
    }
    if (map.hasLayer(groups.Wind_Speed)) {
      map.removeLayer(groups.Wind_Speed)
    }
    if (map.hasLayer(groups.Wind_Direction)) {
      map.removeLayer(groups.Wind_Direction)
    }
    if (map.hasLayer(groups.Wind_Gusts)) {
      map.removeLayer(groups.Wind_Gusts)
    }
    if (map.hasLayer(groups.Qff_Pressure)) {
      map.removeLayer(groups.Qff_Pressure)
    }
    if (map.hasLayer(groups.Station_Pressure)) {
      map.removeLayer(groups.Station_Pressure)
    }
    if (map.hasLayer(groups.Solar_Radiation)) {
      map.removeLayer(groups.Solar_Radiation)
    }
    if (map.hasLayer(groups.One_hour_Precipitation)) {
      map.removeLayer(groups.One_hour_Precipitation)
    }
    if (map.hasLayer(groups.twelve_hour_Precipitation)) {
      map.removeLayer(groups.twelve_hour_Precipitation)
    }
    if (map.hasLayer(groups.Visiblity)) {
      map.removeLayer(groups.Visiblity)
    }
  }
</script>
</body>
</html>