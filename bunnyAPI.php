<?php

/**
 * Bunny CDN storage zone API class
 * @version  1.0
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
     * @throws Exception
     */
    //public function __construct($api_key)
    public function apiKey($api_key)
    {
        if (!isset($api_key) or trim($api_key) == '') {
            throw new Exception("You must provide an API key");
        }
        $this->api_key = $api_key;
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
            return "Connection made to " . (self::HOSTNAME) . "";
        } else {
            throw new Exception("Could not make FTP connection to " . (self::HOSTNAME) . "");
        }
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
     * @return string
     * @throws Exception
     */
    public function purgePullZone($id)
    {
        if (is_null($this->api_key))
            throw new Exception("apiKey() is not set");
        return $this->APIcall('POST', "pullzone/$id/purgeCache");
    }

    /**
     * Delete pull zone for id
     * @param int $id
     * @return string
     * @throws Exception
     */
    public function deletePullZone($id)
    {
        if (is_null($this->api_key))
            throw new Exception("apiKey() is not set");
        return $this->APIcall('DELETE', "pullzone/$id");
    }

    /**
     * Add hostname to pull zone for id
     * @param int $id
     * @param string $hostname
     * @return string
     * @throws Exception
     */
    public function addHostnamePullZone($id, $hostname)
    {
        if (is_null($this->api_key))
            throw new Exception("apiKey() is not set");
        return $this->APIcall('POST', 'pullzone/addHostname', json_encode(array("PullZoneId" => $id, "Hostname" => $hostname)));
    }

    /**
     * Remove hostname for pull zone
     * @param int $id
     * @param string $hostname
     * @return string
     * @throws Exception
     */
    public function removeHostnamePullZone($id, $hostname)
    {
        if (is_null($this->api_key))
            throw new Exception("apiKey() is not set");
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
     * @param string $date Must be within past 3 days (dd-mm-yy)
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
     * @param $name
     * @return string
     * @throws Exception
     */
    public function addStorageZone($name)
    {
        if (is_null($this->api_key))
            throw new Exception("apiKey() is not set");
        return $this->APIcall('POST', 'storagezone', json_encode(array("Name" => $name)));
    }

    /**
     * Delete storage zone
     * @param int $id
     * @return string
     * @throws Exception
     */
    public function deleteStorageZone($id)
    {
        if (is_null($this->api_key))
            throw new Exception("apiKey() is not set");
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
     * @return string
     * @throws Exception
     */
    public function createFolder($name)
    {
        if (is_null($this->connection))
            throw new Exception("zoneConnect() is not set");
        if (ftp_mkdir($this->connection, $name)) {
            return "Successfully created folder $name";
        } else {
            throw new Exception("Could not create folder $name");
        }
    }

    /**
     * Delete a folder (if empty)
     * @param string $name folder name to delete
     * @return string
     * @throws Exception
     */
    public function deleteFolder($name)
    {
        if (is_null($this->connection))
            throw new Exception("zoneConnect() is not set");
        if (ftp_rmdir($this->connection, $name)) {
            return "Successfully deleted $name";
        } else {
            throw new Exception("Could not delete $name");
        }
    }

    /**
     * Delete a file
     * @param string $name file to delete
     * @return string
     * @throws Exception
     */
    public function deleteFile($name)
    {
        if (is_null($this->connection))
            throw new Exception("zoneConnect() is not set");
        if (ftp_delete($this->connection, $name)) {
            return "Successfully deleted $name";
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
                    echo "Deleted $full_name<br>";
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
     * @return string
     * @throws Exception
     */
    public function uploadAllFiles($dir, $place, $mode = FTP_BINARY)
    {
        if (is_null($this->connection))
            throw new Exception("zoneConnect() is not set");
        $obj = scandir($dir);
        foreach ($obj as $file) {
            if (!is_dir($file)) {
                if (ftp_put($this->connection, "" . $place . "$file", "$dir/$file", $mode)) {
                    echo "Successfully uploaded <b>" . $place . "$file</b> as <b>" . $place . "/" . $file . "</b>";
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
            return "Successfully changed to $moveto";
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
            return "Successfully moved to parent dir";
        } else {
            throw new Exception("Error moving to parent dir");
        }
    }

    /**
     * rename a file or folder
     * @param string $old current
     * @param string $new rename to
     * @return string
     * @throws Exception
     */
    public function rename($old, $new)
    {
        if (is_null($this->connection))
            throw new Exception("zoneConnect() is not set");
        if (ftp_rename($this->connection, $old, $new)) {
            return "Successfully renamed $old to $new";
        } else {
            throw new Exception("Error renaming $old to $new");
        }
    }

    /**
     * move a file
     * @param string $file 'path/filename.mp4'
     * @param string $move_to 'path/path/filename.mp4'
     * @return string
     * @throws Exception
     */
    public function moveFile($file, $move_to)
    {
        if (is_null($this->connection))
            throw new Exception("zoneConnect() is not set");
        if (ftp_rename($this->connection, $file, $move_to)) {
            return "Successfully renamed $file to $move_to";
        } else {
            throw new Exception("Error renaming $file to $move_to");
        }
    }

    /**
     * Download a file
     * @param string $save_as Save as when downloaded
     * @param string $get_file File to download
     * @param int $mode
     * @return string
     * @throws Exception
     */
    public function downloadFile($save_as, $get_file, $mode = FTP_BINARY)
    {
        if (is_null($this->connection))
            throw new Exception("zoneConnect() is not set");
        if (ftp_get($this->connection, $save_as, $get_file, $mode)) {
            return "Successfully downloaded <b>$get_file</b> as <b>$save_as</b>";
        } else {
            throw new Exception("Error downloading $get_file as $save_as");
        }
    }

    /**
     * Download all files in a directory
     * @param string $dir_dl_from directory to download all from
     * @param string $dl_into local folder to download into
     * @param int $mode FTP mode for download
     * @return string
     * @throws Exception
     */
    public function downloadAll($dir_dl_from = '', $dl_into = '', $mode = FTP_BINARY)
    {
        if (is_null($this->connection))
            throw new Exception("zoneConnect() is not set");
        $url = (self::STORAGE_API_URL);
        $array = json_decode(file_get_contents("$url/$this->storage_name" . $dir_dl_from . "/?AccessKey=$this->access_key"), true);
        foreach ($array as $value) {
            if ($value['IsDirectory'] == false) {
                $file_name = $value['ObjectName'];
                if (ftp_get($this->connection, "" . $dl_into . "$file_name", $file_name, $mode)) {
                    echo "Successfully downloaded <b>$file_name</b> to <b>" . $dl_into . "$file_name</b>";
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
     * @return string
     * @throws Exception
     */
    public function uploadFile($upload, $upload_as, $mode = FTP_BINARY)
    {
        if (is_null($this->connection))
            throw new Exception("zoneConnect() is not set");
        if (ftp_put($this->connection, $upload_as, $upload, $mode)) {
            return "Successfully uploaded <b>$upload</b> as <b>$upload_as</b>";
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
            return "Connection to " . (self::HOSTNAME) . " successfully closed";
        } else {
            throw new Exception("Error closing connection to " . (self::HOSTNAME) . "");
        }
    }
}