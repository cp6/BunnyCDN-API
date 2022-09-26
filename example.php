<?php
require __DIR__ . '/vendor/autoload.php';

use Corbpie\BunnyCdn\BunnyAPIPull;

$bunny = new BunnyAPIPull();
//Make sure API_KEY is set at line 9 bunnyAPI.php

/*
 *
 * PULL ZONE EXAMPLES
 *
 */

echo json_encode($bunny->listPullZones());//Returns data for all Pull zones on account
//Here you will find the ID's for your pullZones

//Examples using pull zone id: 1337

//Individual pull zone data
echo json_encode($bunny->pullZoneData(26719));

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

use Corbpie\BunnyCdn\BunnyAPIStorage;

$bunny = new BunnyAPIStorage();
//View all storage zones for account
echo $bunny->listStorageZones();//Returns data for all Storage zones on account

$bunny->zoneConnect('homeimagebackups', '');//Create connection to 'homeimagebackups' storage zone
//Access key (2nd param) can be set or left empty to which it will auto fetch from a listStorageZones() call

//List folders for storage zone 'homeimagebackups'
echo $bunny->listFolders();

//Check if a folder (path) exists by using its path
$bunny->folderExists('pets');//Returns true if exists

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

/*
 *
 * Video stream API examples
 *
 */

use Corbpie\BunnyCdn\BunnyAPIStream;

$bunny = new BunnyAPIStream();

//List collections for library 1234
echo json_encode($bunny->getStreamCollections(1234));

//List videos for library 1234 and collection 886gce58-1482-416f-b908-fca0b60f49ba
$bunny->setStreamLibraryId(1234);
$bunny->setStreamCollectionGuid('886gce58-1482-416f-b908-fca0b60f49ba');
echo json_encode($bunny->listVideosForCollectionId());

//List video information individually
echo json_encode($bunny->getVideo(1234,'e6410005-d591-4a7e-a83d-6c1eef0fdc78'));

//Get array of resolutions for video
echo json_encode($bunny->videoResolutionsArray('e6410005-d591-4a7e-a83d-6c1eef0fdc78'));

//Get size of video
echo json_encode($bunny->videoSize('e6410005-d591-4a7e-a83d-6c1eef0fdc78', 'MB'));


//Create a video (prepare for upload)
echo json_encode($bunny->createVideo('title_for_the_video'));
//OR In collection
echo json_encode($bunny->createVideoForCollection('title_for_the_video'));
//These return information for the video. Importantly the video guid

//Upload the video file
echo json_encode($bunny->uploadVideo('a6e8483a-7538-4eb1-bb1f-6c1eef0fdc78', 'test_video.mp4'));
//Uploads test_video.mp4