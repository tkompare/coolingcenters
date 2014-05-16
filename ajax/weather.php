<?php

/** Autoloader */
spl_autoload_register(function ($class) {
	include '../classes/' . $class . '.class.php';
});

$lat = 41.98;
$lng = -87.9;

if(isset($_GET['lat']) && isset($_GET['lng']))
{
	$lat = $_GET['lat'];
	$lng = $_GET['lng'];
}

$NWS = new NWSData($lat,$lng);

$Combined = new NWSCombined($NWS->getCombined());

$Combined->returnJSON();