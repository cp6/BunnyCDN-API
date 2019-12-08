<?php

/**
 * Bunny CDN storage zone API class
 * @version  1.1
 */
class BunnyAPI
{
    const API_URL = 'https://bunnycdn.com/api/';//URL for BunnyCDN API
    const STORAGE_API_URL = 'https://storage.bunnycdn.com/';//URL for storage based API
    const HOSTNAME = 'storage.bunnycdn.com';//FTP hostname
    private $api_key;
    private $access_key;
    private $storage_name;
    private $connection;
    private $data;

    /**
     * Sets access key and the storage name, makes FTP connection with this
     * @param string $api_key (storage zone password)
     * @return string
     * @throws Exception
     */
    //public function __construct($api_key)
    public function apiKey($api_key)
    {
        if (!isset($api_key) or trim($api_key) == '') {
            throw new Exception("You must provide an API key");
        }
        $this->api_key = $api_key;
        return json_encode(array('response' => 'success', 'action' => 'apiKey'));
    }

    /**
     * Sets and creates auth + FTP connection to a storage zone
     * @param string $storage_name
     * @param string $access_key
     * @return string
     * @throws Exception
     */
    public function zoneConnect($storage_name, $access_key)
    {
        $this->storage_name = $storage_name;
        $this->access_key = $access_key;
        $conn_id = ftp_connect((self::HOSTNAME));
        $login = ftp_login($conn_id, $storage_name, $access_key);
        ftp_pasv($conn_id, true);
        if ($conn_id) {
            $this->connection = $conn_id;
            return json_encode(array('response' => 'success', 'action' => 'zoneConnect'));
        } else {
            throw new Exception("Could not make FTP connection to " . (self::HOSTNAME) . "");
        }
    }

    /**
     * Sets the MySQL connection (Optional! only if using MySQL functions)
     * @return object
     */
    public function db_connect()
    {
        $db_user = 'root';
        $db_password = '';
        $db = "mysql:host=127.0.0.1;dbname=bunnycdn;charset=utf8mb4";
        $options = array(
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC);
        return new PDO($db, $db_user, $db_password, $options);
    }

    /**
     * cURL execution with headers and parameters
     * @param string $method
     * @param string $url
     * @param boolean $params
     * @return string
     */
    private function APIcall($method, $url, $params = false)
    {
        $curl = curl_init();
        switch ($method) {
            case "POST":
                curl_setopt($curl, CURLOPT_POST, 1);
                if ($params)
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
                break;
            case "PUT":
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
                if ($params)
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
                break;
            case "DELETE":
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
                if ($params)
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
                break;
            default:
                if ($params)
                    $url = sprintf("%s?%s", $url, http_build_query($params));
        }
        curl_setopt($curl, CURLOPT_URL, "" . (self::API_URL) . "$url");
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json",
            "AccessKey: " . $this->api_key . ""));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        $result = curl_exec($curl);
        curl_close($curl);
        $this->data = $result;
        return $result;
    }

    /**
     * Returns all pull zones and information
     * @return string
     * @throws Exception
     */
    public function listPullZones()
    {
        if (is_null($this->api_key))
            throw new Exception("apiKey() is not set");
        return $this->APIcall('GET', 'pullzone');
    }

    /**
     * Returns pull zone information for id
     * @param int $id
     * @return string
     * @throws Exception
     */
    public function pullZoneData($id)
    {
        if (is_null($this->api_key))
            throw new Exception("apiKey() is not set");
        return $this->APIcall('GET', "pullzone/$id");
    }

    /**
     * Purge the pull zone with id
     * @param int $id
     * @param int $db_log
     * @return string
     * @throws Exception
     */
    public function purgePullZone($id, $db_log = 0)
    {
        if (is_null($this->api_key))
            throw new Exception("apiKey() is not set");
        if ($db_log == 1) {
            $this->actionsLog('PURGE PZ', $id);
        }
        return $this->APIcall('POST', "pullzone/$id/purgeCache");
    }

    /**
     * Delete pull zone for id
     * @param int $id
     * @param int $db_log
     * @return string
     * @throws Exception
     */
    public function deletePullZone($id, $db_log = 0)
    {
        if (is_null($this->api_key))
            throw new Exception("apiKey() is not set");
        if ($db_log == 1) {
            $this->actionsLog('DELETE PZ', $id);
        }
        return $this->APIcall('DELETE', "pullzone/$id");
    }

    /**
     * Add hostname to pull zone for id
     * @param int $id
     * @param string $hostname
     * @param int $db_log
     * @return string
     * @throws Exception
     */
    public function addHostnamePullZone($id, $hostname, $db_log = 0)
    {
        if (is_null($this->api_key))
            throw new Exception("apiKey() is not set");
        if ($db_log == 1) {
            $this->actionsLog('ADD HN', $id, $hostname);
        }
        return $this->APIcall('POST', 'pullzone/addHostname', json_encode(array("PullZoneId" => $id, "Hostname" => $hostname)));
    }

    /**
     * Remove hostname for pull zone
     * @param int $id
     * @param string $hostname
     * @param int $db_log
     * @return string
     * @throws Exception
     */
    public function removeHostnamePullZone($id, $hostname, $db_log = 0)
    {
        if (is_null($this->api_key))
            throw new Exception("apiKey() is not set");
        if ($db_log == 1) {
            $this->actionsLog('REMOVE HN', $id, $hostname);
        }
        return $this->APIcall('DELETE', 'pullzone/deleteHostname', json_encode(array("id" => $id, "hostname" => $hostname)));
    }

    /**
     * Set Force SSL status for pull zone
     * @param int $id
     * @param string $hostname
     * @param boolean $force_ssl
     * @return string
     * @throws Exception
     */
    public function forceSSLPullZone($id, $hostname, $force_ssl = true)
    {
        if (is_null($this->api_key))
            throw new Exception("apiKey() is not set");
        return $this->APIcall('POST', 'pullzone/setForceSSL', json_encode(array("PullZoneId" => $id, "HostName" => $hostname, 'ForceSSl' => $force_ssl)));
    }

    /**
     * Returns log data array for pull zone id
     * @param int $id
     * @param string $date Must be within past 3 days (mm-dd-yy)
     * @return array
     * @throws Exception
     */
    public function pullZoneLogs($id, $date)
    {
        if (is_null($this->api_key))
            throw new Exception("apiKey() is not set");
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, "https://logging.bunnycdn.com/$date/$id.log");
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json",
            "AccessKey: " . $this->api_key . ""));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        $result = curl_exec($curl);
        curl_close($curl);
        $linetoline = explode("\n", $result);
        $line = array();
        foreach ($linetoline as $v1) {
            if (isset($v1) && strlen($v1) > 0) {
                $log_format = explode('|', $v1);
                $details = array(
                    'cache_result' => $log_format[0],
                    'status' => intval($log_format[1]),
                    'datetime' => date('Y-m-d H:i:s', round($log_format[2] / 1000, 0)),
                    'bytes' => intval($log_format[3]),
                    'ip' => $log_format[5],
                    'referer' => $log_format[6],
                    'file_url' => $log_format[7],
                    'user_agent' => $log_format[9],
                    'request_id' => $log_format[10],
                    'cdn_dc' => $log_format[8],
                    'zone_id' => intval($log_format[4]),
                    'country_code' => $log_format[11]
                );
                array_push($line, $details);
            }
        }
        return $line;
    }

    /**
     * Returns all storage zones and information
     * @return string
     * @throws Exception
     */
    public function listStorageZones()
    {
        if (is_null($this->api_key))
            throw new Exception("apiKey() is not set");
        return $this->APIcall('GET', 'storagezone');
    }

    /**
     * Create storage zone
     * @param string $name
     * @param int $db_log
     * @return string
     * @throws Exception
     */
    public function addStorageZone($name, $db_log = 0)
    {
        if (is_null($this->api_key))
            throw new Exception("apiKey() is not set");
        if ($db_log == 1) {
            $this->actionsLog('ADD SZ', $name);
        }
        return $this->APIcall('POST', 'storagezone', json_encode(array("Name" => $name)));
    }

    /**
     * Delete storage zone
     * @param int $id
     * @param int $db_log
     * @return string
     * @throws Exception
     */
    public function deleteStorageZone($id, $db_log = 0)
    {
        if (is_null($this->api_key))
            throw new Exception("apiKey() is not set");
        if ($db_log == 1) {
            $this->actionsLog('DELETE SZ', $id);
        }
        return $this->APIcall('DELETE', "storagezone/$id");
    }

    /**
     * Purge cache for a URL
     * @param $url
     * @return string
     * @throws Exception
     */
    public function purgeCache($url)
    {
        if (is_null($this->api_key))
            throw new Exception("apiKey() is not set");
        return $this->APIcall('POST', 'purge', json_encode(array("url" => $url)));
    }

    /**
     * Get statistics
     * @return string
     * @throws Exception
     */
    public function getStatistics()
    {
        if (is_null($this->api_key))
            throw new Exception("apiKey() is not set");
        return $this->APIcall('GET', 'statistics');
    }

    /**
     * Get billing information
     * @return string
     * @throws Exception
     */
    public function getBilling()
    {
        if (is_null($this->api_key))
            throw new Exception("apiKey() is not set");
        return $this->APIcall('GET', 'billing');
    }

    /**
     * Get current account balance
     * @return string
     * @throws Exception
     */
    public function balance()
    {
        if (is_null($this->data))
            throw new Exception("getBilling() must be called first");
        return json_decode($this->data, true)['Balance'];
    }

    /**
     * Gets current month charge amount
     * @return string
     * @throws Exception
     */
    public function monthCharges()
    {
        if (is_null($this->data))
            throw new Exception("getBilling() must be called first");
        return json_decode($this->data, true)['ThisMonthCharges'];
    }

    /**
     * Array for month charges per zone
     * @return array
     * @throws Exception
     */
    public function monthChargeBreakdown()
    {
        if (is_null($this->data))
            throw new Exception("getBilling() must be called first");
        $ar = json_decode($this->data, true);
        return array('storage' => $ar['MonthlyChargesStorage'], 'EU' => $ar['MonthlyChargesEUTraffic'],
            'US' => $ar['MonthlyChargesUSTraffic'], 'ASIA' => $ar['MonthlyChargesASIATraffic'],
            'SA' => $ar['MonthlyChargesSATraffic']);
    }

    /**
     * Apply a coupon code
     * @param $code
     * @return string
     * @throws Exception
     */
    public function applyCoupon($code)
    {
        if (is_null($this->api_key))
            throw new Exception("apiKey() is not set");
        return $this->APIcall('POST', 'applycode', json_encode(array("couponCode" => $code)));
    }

    /**
     * Create a folder
     * @param string $name folder name to create
     * @param int $db_log
     * @return string
     * @throws Exception
     */
    public function createFolder($name, $db_log = 0)
    {
        if (is_null($this->connection))
            throw new Exception("zoneConnect() is not set");
        if (ftp_mkdir($this->connection, $name)) {
            if ($db_log == 1) {
                $this->actionsLog('CREATE FOLDER', $name);
            }
            return json_encode(array('response' => 'success', 'action' => 'createFolder'));
        } else {
            throw new Exception("Could not create folder $name");
        }
    }

    /**
     * Delete a folder (if empty)
     * @param string $name folder name to delete
     * @param int $db_log
     * @return string
     * @throws Exception
     */
    public function deleteFolder($name, $db_log = 0)
    {
        if (is_null($this->connection))
            throw new Exception("zoneConnect() is not set");
        if (ftp_rmdir($this->connection, $name)) {
            if ($db_log == 1) {
                $this->actionsLog('DELETE FOLDER', $name);
            }
            return json_encode(array('response' => 'success', 'action' => 'deleteFolder'));
        } else {
            throw new Exception("Could not delete $name");
        }
    }

    /**
     * Delete a file
     * @param string $name file to delete
     * @param int $db_log log action to deleted_files table
     * @return string
     * @throws Exception
     */
    public function deleteFile($name, $db_log = 0)
    {
        if (is_null($this->connection))
            throw new Exception("zoneConnect() is not set");
        if (ftp_delete($this->connection, $name)) {
            if ($db_log == 1) {
                $path_data = pathinfo($name);
                $db = $this->db_connect();
                $insert = $db->prepare('INSERT INTO `deleted_files` (`zone_name`, `file`, `dir`) VALUES (?, ?, ?)');
                $insert->execute([$this->storage_name, $path_data['basename'], $path_data['dirname']]);
            }
            return json_encode(array('response' => 'success', 'action' => 'deleteFile'));
        } else {
            throw new Exception("Could not delete $name");
        }
    }

    /**
     * Delete all files in a folder
     * @param string $dir delete all files in here
     * @return string
     * @throws Exception
     */
    public function deleteAllFiles($dir)
    {
        if (is_null($this->connection))
            throw new Exception("zoneConnect() is not set");
        $url = (self::STORAGE_API_URL);
        $array = json_decode(file_get_contents("$url/$this->storage_name/" . $dir . "/?AccessKey=$this->access_key"), true);
        foreach ($array as $value) {
            if ($value['IsDirectory'] == false) {
                $file_name = $value['ObjectName'];
                $full_name = "$dir/$file_name";
                if (ftp_delete($this->connection, $full_name)) {
                    echo json_encode(array('response' => 'success', 'action' => 'deleteAllFiles'));
                } else {
                    throw new Exception("Could not delete $full_name");
                }
            }
        }
    }

    /**
     * Upload all files in a directory to a folder
     * @param string $dir upload all files from here
     * @param string $place upload the files to this location
     * @param int $mode
     * @param int $db_log
     * @return string
     * @throws Exception
     */
    public function uploadAllFiles($dir, $place, $mode = FTP_BINARY, $db_log = 0)
    {
        if (is_null($this->connection))
            throw new Exception("zoneConnect() is not set");
        $obj = scandir($dir);
        foreach ($obj as $file) {
            if (!is_dir($file)) {
                if (ftp_put($this->connection, "" . $place . "$file", "$dir/$file", $mode)) {
                    if ($db_log == 1) {
                        $this->actionsLog('UPLOAD FILE', "" . $place . "$file", "$dir/$file");
                    }
                    echo json_encode(array('response' => 'success', 'action' => 'uploadAllFiles'));
                } else {
                    throw new Exception("Error uploading " . $place . "$file as " . $place . "/" . $file . "");
                }
            }
        }
    }

    /**
     * Returns array with file count and total size
     * @param string $dir directory to do count in
     * @return array
     * @throws Exception
     */
    public function dirSize($dir = '')
    {
        if (is_null($this->connection))
            throw new Exception("zoneConnect() is not set");
        $url = (self::STORAGE_API_URL);
        $array = json_decode(file_get_contents("$url/$this->storage_name" . $dir . "/?AccessKey=$this->access_key"), true);
        $size = 0;
        $files = 0;
        foreach ($array as $value) {
            if ($value['IsDirectory'] == false) {
                $size = ($size + $value['Length']);
                $files++;
            }
        }
        return array('dir' => $dir, 'files' => $files, 'size_b' => $size, 'size_kb' => number_format(($size / 1024), 3),
            'size_mb' => number_format(($size / 1048576), 3), 'size_gb' => number_format(($size / 1073741824), 3));
    }

    /**
     * Return current directory
     * @return string
     * @throws Exception
     */
    public function currentDir()
    {
        if (is_null($this->connection))
            throw new Exception("zoneConnect() is not set");
        return ftp_pwd($this->connection);
    }

    /**
     * Change working directory
     * @param string $moveto movement
     * @return string
     * @throws Exception
     */
    public function changeDir($moveto)
    {
        if (is_null($this->connection))
            throw new Exception("zoneConnect() is not set");
        if (ftp_chdir($this->connection, $moveto)) {
            return json_encode(array('response' => 'success', 'action' => 'changeDir'));
        } else {
            throw new Exception("Error moving to $moveto");
        }
    }

    /**
     * Move to parent directory
     * @return string
     * @throws Exception
     */
    public function moveUpOne()
    {
        if (is_null($this->connection))
            throw new Exception("zoneConnect() is not set");
        if (ftp_cdup($this->connection)) {
            return json_encode(array('response' => 'success', 'action' => 'moveUpOne'));
        } else {
            throw new Exception("Error moving to parent dir");
        }
    }

    /**
     * Renames a file
     * @note Downloads and re-uploads file as BunnyCDN has blocked ftp_rename()
     * @param string $old_dir current file directory
     * @param string $old_name current filename
     * @param string $new_dir rename file to directory
     * @param string $new_name rename file to
     * @param int $db_log log change into file_history table
     * @return string
     * @throws Exception
     */
    public function rename($old_dir, $old_name, $new_dir, $new_name, $db_log = 0)
    {
        if (is_null($this->connection))
            throw new Exception("zoneConnect() is not set");
        $path_data = pathinfo("" . $old_dir . "$old_name");
        $file_type = $path_data['extension'];
        if (ftp_get($this->connection, "tempRENAME.$file_type", "" . $old_dir . "$old_name", FTP_BINARY)) {
            if (ftp_put($this->connection, "$new_dir" . $new_name . "", "tempRENAME.$file_type", FTP_BINARY)) {
                if (ftp_delete($this->connection, "" . $old_dir . "$old_name")) {
                    if ($db_log == 1) {
                        $db = $this->db_connect();
                        $insert = $db->prepare('INSERT INTO file_history (new_name, old_name, zone_name, new_dir, old_dir) 
                                 VALUES (?, ?, ?, ?, ?)');
                        $insert->execute([$new_name, $old_name, $this->storage_name, $new_dir, $old_dir]);
                        unlink("tempRENAME.$file_type");//Delete temp file
                    }
                    return json_encode(array('response' => 'success', 'action' => 'rename'));
                }
            }
        } else {
            throw new Exception("Error renaming $old_name to $new_name");
        }
    }

    /**
     * move a file
     * @param string $file 'path/filename.mp4'
     * @param string $move_to 'path/path/filename.mp4'
     * @param int $db_log
     * @return string
     * @throws Exception
     */
    public function moveFile($file, $move_to, $db_log = 0)
    {
        if (is_null($this->connection))
            throw new Exception("zoneConnect() is not set");
        if (ftp_rename($this->connection, $file, $move_to)) {
            if ($db_log == 1) {
                $this->actionsLog('MOVE FILE', $file, $move_to);
            }
            return json_encode(array('response' => 'success', 'action' => 'moveFile'));
        } else {
            throw new Exception("Error renaming $file to $move_to");
        }
    }

    /**
     * Download a file
     * @param string $save_as Save as when downloaded
     * @param string $get_file File to download
     * @param int $mode
     * @param int $db_log
     * @return string
     * @throws Exception
     */
    public function downloadFile($save_as, $get_file, $mode = FTP_BINARY, $db_log = 0)
    {
        if (is_null($this->connection))
            throw new Exception("zoneConnect() is not set");
        if (ftp_get($this->connection, $save_as, $get_file, $mode)) {
            if ($db_log == 1) {
                $this->actionsLog('DOWNLOAD', $save_as, $get_file);
            }
            return json_encode(array('response' => 'success', 'action' => 'downloadFile'));
        } else {
            throw new Exception("Error downloading $get_file as $save_as");
        }
    }

    /**
     * Download all files in a directory
     * @param string $dir_dl_from directory to download all from
     * @param string $dl_into local folder to download into
     * @param int $mode FTP mode for download
     * @param int $db_log
     * @return string
     * @throws Exception
     */
    public function downloadAll($dir_dl_from = '', $dl_into = '', $mode = FTP_BINARY, $db_log = 0)
    {
        if (is_null($this->connection))
            throw new Exception("zoneConnect() is not set");
        $url = (self::STORAGE_API_URL);
        $array = json_decode(file_get_contents("$url/$this->storage_name" . $dir_dl_from . "/?AccessKey=$this->access_key"), true);
        foreach ($array as $value) {
            if ($value['IsDirectory'] == false) {
                $file_name = $value['ObjectName'];
                if (ftp_get($this->connection, "" . $dl_into . "$file_name", $file_name, $mode)) {
                    if ($db_log == 1) {
                        $this->actionsLog('DOWNLOAD', "" . $dl_into . "$file_name", $file_name);
                    }
                    echo json_encode(array('response' => 'success', 'action' => 'downloadAll'));
                } else {
                    throw new Exception("Error downloading $file_name to " . $dl_into . "$file_name");
                }
            }
        }
    }

    /**
     * Upload a file
     * @param string $upload File to upload
     * @param string $upload_as Save as when uploaded
     * @param int $mode
     * @param int $db_log
     * @return string
     * @throws Exception
     */
    public function uploadFile($upload, $upload_as, $mode = FTP_BINARY, $db_log = 0)
    {
        if (is_null($this->connection))
            throw new Exception("zoneConnect() is not set");
        if (ftp_put($this->connection, $upload_as, $upload, $mode)) {
            if ($db_log == 1) {
                $this->actionsLog('UPLOAD', $upload, $upload_as);
            }
            return json_encode(array('response' => 'success', 'action' => 'uploadFile'));
        } else {
            throw new Exception("Error uploading $upload as $upload_as");
        }
    }

    /**
     * Set Json type header (Pretty print JSON in Firefox)
     * @return string
     */
    public function jsonHeader()
    {
        header('Content-Type: application/json');
    }

    /**
     * Returns official BunnyCDN data from storage instance
     * @return string
     * @throws Exception
     */
    public function listAllOG()
    {
        if (is_null($this->connection))
            throw new Exception("zoneConnect() is not set");
        $url = (self::STORAGE_API_URL);
        return file_get_contents("$url/$this->storage_name/?AccessKey=$this->access_key");
    }

    /**
     * Returns formatted Json data about all files in location
     * @param string $location
     * @return string
     * @throws Exception
     */
    public function listFiles($location = '')
    {
        if (is_null($this->connection))
            throw new Exception("zoneConnect() is not set");
        $url = (self::STORAGE_API_URL);
        $array = json_decode(file_get_contents("$url/$this->storage_name" . $location . "/?AccessKey=$this->access_key"), true);
        $items = array('storage_name' => "" . $this->storage_name, 'current_dir' => $location, 'data' => array());
        foreach ($array as $value) {
            if ($value['IsDirectory'] == false) {
                $created = date('Y-m-d H:i:s', strtotime($value['DateCreated']));
                $last_changed = date('Y-m-d H:i:s', strtotime($value['LastChanged']));
                if (isset(pathinfo($value['ObjectName'])['extension'])) {
                    $file_type = pathinfo($value['ObjectName'])['extension'];
                } else {
                    $file_type = null;
                }
                $file_name = $value['ObjectName'];
                $size_kb = floatval(($value['Length'] / 1024));
                $guid = $value['Guid'];
                $items['data'][] = array('name' => $file_name, 'file_type' => $file_type, 'size' => $size_kb, 'created' => $created,
                    'last_changed' => $last_changed, 'guid' => $guid);
            }
        }
        return json_encode($items);
    }

    /**
     * Returns formatted Json data about all folders in location
     * @param string $location
     * @return string
     * @throws Exception
     */
    public function listFolders($location = '')
    {
        if (is_null($this->connection))
            throw new Exception("zoneConnect() is not set");
        $url = (self::STORAGE_API_URL);
        $array = json_decode(file_get_contents("$url/$this->storage_name" . $location . "/?AccessKey=$this->access_key"), true);
        $items = array('storage_name' => $this->storage_name, 'current_dir' => $location, 'data' => array());
        foreach ($array as $value) {
            $created = date('Y-m-d H:i:s', strtotime($value['DateCreated']));
            $last_changed = date('Y-m-d H:i:s', strtotime($value['LastChanged']));
            $foldername = $value['ObjectName'];
            $guid = $value['Guid'];
            if ($value['IsDirectory'] == true) {
                $items['data'][] = array('name' => $foldername, 'created' => $created,
                    'last_changed' => $last_changed, 'guid' => $guid);
            }
        }
        return json_encode($items);
    }

    /**
     * Returns formatted Json data about all files and folders in location
     * @param string $location
     * @return string
     * @throws Exception
     */
    function listAll($location = '')
    {
        if (is_null($this->connection))
            throw new Exception("zoneConnect() is not set");
        $url = (self::STORAGE_API_URL);
        $array = json_decode(file_get_contents("$url/$this->storage_name" . $location . "/?AccessKey=$this->access_key"), true);
        $items = array('storage_name' => "" . $this->storage_name, 'current_dir' => $location, 'data' => array());
        foreach ($array as $value) {
            $created = date('Y-m-d H:i:s', strtotime($value['DateCreated']));
            $last_changed = date('Y-m-d H:i:s', strtotime($value['LastChanged']));
            $file_name = $value['ObjectName'];
            $guid = $value['Guid'];
            if ($value['IsDirectory'] == true) {
                $file_type = null;
                $size_kb = null;
            } else {
                if (isset(pathinfo($value['ObjectName'])['extension'])) {
                    $file_type = pathinfo($value['ObjectName'])['extension'];
                } else {
                    $file_type = null;
                }
                $size_kb = floatval(($value['Length'] / 1024));
            }
            $items['data'][] = array('name' => $file_name, 'file_type' => $file_type, 'size' => $size_kb, 'is_dir' => $value['IsDirectory'], 'created' => $created,
                'last_changed' => $last_changed, 'guid' => $guid);
        }
        return json_encode($items);
    }

    /**
     * Closes FTP connection (Optional use)
     * @return string
     * @throws Exception
     */
    public function closeConnection()
    {
        if (ftp_close($this->connection)) {
            return json_encode(array('response' => 'success', 'action' => 'closeConnection'));
        } else {
            throw new Exception("Error closing connection to " . (self::HOSTNAME) . "");
        }
    }

    /**
     * @note Below begins the MySQL database functions
     * @note These are completely optional
     * @note Please ensure that you have edited db_connect() beginning up at line 56
     * @note Also ran MySQL_database.sql file
     */

    /**
     * Inserts pull zones into `pullzones` database table
     * @return string
     */
    public function insertPullZones()
    {
        $db = $this->db_connect();
        $data = json_decode($this->listPullZones(), true);
        foreach ($data as $aRow) {
            if ($aRow['Enabled'] == true) {
                $enabled = 1;
            } else {
                $enabled = 0;
            }
            $insert = $db->prepare('INSERT IGNORE INTO `pullzones` (`id`, `name`, `origin_url`, `enabled`, `bandwidth_used`, `bandwidth_limit`,
                                `monthly_charge`, `storage_zone_id`) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
            $insert->execute([$aRow['Id'], $aRow['Name'], $aRow['OriginUrl'], $enabled,
                $aRow['MonthlyBandwidthUsed'], $aRow['MonthlyBandwidthLimit'], $aRow['MonthlyCharges'], $aRow['StorageZoneId']]);
        }
        return json_encode(array('response' => 'success', 'action' => 'insertPullZoneLogs'));
    }

    /**
     * Inserts storage zones into `storagezones` database table
     * @return string
     */
    public function insertStorageZones()
    {
        $db = $this->db_connect();
        $data = json_decode($this->listStorageZones(), true);
        foreach ($data as $aRow) {
            if ($aRow['Deleted'] == false) {
                $enabled = 1;
            } else {
                $enabled = 0;
            }
            $insert = $db->prepare('INSERT IGNORE INTO `storagezones` (`id`, `name`, `storage_used`, `enabled`, `files_stored`, `date_modified`) 
                VALUES (?, ?, ?, ?, ?, ?)');
            $insert->execute([$aRow['Id'], $aRow['Name'], $aRow['StorageUsed'], $enabled,
                $aRow['FilesStored'], $aRow['DateModified']]);
        }
        return json_encode(array('response' => 'success', 'action' => 'insertPullZoneLogs'));
    }

    /**
     * Inserts pull zone logs into `logs` database table
     * @param int $id
     * @param string $date
     * @return string
     */
    public function insertPullZoneLogs($id, $date)
    {
        $db = $this->db_connect();
        $data = $this->pullZoneLogs($id, $date);
        foreach ($data as $aRow) {
            $insert_overview = $db->prepare('INSERT IGNORE INTO `log_main` (`zid`, `rid`, `result`, `referer`, `file_url`, `datetime`) VALUES (?, ?, ?, ?, ?, ?)');
            $insert_overview->execute([$aRow['zone_id'], $aRow['request_id'], $aRow['cache_result'], $aRow['referer'],
                $aRow['file_url'], $aRow['datetime']]);
            $insert_main = $db->prepare('INSERT IGNORE INTO `log_more` (`zid`, `rid`, `status`, `bytes`, `ip`,
                `user_agent`, `cdn_dc`, `country_code`) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
            $insert_main->execute([$aRow['zone_id'], $aRow['request_id'], $aRow['status'], $aRow['bytes'], $aRow['ip'],
                $aRow['user_agent'], $aRow['cdn_dc'], $aRow['country_code']]);
        }
        return json_encode(array('response' => 'success', 'action' => 'insertPullZoneLogs'));
    }

    /**
     * Action logger for broader actions
     * @param string $task
     * @param string $file
     * @param string $file_other
     */
    public function actionsLog($task, $file, $file_other = NULL)
    {
        $db = $this->db_connect();
        $insert = $db->prepare('INSERT INTO `actions` (`task`, `zone_name`, `file`, `file_other`) VALUES (?, ?, ?, ?)');
        $insert->execute([$task, $this->storage_name, $file, $file_other]);
    }

}