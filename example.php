<?php
require_once('bunnyAPI.php');
$bunny = new BunnyAPI();
//Make sure API_KEY is set at line 10 bunnyAPI.php

echo $bunny->listPullZones();//Returns data for all Pull zones on account

echo $bunny->listStorageZones();//Returns data for all Storage zones on account

$bunny->zoneConnect('homeimagebackups', '');//Create connection to 'homeimagebackups'
//Access key can be set or left empty to which it will auto fetch from a listStorageZones() call

//List folders for storage zone 'homeimagebackups'
echo $bunny->listFolders();

//Create a new folder called pets
echo $bunny->createFolder('pets');

//Upload file (fluffy.jpg) into pets folder
echo $bunny->uploadFile('fluffy.jpg', '/pets/fluffy.jpg');

//Rename fluffy.jpg to fluffy_young.jpg
echo $bunny->renameFile('pets/', 'fluffy.jpg', 'fluffy_young.jpg');//Renames pets/fluffy.jpg as pets/fluffy_young.jpg

//Move a file
echo $bunny->moveFile('pets/', 'fluffy_young.jpg', 'pets/puppy_fluffy/');//Moves pets/fluffy_young.jpg to pets/puppy_fluffy/fluffy_young.jpg

//Delete fluffy_young.jpg
echo $bunny->deleteFile('pets/puppy_fluffy/fluffy_young.jpg');

//Delete folders (only works if folder empty)
echo $bunny->deleteFolder('pets/puppy_fluffy/');
echo $bunny->deleteFolder('pets/');