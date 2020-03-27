# BunnyCDN API Class
The most comprehensive, feature packed and easy to use PHP class for the BunnyCDN pull and storage zones [API](https://bunnycdn.docs.apiary.io/).

This class whilst having a main focus on storage zone interaction includes pull zone features. 
Combining API with FTP, managing and using BunnyCDN storage zones just got easier.

### Requires

For Pull zone, billing and statistics API interaction you will need your BunnyCDN API key, this is found in your dashboard in the My Account section.

If you want to interact with storage zones you will need the name and password which can be obtained using this class below or at the storage zone panel.

## Features & abilities

* List storage zones
* Add storage zone
* Delete storage zone
* Create folder in storage zone
* Delete folder in storage zone
* Delete file in storage zone
* Delete all files in a folder in storage zone
* Download a file from storage zone
* Download all files in a folder from storage zone
* Upload file to storage zone
* Upload all files in a folder to storage zone
* Rename file or folder in storage zone
* Move file in storage zone
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
* Add pull zone
* Delete pull zone
* Purge pull zone
* Add hostname to pull zone
* Remove hostname from pull zone
* Set force SSL for pull zone
* List pull zone HTTP access logs

## Usage
Usage is simple, make sure the class file is included
```php
require_once('bunnyAPI.php');
```

Then set your API key
```php
$bunny = new bunnyAPI();
$bunny->apiKey('XXXX-XXXX-XXXX');//Bunny api key
```
---

Storage zone name and access key for storage zone interaction (**not needed if just using pull zone functions**)
```php
$bunny->zoneConnect($storagename, $access_key);
```
`$storagename` name of storage zone `string`

`$access_key` key/password to storage zone ```string```

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

Rename a file or folder

BunnyCDN does not allow for ftp_rename so file is downloaded, re-uploaded as new name and then deleted locally.
```php
$bunny->rename($old_name, $new_name);
```

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

Close connection (Optional)
```php
$bunny->closeConnection();
```


### MySQL Functions

Run `MySQL_database.sql`

Put details in `db_connect();`

---

Insert Pull zones into database
```php
$bunny->insertPullZones();
```
---

Insert Storage zones into database
```php
$bunny->insertStorageZones();
```
---

Insert Pull zone logs into database
```php
$bunny->insertPullZoneLogs($id, $date);
```
`$id` Pull zone id `int`

`$date` Date for logs, only past 3 days (mm-dd-yy) `string`

---

## TODO

* Access more refined, individual data
* Improve action logging

## VERSIONS

#### 1.1 

* Fixed rename file function
* Introduced MySQL functions (MySQL_database.sql)
* Log actions
* Store Pull and Storage zone basic data
* Fixed Storage zone requests log documentation for date (dd-mm-yy -> mm-dd-yy)
* Added formatted responses for most actions

#### 1.0

* Core actions
* Basic FTP commands for Storage zone interaction
