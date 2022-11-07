
<?php
header('Access-Control-Allow-Origin:*');
$url = 'https://www.meteolab.si/amp_arso/json/data';

$html = file_get_contents($url);

$count = preg_match_all('/<td><a href="([^"]+)">[^<]*<\/a><\/td>/i', $html, $files);
$file_names_arr=array();
for ($i = 0; $i < $count; ++$i) {
    $file_names_arr[$i]=$files[1][$i];
    // echo "File: " . $files[1][$i] . "<br />\n";
}
echo  json_encode($file_names_arr);
?>