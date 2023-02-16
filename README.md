# BunnyCDN API Class

The most comprehensive, feature packed and easy to use PHP class for [bunny.net](https://bunny.net?ref=qxdxfxutxf) (
BunnyCDN) pull, video streaming, DNS and storage zones [API](https://docs.bunny.net/reference/bunnynet-api-overview).

This class whilst having a main focus on storage zone interaction includes pull zone features. Combining API with FTP,
managing and using BunnyNet storage zones just got easier.

[![Generic badge](https://img.shields.io/badge/version-1.9.2-blue.svg)]()
[![Generic badge](https://img.shields.io/badge/PHP-8.2-purple.svg)]()

## Index
* [Features](#features)
* [Installing & usage](#installing)
* [Pullzone](#pullzone)
* [Storage](#storage)
* [Video streaming](#video)
* [DNS]()
* [Misc]()

### 1.9.2 changes
* Updated project to be PHP version 8.2 as a minimum

### TODO
* Sort (features) and index the readme

### Requirements

* PHP 8.2

For Pull zone, billing and statistics API interaction you will need your BunnyNet API key, this is found in your
dashboard in the My Account section.

If you want to interact with storage zones you will need your BunnyCDN API key set and the name of the storage zone.

You can get this with ```listStorageZones()``` as it returns all the storage zone data/info for the account.

<span id="features"></span>
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

<span id="installing"></span>
## Usage

Install with composer:

```
composer require corbpie/bunny-cdn-api
```

Use like:

```php
require __DIR__ . '/vendor/autoload.php';

use Corbpie\BunnyCdn\BunnyAPIPull;

$bunny = new BunnyAPIPull();//Initiate the class

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

<span id="storage"></span>
### Storage zone interaction

```php
require __DIR__ . '/vendor/autoload.php';

use Corbpie\BunnyCdn\BunnyAPIStorage;

$bunny = new BunnyAPIStorage();
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
<span id="pullzone"></span>
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
<span id="video"></span>
### Video streaming zone interaction

```php
require __DIR__ . '/vendor/autoload.php';

use Corbpie\BunnyCdn\BunnyAPIStream;

$bunny = new BunnyAPIStream();
```
---

**You can only get the video library id from your bunny.net stream library page**

Set video stream library id

```php
$bunny->setStreamLibraryId($library_id);
```

`$library_id` stream library id `int`

---

Get video collections

Requires ```setStreamLibraryId()``` to be set.

```php
$bunny->getVideoCollections();
```

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

Get video collections for library id

Requires ```setStreamLibraryId()``` to be set.

```php
$bunny->getStreamCollections($page, $items_pp,$order_by);
```

`$page` page number `int`

`$items_pp` items to show `int`

`$order_by` order by `string`

---

Get streams for a collection

Requires ```setStreamLibraryId()``` and ```setStreamCollectionGuid()``` to be set.

```php
$bunny->getStreamForCollection();
```

---

Update stream collection

Requires ```setStreamLibraryId()``` and ```setStreamCollectionGuid()``` to be set.

```php
$bunny->updateCollection($updated_collection_name);
```

`$updated_collection_name` the name to update video collection to `string`

---

Delete stream collection

Requires ```setStreamLibraryId()``` and ```setStreamCollectionGuid()``` to be set.

```php
$bunny->deleteCollection();
```

---

Create stream collection

Requires ```setStreamLibraryId()``` to be set.

```php
$bunny->createCollection($new_collection_name);
```

`$new_collection_name` the name for your new video collection `string`

---

List videos for library

Requires ```setStreamLibraryId()``` to be set.

```php
$bunny->listVideos($collection_guid);
```

`$collection_guid` video collection guid `string`

---

Get video information

Requires ```setStreamLibraryId()``` to be set.

```php
$bunny->getVideo($collection_guid);
```

`$collection_guid` video collection guid `string`

---

Delete video

Requires ```setStreamLibraryId()``` to be set.

```php
$bunny->deleteVideo($collection_guid);
```

`$library_id` library id `int`

`$collection_guid` video collection guid `string`

---

Create video

Requires ```setStreamLibraryId()``` to be set.

```php
$bunny->createVideo($video_title);
```

`$video_title` video title `string`


---

Create video for collection

Requires ```setStreamLibraryId()``` and ```setStreamCollectionGuid()```  to be set.

```php
$bunny->createVideoForCollection($video_title);
```

`$video_title` video title `string`

---

Upload video

Requires ```setStreamLibraryId()``` to be set.

Need to use ```createVideo()``` first to get video guid

```php
$bunny->uploadVideo($video_guid, $video_to_upload);
```

`$video_guid` video guid `string`

`$video_to_upload` video filename `string`

---

Set thumbnail for video

Requires ```setStreamLibraryId()``` to be set.

```php
$bunny->setThumbnail($video_guid, $thumbnail_url);
```

`$video_guid` video guid `string`

`$thumbnail_url` image url `string`

---

Get video resolutions

Requires ```setStreamLibraryId()``` to be set.

```php
$bunny->videoResolutionsArray($video_guid);
```

---

Get video size

Requires ```setStreamLibraryId()``` to be set.

```php
$bunny->videoSize($video_guid);
```

---

Add captions

Requires ```setStreamLibraryId()``` to be set.

```php
$bunny->addCaptions($video_guid, $collection_guid, $label, $captions_file);
```

`$video_guid` video guid `string`

`$srclang` caption srclang `string`

`$label` label for captions `string`

`$captions_file` caption file URL `string`

---

Delete captions

Requires ```setStreamLibraryId()``` to be set.

```php
$bunny->deleteCaptions($library_id, $video_guid, $srclang);
```

`$video_guid` video guid `string`

`$srclang` captions srclang `string`

---