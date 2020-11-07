<?php
require __DIR__ . '/vendor/autoload.php';

use Corbpie\BunnyCdn\BunnyAPI;

$bunny = new BunnyAPI();
//Make sure API_KEY is set at line 12 bunnyAPI.php

/*
 *
 * PULL ZONE EXAMPLES
 *
 */

echo $bunny->listPullZones();//Returns data for all Pull zones on account
//Here you will find the ID's for your pullZones

//Examples using pull zone id: 1337

//Individual pull zone data
$bunny->pullZoneData(1337);

//List hostnames for a pull zone
$bunny->pullZoneHostnames(1337);

//Add hostname to pull zone
$bunny->addHostnamePullZone(1337, 'cdn.domain.com');

//Force SSL for pull zone hostname
$bunny->forceSSLPullZone(1337, 'cdn.domain.com', true);

//Disable SSL for pull zone hostname
$bunny->forceSSLPullZone(1337, 'cdn.domain.com', false);

//Remove hostname for pull zone
$bunny->removeHostnamePullZone(1337, 'cdn.domain.com');

//List blocked ip addresses
$bunny->listBlockedIpPullZone(1337);

//Add ip to blocked
$bunny->addBlockedIpPullZone(1337, '199.000.111.222');

//Remove blocked ip
$bunny->unBlockedIpPullZone(1337, '199.000.111.222');

//Pull zone HTTP access logs (mm-dd-yy)
$bunny->pullZoneLogs(1337, '10-29-20');

//Create pull zone
$bunny->createPullZone('a_test_pull_zone', 'https://domain.com');

//Purge pull zone
$bunny->purgePullZone(1337);

//Purge cache for a URL
$bunny->purgeCache('https://cdn.domain.com/css/style.min.css');

//Get monthly charges
$bunny->monthCharges();

//Total billing amount
$bunny->totalBillingAmount();

//Current account balance
$bunny->balance();

//Monthly charges break down (per zone)
$bunny->monthChargeBreakdown();

//Bandwidth stats
$bunny->getStatistics();


/*
 *
 * STORAGE ZONE EXAMPLES
 *
 */

//View all storage zones for account
echo $bunny->listStorageZones();//Returns data for all Storage zones on account

$bunny->zoneConnect('homeimagebackups', '');//Create connection to 'homeimagebackups' storage zone
//Access key (2nd param) can be set or left empty to which it will auto fetch from a listStorageZones() call

//List folders for storage zone 'homeimagebackups'
echo $bunny->listFolders();

//Create a new folder
echo $bunny->createFolder('pets');//Creates a new folder called pets

//Upload file into folder
echo $bunny->uploadFile('fluffy.jpg', '/pets/fluffy.jpg');//Uploads fluffy.jpg as pets/fluffy.jpg

//Rename a file
echo $bunny->renameFile('pets/', 'fluffy.jpg', 'fluffy_young.jpg');//Renames pets/fluffy.jpg as pets/fluffy_young.jpg

//Move a file
echo $bunny->moveFile('pets/', 'fluffy_young.jpg', 'pets/puppy_fluffy/');//Moves pets/fluffy_young.jpg to pets/puppy_fluffy/fluffy_young.jpg

//Get file size
echo $bunny->getFileSize('pets/puppy_fluffy/fluffy_young.jpg');//File size as bytes
echo $bunny->convertBytes($bunny->getFileSize('pets/puppy_fluffy/fluffy_young.jpg'), 'MB');//File size as megabytes

//Delete a file
echo $bunny->deleteFile('pets/puppy_fluffy/fluffy_young.jpg');//Deletes fluffy_young.jpg

//Delete folders (only works if folder empty)
echo $bunny->deleteFolder('pets/puppy_fluffy/');
echo $bunny->deleteFolder('pets/');