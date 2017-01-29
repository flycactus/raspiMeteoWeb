<?php
header ("Content-type: image/png");
$image = imagecreatefrompng("15-08-28_meteo.png");
imagepng($image,"current_meteo.png"); 
?>
