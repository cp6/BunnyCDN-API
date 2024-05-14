# BunnyNET CDN API Class

The most comprehensive, feature packed and easy to use PHP class for [bunny.net](https://bunny.net?ref=qxdxfxutxf) (
BunnyCDN) pull, video streaming, DNS and storage zones [API](https://docs.bunny.net/reference/bunnynet-api-overview).

This class whilst having a main focus on storage zone interaction includes pull zone features, DNS, Video streaming and
more. Combining API with FTP,
managing and using BunnyNet storage zones just got easier.

[![Generic badge](https://img.shields.io/badge/version-1.9.5-blue.svg)]()
[![Generic badge](https://img.shields.io/badge/PHP-8.2-purple.svg)]()

## Table of contents

- [Features](#features)
- [Installing & usage](#installing)
    - [Setting API key](#setting-api-key)
- [Pullzone](#pullzone)
    - [List all pullzones](#list-pullzones)
    - [List a pullzone](#list-pullzone)
    - [Purge pullzone](#purge-pullzone)
    - [Delete pullzone](#delete-pullzone)
    - [List pullzone hostnames](#hostnames-pullzone)
    - [Add hostname to pullzone](#add-hostname-pullzone)
    - [Remove hostname from pullzone](#remove-hostname-pullzone)
    - [Change ssl status for pullzone](#ssl-pullzone)
    - [Add IP block for pullzone](#ip-block-pullzone)
    - [Remove IP block for pullzone](#ip-unblock-pullzone)
    - [List blocked IPs for pullzone](#ip-blocked-pullzone)
    - [Purge cache for a URL](#purge-url-pullzone)
    - [Pullzone logs are array](#logs-pullzone)
- [Storage](#storage)
    - [Connect to storage zone](#storagezone-connect)
    - [List all storage zones](#storagezone-list)
    - [Add storage zone](#add-storagezone)
    - [Delete storage zone](#delete-storagezone)
    - [Get directory size](#get-directory-size)
    - [Return current directory](#current-directory)
    - [Change directory](#change-directory)
    - [Check folder exists](#folder-exists)
    - [Check file exists](#file-exists)
    - [Move to parent directory](#move-to-parent-directory)
    - [Create folder in current directory](#create-folder)
    - [Delete folder](#delete-folder)
    - [Delete file](#delete-file)
    - [Delete all files in a folder](#delete-all-files)
    - [Rename a file](#rename-file)
    - [Move a file](#move-file)
    - [Download a file](#download-file)
    - [Download all files in a directory](#download-all-files)
    - [Upload a file](#upload-file)
    - [Upload all files from a local folder](#upload-all-files)
    - [Return storage zone files and folder data](#return-file-folder-data)
    - [Return storage zone directory files formatted](#return-files-formatted)
    - [Return storage zone directory folders formatted](#return-folders-formatted)
    - [Return storage zone directory file and folders formatted](#return-file-folders-formatted)
- [Video streaming](#video)
    - [Set video library](#set-video-library)
    - [Get video collections](#get-video-collection)
    - [Set video collection GUID](#get-video-collection)
    - [Get streams for collection](#get-streams-collection)
    - [Update stream collection](#update-stream-collection)
    - [Delete stream collection](#delete-stream-collection)
    - [Create stream collection](#create-stream-collection)
    - [List videos in library](#list-videos-library)
    - [Get video information](#get-video)
    - [Delete video](#delete-video)
    - [Create video](#create-video)
    - [Create video for collection](#create-video-collection)
    - [Upload video](#upload-video)
    - [Set thumbnail](#set-thumbnail)
    - [Get video resolutions](#video-resolutions)
    - [Get video size](#video-size)
    - [Add captions](#add-captions)
    - [Delete captions](#delete-captions)
- [DNS](#dns)
    - [Get all DNS zones](#get-dns-zones)
    - [Get DNS zone](#get-dns-zone)
    - [Add DNS zone](#add-dns)
    - [Add DNS zone full](#add-dns-full)
    - [Delete DNS zone](#delete-dns)
    - [Get DNS zone statistics](#get-dns-stats)
    - [Update DNS zone nameservers](#update-nameservers)
    - [Update DNS zone SOA email](#update-soa-email)
    - [Add DNS record](#add-dns-record)
    - [Add DNS A record](#add-a-record)
    - [Add DNS AAAA record](#add-aaaa-record)
    - [Add DNS CNAME record](#add-cname-record)
    - [Add DNS MX record](#add-mx-record)
    - [Add DNS TXT record](#add-txt-record)
    - [Add DNS NS record](#add-ns-record)
    - [Add DNS redirect record](#add-redirect)
    - [Update DNS A record](#updated-a-record)
    - [Update DNS AAAA record](#update-aaaa-record)
    - [Disable DNS record](#disable-dns)
    - [Enable DNS record](#enable-dns)
    - [Delete DNS record](#delete-dns)
- [Misc]()

### 1.9.5 changes

* Fixed video stream upload files not working.
* Fixed `purgeCache()` not working.
* Added debug request option: `$bunny->debug_request = true` to view HTTP call information.
* Added `$stream_library_access_key` and `streamLibraryAccessKey()` Can set
  with `$bunny->stream_library_access_key = '';`
* Updated table of contents in readme.

### Requirements

* PHP 8.2

For Pull zone, billing and statistics API interaction you will need your BunnyNet API key, this is found in your
dashboard in the My Account section.

The video streaming API you need the video stream library access key which is found in the settings for the library at
bunny.net.

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
* Create, edit and delete videos
* Create, edit and delete DNS zones
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
<span id="setting-api-key"></span>
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
<span id="storagezone-connect"></span>

```php
$bunny->zoneConnect($storagename, $access_key);
```

`$storagename` name of storage zone `string`

`$access_key` key/password to storage zone ```string``` **optional**

---

List storage zones
<span id="storagezone-list"></span>

```php
$bunny->listStorageZones();
```

returns `array`

---

Add a storage zone
<span id="add-storagezone"></span>

```php
$bunny->addStorageZone($newstoragezone);
```

`$newstoragezone` name of storage zone to create `string`

---

Delete a storage zone
<span id="delete-storagezone"></span>

```php
$bunny->deleteStorageZone($id);
```

`$id` id of storage zone to delete `int`

---

Get directory size
<span id="get-directory-size"></span>

```php
$dir = "profiles/admin/images";

$bunny->dirSize($dir);
```

`$dir` directory to get size of `string`

---

Return current directory
<span id="current-directory"></span>

```php
$bunny->currentDir();
```

returns `string`

---

Change directory
<span id="change-directory"></span>

```php
$bunny->changeDir($dir);
```

`$dir` directory navigation (FTP rules) `string`

---

Check folder exists
<span id="folder-exists"></span>

```php
$bunny->folderExists($path);
```

`$path` the folder path `string`

---

Check file exists
<span id="file-exists"></span>

```php
$bunny->fileExists($file);
```

`$file` the full file path including the filename `string`

---

Move to parent directory
<span id="move-to-parent-directory"></span>

```php
$bunny->moveUpOne();
```

---

Create folder in current directory
<span id="create-folder"></span>

```php
$bunny->createFolder($newfolder);
```

`$newfolder` Create a folder in current directory `string`

---

Delete folder
<span id="delete-folder"></span>

```php
$bunny->deleteFolder($name);
```

`$name` Name of folder to delete (must be empty) `string`

---

Delete a file
<span id="delete-file"></span>

```php
$bunny->deleteFile($name);
```

`$name` Name of file to delete `string`

---

Delete all files in a folder
<span id="delete-all-files"></span>

```php
$bunny->deleteAllFiles($dir);
```

`$dir` Directory to delete all files in `string`

---

Rename a file

BunnyCDN does not allow for ftp_rename so file copied to new name and then old file deleted.
<span id="rename-file"></span>

```php
$bunny->renameFile($directory, $old_file_name, $new_file_name);
```

`$directory` Directory that contains the file `string`

`$old_name` Object that is being renamed `string`

`$new_name` New name for object `string`

---

Move a file
<span id="move-file"></span>

```php
$bunny->moveFile($file, $move_to);
```

`$file` File to move `string`

`$move_to` Directory to move file to `string`

---

Download a file
<span id="download-file"></span>

```php
$bunny->downloadFile($save_as, $get_file, $mode);
```

`$save_as` Save file as `string`

`$get_file` File to download `string`

`$mode` FTP mode to use `INT`

---

Download all files in a directory
<span id="download-all-files"></span>

```php
$bunny->downloadAll($dir_dl_from, $dl_into, $mode);
```

`$dir_dl_from` Directory to download all from `string`

`$dl_into` Download into `string`

`$mode` FTP mode to use `INT`

---

Upload a file
<span id="upload-file"></span>

```php
$bunny->uploadFile($upload, $upload_as, $mode);
```

`$upload` File to upload `string`

`$upload_as` Upload as `string`

`$mode` FTP mode to use `INT`

---

Upload all files from a local folder
<span id="upload-all-files"></span>

```php
$bunny->uploadAllFiles($dir, $place, $mode);
```

`$dir` Upload all files from this directory `string`

`$place` Upload to `string`

`$mode` FTP mode to use `INT`

---

Return storage zone files and folder data (Original)
<span id="return-file-folder-data"></span>

```php
$bunny->listAllOG();
```

returns `array`

---

Return storage zone directory files formatted
<span id="return-files-formatted"></span>

```php
$bunny->listFiles($location);
```

`$location` Directory to get and return data `string`

returns `array`

---

Return storage zone directory folders formatted
<span id="return-folders-formatted"></span>

```php
$bunny->listFolders($location);
```

`$location` Directory to get and return data `string`

returns `array`

---

Return storage zone directory file and folders formatted
<span id="return-file-folders-formatted"></span>

```php
$bunny->listAll($location);
```

`$location` Directory to get and return data `string`

returns `array`

---
<span id="pullzone"></span>
<span id="list-pullzones"></span>
List all pull zones and data

```php
$bunny->listPullZones();
```

returns `array`

---
<span id="list-pullzone"></span>
List pull zones data for id

```php
$bunny->pullZoneData($id);
```

`$id` Pull zone to get data from `int`

returns `array`

---
<span id="purge-pullzone"></span>
Purge pull zone data

```php
$bunny->purgePullZone($id);
```

`$id` Pull zone to purge `int`

---
<span id="delete-pullzone"></span>
Delete pull zone data

```php
$bunny->deletePullZone($id);
```

`$id` Pull zone to delete `int`

---
<span id="hostnames-pullzone"></span>
Lists pullzone hostnames and amount

```php
$bunny->pullZoneHostnames($pullzone_id);
```

---
<span id="add-hostname-pullzone"></span>
Add hostname to pull zone

```php
$bunny->addHostnamePullZone($id, $hostname);
```

`$id` Pull zone hostname will be added to `int`

`$hostname` Hostname to add `string`

---
<span id="remove-hostname-pullzone"></span>
Remove a hostname from pull zone

```php
$bunny->removeHostnamePullZone($id, $hostname);
```

`$id` Pull zone hostname be removed from `int`

`$hostname` Hostname to remove `string`

---
<span id="ssl-pullzone"></span>
Change force SSL status for pull zone

```php
$bunny->forceSSLPullZone($id, $hostname, $force_ssl);
```

`$id` Pull zone hostname change status `int`

`$hostname` Affected hostname  `string`

`$force_ssl` True = on, FALSE = off  `bool`

---
<span id="ip-block-pullzone"></span>
Add ip to block for pullzone

```php
$bunny->addBlockedIpPullZone($pullzone_id, $ip, $db_log = false);
```

---
<span id="ip-unblock-pullzone"></span>
Un block an ip for pullzone

```php
$bunny->unBlockedIpPullZone($pullzone_id, $ip, $db_log = false);
```

---
<span id="ip-blocked-pullzone"></span>
List all blocked ip's for pullzone

```php
$bunny->listBlockedIpPullZone($pullzone_id);
```

---
<span id="purge-url-pullzone"></span>
Purge cache for a URL

```php
$bunny->purgeCache($url, $async = false);
```

`$url` Purge cache for this url  `string`

`$async` Dont wait for the purge before returning result  `bool`

---
<span id="logs-pullzone"></span>
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

Calling and setting stream library access key

```php
require __DIR__ . '/vendor/autoload.php';

use Corbpie\BunnyCdn\BunnyAPIStream;

$bunny = new BunnyAPIStream();

$bunny->streamLibraryAccessKey('XXXX-XXXXX-XXXX-XXXX');

//Or set stream library access key at line 14 in BunnyAPI.php
```

---

**You can only get the video library id from your bunny.net stream library page**
<span id="set-video-library"></span>
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
<span id="get-video-collection"></span>
Get video collections for library id

Requires ```setStreamLibraryId()``` to be set.

```php
$bunny->getStreamCollections($page, $items_pp,$order_by);
```

`$page` page number `int`

`$items_pp` items to show `int`

`$order_by` order by `string`

---
<span id="get-streams-collection"></span>
Get streams for a collection

Requires ```setStreamLibraryId()``` and ```setStreamCollectionGuid()``` to be set.

```php
$bunny->getStreamForCollection();
```

---
<span id="update-stream-collection"></span>
Update stream collection

Requires ```setStreamLibraryId()``` and ```setStreamCollectionGuid()``` to be set.

```php
$bunny->updateCollection($updated_collection_name);
```

`$updated_collection_name` the name to update video collection to `string`

---
<span id="delete-stream-collection"></span>
Delete stream collection

Requires ```setStreamLibraryId()``` and ```setStreamCollectionGuid()``` to be set.

```php
$bunny->deleteCollection();
```

---
<span id="create-stream-collection"></span>
Create stream collection

Requires ```setStreamLibraryId()``` to be set.

```php
$bunny->createCollection($new_collection_name);
```

`$new_collection_name` the name for your new video collection `string`

---
<span id="list-videos-library"></span>
List videos for library

Requires ```setStreamLibraryId()``` to be set.

```php
$bunny->listVideos($collection_guid);
```

`$collection_guid` video collection guid `string`

---
<span id="get-video"></span>
Get video information

Requires ```setStreamLibraryId()``` to be set.

```php
$bunny->getVideo($collection_guid);
```

`$collection_guid` video collection guid `string`

---
<span id="delete-video"></span>
Delete video

Requires ```setStreamLibraryId()``` to be set.

```php
$bunny->deleteVideo($collection_guid);
```

`$library_id` library id `int`

`$collection_guid` video collection guid `string`

---
<span id="create-video"></span>
Create video

Requires ```setStreamLibraryId()``` to be set.

```php
$bunny->createVideo($video_title);
```

`$video_title` video title `string`


---
<span id="create-video-collection"></span>
Create video for collection

Requires ```setStreamLibraryId()``` and ```setStreamCollectionGuid()```  to be set.

```php
$bunny->createVideoForCollection($video_title);
```

`$video_title` video title `string`

---
<span id="upload-video"></span>
Upload video

Requires ```setStreamLibraryId()``` to be set.

Need to use ```createVideo()``` first to get video guid

```php
$bunny->uploadVideo($video_guid, $video_to_upload);
```

`$video_guid` video guid `string`

`$video_to_upload` video filename `string`

---
<span id="set-thumbnail"></span>
Set thumbnail for video

Requires ```setStreamLibraryId()``` to be set.

```php
$bunny->setThumbnail($video_guid, $thumbnail_url);
```

`$video_guid` video guid `string`

`$thumbnail_url` image url `string`

---
<span id="video-resolutions"></span>
Get video resolutions

Requires ```setStreamLibraryId()``` to be set.

```php
$bunny->videoResolutionsArray($video_guid);
```

---
<span id="video-size"></span>
Get video size

Requires ```setStreamLibraryId()``` to be set.

```php
$bunny->videoSize($video_guid);
```

---
<span id="add-captions"></span>
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
<span id="delete-captions"></span>
Delete captions

Requires ```setStreamLibraryId()``` to be set.

```php
$bunny->deleteCaptions($library_id, $video_guid, $srclang);
```

`$video_guid` video guid `string`

`$srclang` captions srclang `string`

---

### DNS zone interaction
<span id="dns"></span>

```php
require __DIR__ . '/vendor/autoload.php';

use Corbpie\BunnyCdn\BunnyAPIDNS;

$bunny = new BunnyAPIDNS();

```

---

<span id="get-dns-zones"></span>
Get DNS all zones

```php
$bunny->getDNSZones();
```

`$library_id` stream library id `int`

---
<span id="get-dns-zone"></span>
Get DNS zone

```php
$bunny->getDNSZone($zone_id);
```

`$zone_id` DNS zone id `int`

---

<span id="add-dns"></span>
Add a DNS zone

```php
$bunny->addDNSZone($domain, $logging);
```

`$domain` domain name `string`
`$logging` use logging `bool`

---

<span id="add-dns-full"></span>
Add a DNS zone full

```php
$parameters = array(
    'Domain' => 'zonedomain.com', 'NameserversDetected' => true, 'CustomNameserversEnabled' => true,
    'Nameserver1' => 'customns1.com', 'Nameserver2' => 'customns2.com', 'SoaEmail' => 'contact@zonedomain.com',
    'DateModified' => '2022-08-18 23:59:59', 'DateCreated' => '2022-08-18 23:59:59', 'NameserversNextCheck' => '2022-08-28 23:59:59',
    'LoggingEnabled' => true, 'LoggingIPAnonymizationEnabled' => true
);
$bunny->addDNSZoneFull($parameters);
```

`$parameters` parameters to create `array`

---

<span id="delete-dns"></span>
Delete DNS zone

```php
$bunny->deleteDNSZone($zone_id);
```

`$zone_id` DNS zone id `int`

---

<span id="get-dns-stats"></span>
DNS zone statistics

```php
$bunny->getDNSZoneStatistics($zone_id);
```

`$zone_id` DNS zone id `int`

---

<span id="update-nameservers"></span>
Update DNS nameservers

```php
$bunny->updateDNSZoneNameservers($zone_id, $custom_ns, $ns_one, $ns_two);
```

`$zone_id` DNS zone id `int`
`$custom_ns` use custom nameservers `bool`
`$ns_one` NS one `string`
`$ns_two` NS two `string`

---

<span id="update-soa-email"></span>
Update DNS SOA email

```php
$bunny->updateDNSZoneNameservers($zone_id, $soa_email);
```

`$zone_id` DNS zone id `int`
`$soa_email` NS one `string`

---

<span id="add-dns-record"></span>
Add a DNS record by using parameters https://docs.bunny.net/reference/dnszonepublic_addrecord

```php
$parameters = array('Type' => 0, 'Ttl' => 120, 'Accelerated' => true, 'Weight' => 200);
$bunny->addDNSRecord($zone_id, $name, $value, $parameters);
```

`$zone_id` DNS zone id `int`
`$name` name `string`
`$value` IP address `string`
`$parameters` `array`

---

<span id="add-a-record"></span>
Add DNS A record

```php
$bunny->addDNSRecordA($zone_id, $hostname, $ipv4);
```

`$zone_id` DNS zone id `int`
`$hostname` hostname `string`
`$ipv4` IPv4 address `string`

---

<span id="add-aaaa-record"></span>
Add DNS AAAA record

```php
$bunny->addDNSRecordAAAA($zone_id, $hostname, $ipv6);
```

`$zone_id` DNS zone id `int`
`$hostname` hostname `string`
`$ipv6` IPv6 address `string`

---

<span id="add-cname-record"></span>
Add DNS CNAME record

```php
$bunny->addDNSRecordCNAME($zone_id, $hostname, $target);
```

`$zone_id` DNS zone id `int`
`$hostname` hostname `string`
`$target` `string`

---

<span id="add-mx-record"></span>
Add DNS MX record

```php
$bunny->addDNSRecordMX($zone_id, $hostname, $mail, $priority);
```

`$zone_id` DNS zone id `int`
`$hostname` hostname `string`
`$mail` mail server `string`
`$priority` `int`

---

<span id="add-txt-record"></span>
Add DNS TXT record

```php
$bunny->addDNSRecordTXT($zone_id, $hostname, $content);
```

`$zone_id` DNS zone id `int`
`$hostname` hostname `string`
`$content` txt contents `string`

---

<span id="add-ns-record"></span>
Add DNS NS record

```php
$bunny->addDNSRecordNS($zone_id, $hostname, $target);
```

`$zone_id` DNS zone id `int`
`$hostname` hostname `string`
`$target` `string`

---

<span id="add-redirect"></span>
Add DNS redirect

```php
$bunny->addDNSRecordRedirect($zone_id, $hostname, $url);
```

`$zone_id` DNS zone id `int`
`$hostname` hostname `string`
`$url` redirect to `string`

---

<span id="updated-a-record"></span>
Update DNS A record

```php
$bunny->updateDNSRecordA($zone_id, $dns_id, $hostname, $ipv4);
```

`$zone_id` DNS zone id `int`
`$dns_id` DNS record id `int`
`$hostname` hostname `string`
`$ipv4` ipv4 address `string`

---

<span id="update-aaaa-record"></span>
Update DNS AAAA record

```php
$bunny->updateDNSRecordAAAA($zone_id, $dns_id, $hostname, $ipv6);
```

`$zone_id` DNS zone id `int`
`$dns_id` DNS record id `int`
`$hostname` hostname `string`
`$ipv6` ipv6 address `string`

---

<span id="disable-dns"></span>
Disable DNS record

```php
$bunny->disableDNSRecord($zone_id, $dns_id);
```

`$zone_id` DNS zone id `int`
`$dns_id` DNS record id `int`

---

<span id="enable-dns"></span>
Enable DNS record

```php
$bunny->enableDNSRecord($zone_id, $dns_id);
```

`$zone_id` DNS zone id `int`
`$dns_id` DNS record id `int`

---

<span id="delete-dns"></span>
Delete DNS record

```php
$bunny->deleteDNSRecord($zone_id, $dns_id);
```

`$zone_id` DNS zone id `int`
`$dns_id` DNS record id `int`

---