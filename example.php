<?php
require_once('bunnyAPI.php');
$bunny = new BunnyAPI();
$bunny->apiKey('8561609c-681d-6q05-ck9m-34810be1542abaa4d8-f8de-2208-ff38-26c58dadb544');
echo $bunny->listPullZones();//Returns data for all pull zones on account