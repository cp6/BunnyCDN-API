# BunnyCDN API Class

The most comprehensive, feature packed and easy to use PHP class for the BunnyCDN pull, video streaming and storage
zones [API](https://bunnycdn.docs.apiary.io/).

This class whilst having a main focus on storage zone interaction includes pull zone features. Combining API with FTP,
managing and using BunnyCDN storage zones just got easier.

[![Generic badge](https://img.shields.io/badge/version-1.4-blue.svg)](https://shields.io/)

**Note video streaming API is seemingly not finalized and is changing**

### Requires

For Pull zone, billing and statistics API interaction you will need your BunnyCDN API key, this is found in your
dashboard in the My Account section.

If you want to interact with storage zones you will need your BunnyCDN API key set and the name of the storage zone.

```listStorageZones()``` returns all the storage zone data/info for the account.

## Features & abilities

* List storage zones
* Add/create storage zone
* Delete storage zone
* Create folder in storage zone
* Delete folder in storage zone
* Delete file in storage zone
* Delete all files in a folder in storage zone
* Download a file from storage zone
* Download a file from storage zone with progress percentage
* Download all files in a folder from storage zone
* Upload file to storage zone
* Upload file to storage zone with progress percentage
* Upload all files in a folder to storage zone
* Rename file or folder in storage zone
* Move file in storage zone
* Get file size in storage zone
* Get directory size in storage zone
* Navigate/List directories in storage zone
* List all from storage zone directory
* List all files formatted from storage zone directory
* List all folders formatted from storage zone directory
* List all formatted from storage zone directory
* Get usage statistics
* Get billing data
* View balance
* View monthly charge
* View monthly charge breakdown
* Apply coupon code
* List pull zones
* Get pull zone
* Add pull zone
* Update pull zone
* Delete pull zone
* Purge pull zone
* Add hostname to pull zone
* Remove hostname from pull zone
* Set force SSL for pull zone
* List pull zone HTTP access logs
* Calculate costs

## Usage

Usage is simple, install with:

```
composer require corbpie/bunny-cdn-api
```

Use like:

```php
require __DIR__ . '/vendor/autoload.php';

use Corbpie\BunnyCdn\BunnyAPI;

$bunny = new bunnyAPI();//Initiate the class

echo $bunny->listPullZones();
```

#### Setting API key:

**option 1 (preferred)**

Line 12 ```bunnyAPI.php```

```php
const API_KEY = 'XXXX-XXXX-XXXX';
```

**option 2**

With ```apiKey()``` (needs setting with each calling of class)

```php
$bunny->apiKey('XXXX-XXXX-XXXX');//Bunny api key
```

---

To have max execution time of 300 seconds

```php
$bunny = new bunnyAPI(300);
```

---

Storage zone name and access key for storage zone interaction (**not needed if just using pull zone functions**)

Set ```$access_key = ''``` to obtain key automatically (storage name must be accurate)

```php
$bunny->zoneConnect($storagename, $access_key);
```

`$storagename` name of storage zone `string`

`$access_key` key/password to storage zone ```string``` **optional**

---

List storage zones

```php
$bunny->listStorageZones();
```

returns `array`

---

Add a storage zone

```php
$bunny->addStorageZone($newstoragezone);
```

`$newstoragezone` name of storage zone to create `string`

---

Delete a storage zone

```php
$bunny->deleteStorageZone($id);
```

`$id` id of storage zone to delete `int`

---

Get directory size

```php
$bunny->dirSize($dir);
```

`$dir` directory to get size of `string`

---

Return current directory

```php
$bunny->currentDir();
```

returns `string`

---

Change directory

```php
$bunny->changeDir($dir);
```

`$dir` directory navigation (FTP rules) `string`

---

Move to parent directory

```php
$bunny->moveUpOne();
```

---

Create folder in current directory

```php
$bunny->createFolder($newfolder);
```

`$newfolder` Create a folder in current directory `string`

---

Delete folder

```php
$bunny->deleteFolder($name);
```

`$name` Name of folder to delete (must be empty) `string`

---

Delete a file

```php
$bunny->deleteFile($name);
```

`$name` Name of file to delete `string`

---

Delete all files in a folder

```php
$bunny->deleteAllFiles($dir);
```

`$dir` Directory to delete all files in `string`

---

Rename a file

BunnyCDN does not allow for ftp_rename so file copied to new name and then old file deleted.

```php
$bunny->renameFile($directory, $old_file_name, $new_file_name);
```

`$directory` Directory that contains the file `string`

`$old_name` Object that is being renamed `string`

`$new_name` New name for object `string`

---

Move a file

```php
$bunny->moveFile($file, $move_to);
```

`$file` File to move `string`

`$move_to` Directory to move file to `string`

---

Download a file

```php
$bunny->downloadFile($save_as, $get_file, $mode);
```

`$save_as` Save file as `string`

`$get_file` File to download `string`

`$mode` FTP mode to use `INT`

---

Download all files in a directory

```php
$bunny->downloadAll($dir_dl_from, $dl_into, $mode);
```

`$dir_dl_from` Directory to download all from `string`

`$dl_into` Download into `string`

`$mode` FTP mode to use `INT`

---

Upload a file

```php
$bunny->uploadFile($upload, $upload_as, $mode);
```

`$upload` File to upload `string`

`$upload_as` Upload as `string`

`$mode` FTP mode to use `INT`

---

Upload all files from a local folder

```php
$bunny->uploadAllFiles($dir, $place, $mode);
```

`$dir` Upload all files from this directory `string`

`$place` Upload to `string`

`$mode` FTP mode to use `INT`

---

Return storage zone files and folder data (Original)

```php
$bunny->listAllOG();
```

returns `array`

---

Return storage zone directory files formatted

```php
$bunny->listFiles($location);
```

`$location` Directory to get and return data `string`

returns `array`

---

Return storage zone directory folders formatted

```php
$bunny->listFolders($location);
```

`$location` Directory to get and return data `string`

returns `array`

---

Return storage zone directory file and folders formatted

```php
$bunny->listAll($location);
```

`$location` Directory to get and return data `string`

returns `array`

---

List all pull zones and data

```php
$bunny->listPullZones();
```

returns `array`

---

List pull zones data for id

```php
$bunny->pullZoneData($id);
```

`$id` Pull zone to get data from `int`

returns `array`

---

Purge pull zone data

```php
$bunny->purgePullZone($id);
```

`$id` Pull zone to purge `int`

---

Delete pull zone data

```php
$bunny->deletePullZone($id);
```

`$id` Pull zone to delete `int`

---

Lists pullzone hostnames and amount

```php
$bunny->pullZoneHostnames($pullzone_id);
```

---

Add hostname to pull zone

```php
$bunny->addHostnamePullZone($id, $hostname);
```

`$id` Pull zone hostname will be added to `int`

`$hostname` Hostname to add `string`

---

Remove a hostname from pull zone

```php
$bunny->removeHostnamePullZone($id, $hostname);
```

`$id` Pull zone hostname be removed from `int`

`$hostname` Hostname to remove `string`

---

Change force SSL status for pull zone

```php
$bunny->forceSSLPullZone($id, $hostname, $force_ssl);
```

`$id` Pull zone hostname change status `int`

`$hostname` Affected hostname  `string`

`$force_ssl` True = on, FALSE = off  `bool`

---

Add ip to block for pullzone

```php
$bunny->addBlockedIpPullZone($pullzone_id, $ip, $db_log = false);
```

---

Un block an ip for pullzone

```php
$bunny->unBlockedIpPullZone($pullzone_id, $ip, $db_log = false);
```

---

List all blocked ip's for pullzone

```php
$bunny->listBlockedIpPullZone($pullzone_id);
```

---

Purge cache for a URL

```php
$bunny->purgeCache($url);
```

`$url` Purge cache for this url  `string`

---

Pull zone logs as formatted array

```php
$bunny->pullZoneLogs($id, $date);
```

`$id` Pull zone id `int`

`$date` Date for logs, only past 3 days (mm-dd-yy) `string`

---

Get usage statistics

```php
$bunny->getStatistics();
```

returns `array`

---

Get billing data

```php
$bunny->getBilling();
```

returns `array`

---

Get account balance

```php
$bunny->balance();
```

returns `float`

---

Get monthly charge

```php
$bunny->monthCharges();
```

returns `float`

---

Get monthly charge breakdown for region

```php
$bunny->monthChargeBreakdown();
```

returns `array`

---

Lists total billing amount and first date time

```php
$bunny->totalBillingAmount();
```

returns `array`

---

Apply a coupon code

```php
$bunny->applyCoupon($code);
```

---

Set Json header

```php
$bunny->jsonHeader();
```

---

Convert/format bytes to other data types

```php
$bunny->convertBytes($bytes, $convert_to = 'GB', $format = true, $decimals = 2);
```

---

Convert bool to int value

```php
$bunny->boolToInt($bool);
```

returns `int`

---

Close connection (Optional)

```php
$bunny->closeConnection();
```

---

## Video streaming

Set video stream library id

```php
$bunny->setStreamLibraryId($library_id);
```

`$library_id` stream library id `int`

---

Set video collection guid

```php
$bunny->setStreamCollectionGuid($collection_guid);
```

`$collection_guid` video collection guid `string`

---

Set video guid

```php
$bunny->setStreamVideoGuid($video_guid);
```

`$video_guid` video guid `string`

---

Get video collections for library

```php
$bunny->getStreamCollections($library_id, $page, $items_pp,$order_by);
```

`$library_id` library id `int`

*0 = use set library id from setStreamLibraryId()*

`$page` page number `int`

`$items_pp` items to show `int`

`$order_by` order by `string`

---

Get streams for a collection

```php
$bunny->getStreamForCollection($library_id, $collection_guid);
```

`$library_id` library id `int`

*0 = use set library id from setStreamLibraryId()*

`$collection_guid` video collection guid `string`

*leave empty to use set collection guid from setStreamCollectionGuid()*

---

Update stream collection

```php
$bunny->updateCollection($library_id, $collection_guid);
```

`$library_id` library id `int`

`$collection_guid` video collection guid `string`

---

Delete stream collection

```php
$bunny->deleteCollection($library_id, $collection_guid);
```

`$library_id` library id `int`

`$collection_guid` video collection guid `string`

---

Create stream collection

```php
$bunny->createCollection($library_id, $collection_guid);
```

`$library_id` library id `int`

`$collection_guid` video collection guid `string`

---

List videos for library

```php
$bunny->listVideos($library_id, $collection_guid);
```

`$library_id` library id `int`

`$collection_guid` video collection guid `string`

---

Get video information

```php
$bunny->getVideo($library_id, $collection_guid);
```

`$library_id` library id `int`

`$collection_guid` video collection guid `string`

---

Delete video

```php
$bunny->deleteVideo($library_id, $collection_guid);
```

`$library_id` library id `int`

`$collection_guid` video collection guid `string`

---

Create video

```php
$bunny->createVideo($library_id, $video_title, $collection_guid);
```

`$library_id` library id `int`

`$video_title` video title `string`

`$collection_guid` video collection guid `string`

---

Upload video

Need to use createVideo() first to get video guid

```php
$bunny->uploadVideo($library_id, $collection_guid, $video_to_upload);
```

`$library_id` library id `int`

`$collection_guid` video collection guid `string`

`$video_to_upload` video file `string`

---

Set thumbnail for video

```php
$bunny->setThumbnail($library_id, $video_guid, $thumbnail_url);
```

`$library_id` library id `int`

`$video_guid` video guid `string`

`$thumbnail_url` image url `string`

---

Add captions

```php
$bunny->addCaptions($library_id, $video_guid, $collection_guid, $label, $captions_file);
```

`$library_id` library id `int`

`$video_guid` video guid `string`

`$srclang` caption srclang `string`

`$label` label for captions `string`

`$captions_file` caption file URL `string`

---

Delete captions

```php
$bunny->deleteCaptions($library_id, $video_guid, $srclang);
```

`$library_id` library id `int`

`$video_guid` video guid `string`

`$srclang` captions srclang `string`

---

## TODO

* Proper exception handling
