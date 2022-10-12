<?php

namespace Corbpie\BunnyCdn;

class BunnyAPIStorage extends BunnyAPI
{
    protected string $storage_name;

    public function zoneConnect(string $storage_name, string $access_key = ''): void
    {
        $this->storage_name = $storage_name;
        (empty($access_key)) ? $this->findStorageZoneAccessKey($storage_name) : $this->access_key = $access_key;
        $conn_id = ftp_connect((self::HOSTNAME));
        $login = ftp_login($conn_id, $storage_name, $this->access_key);
        ftp_pasv($conn_id, true);
        try {
            if (!$conn_id) {
                throw new BunnyAPIException("Could not make FTP connection to " . (self::HOSTNAME));
            }
            $this->connection = $conn_id;
        } catch (BunnyAPIException $e) {//display error message
            echo $e->errorMessage();
        }
    }

    protected function findStorageZoneAccessKey(string $storage_name): ?string
    {
        $data = $this->listStorageZones();
        foreach ($data as $zone) {
            if ($zone['Name'] === $storage_name) {
                return $this->access_key = $zone['Password'];
            }
        }
        return null;//Never found access key for said storage zone
    }

    public function listStorageZones(): array
    {
        return $this->APIcall('GET', 'storagezone');
    }

    public function addStorageZone(string $name, string $origin_url, string $main_region = 'DE', array $replicated_regions = []): array
    {
        return $this->APIcall('POST', 'storagezone', array("Name" => $name, "OriginUrl" => $origin_url, "Region" => $main_region, "ReplicationRegions" => $replicated_regions));
    }

    public function deleteStorageZone(int $id): array
    {
        return $this->APIcall('DELETE', "storagezone/$id");
    }

    public function uploadFileHTTP(string $file, string $save_as = 'folder/filename.jpg'): array
    {
        return $this->APIcall('PUT', $this->storage_name . "/" . $save_as, array('file' => $file), 'STORAGE');
    }

    public function deleteFileHTTP(string $file): array
    {
        return $this->APIcall('DELETE', $this->storage_name . "/" . $file, array(), 'STORAGE');
    }

    public function downloadFileHTTP(string $file): array
    {
        return $this->APIcall('GET', $this->storage_name . "/" . $file, array(), 'STORAGE');
    }

    public function folderExists(string $path): bool
    {
        if (!ftp_nlist($this->connection, $path)) {
            return false;
        }
        return true;
    }

    public function createFolder(string $name): array
    {
        if (ftp_mkdir($this->connection, $name)) {
            return array('response' => 'success', 'action' => __FUNCTION__, 'value' => $name);
        }
        return array('response' => 'fail', 'action' => __FUNCTION__, 'value' => $name);
    }

    public function deleteFolder(string $name): array
    {
        if (ftp_rmdir($this->connection, $name)) {
            return array('response' => 'success', 'action' => __FUNCTION__, 'value' => $name);
        }
        return array('response' => 'fail', 'action' => __FUNCTION__, 'value' => $name);
    }

    public function deleteFile(string $name): array
    {
        if (ftp_delete($this->connection, $name)) {
            return array('response' => 'success', 'action' => __FUNCTION__, 'value' => $name);
        }
        return array('response' => 'fail', 'action' => __FUNCTION__, 'value' => $name);
    }

    public function deleteAllFiles(string $dir): array
    {
        $array = json_decode(file_get_contents(self::STORAGE_API_URL . "/$this->storage_name/{$dir}/?AccessKey=" . $this->access_key), true);
        $files_deleted = 0;
        foreach ($array as $value) {
            if ($value['IsDirectory'] === false) {
                $file_name = $value['ObjectName'];
                if (ftp_delete($this->connection, "$dir/$file_name")) {
                    $files_deleted++;
                }
            }
        }
        return array('action' => __FUNCTION__, 'value' => $dir, 'files_deleted' => $files_deleted);
    }

    public function uploadAllFiles(string $dir, string $place, $mode = FTP_BINARY): array
    {
        $obj = scandir($dir);
        $files_uploaded = 0;
        foreach ($obj as $file) {
            if (!is_dir($file) && ftp_put($this->connection, $place . $file, "$dir/$file", $mode)) {
                $files_uploaded++;
            }
        }
        return array('action' => __FUNCTION__, 'value' => $dir, 'files_uploaded' => $files_uploaded);
    }

    public function getFileSize(string $file): int
    {
        return ftp_size($this->connection, $file);
    }

    public function dirSize(string $dir = ''): array
    {
        $array = json_decode(file_get_contents(self::STORAGE_API_URL . "/$this->storage_name" . $dir . "/?AccessKey=" . $this->access_key), true);
        $size = $files = 0;
        foreach ($array as $value) {
            if ($value['IsDirectory'] === false) {
                $size += $value['Length'];
                $files++;
            }
        }
        return array('dir' => $dir, 'files' => $files, 'size_b' => $size, 'size_kb' => number_format(($size / 1024), 3),
            'size_mb' => number_format(($size / 1048576), 3), 'size_gb' => number_format(($size / 1073741824), 3));
    }

    public function currentDir(): string
    {
        return ftp_pwd($this->connection);
    }

    public function changeDir(string $moveto): array
    {
        if (ftp_chdir($this->connection, $moveto)) {
            return array('response' => 'success', 'action' => __FUNCTION__, 'value' => $moveto);
        }
        return array('response' => 'fail', 'action' => __FUNCTION__, 'value' => $moveto);
    }

    public function moveUpOne(): array
    {
        if (ftp_cdup($this->connection)) {
            return array('response' => 'success', 'action' => __FUNCTION__);
        }
        return array('response' => 'fail', 'action' => __FUNCTION__);
    }

    public function renameFile(string $dir, string $file_name, string $new_file_name): array
    {
        $path_data = pathinfo("{$dir}$file_name");
        $file_type = $path_data['extension'];
        if (ftp_get($this->connection, "TEMPFILE.$file_type", "{$dir}$file_name", FTP_BINARY)) {
            if (ftp_put($this->connection, "{$dir}$new_file_name", "TEMPFILE.$file_type", FTP_BINARY)) {
                $this->deleteFile("{$dir}$file_name");
                return array('response' => 'success', 'action' => __FUNCTION__, 'old' => "{$dir}$file_name", 'new' => "{$dir}$new_file_name");
            }
            return array('response' => 'fail', 'action' => __FUNCTION__, 'old' => "{$dir}$file_name", 'new' => "{$dir}$new_file_name");
        }
        return array('response' => 'fail', 'action' => __FUNCTION__, 'old' => "{$dir}$file_name", 'new' => "{$dir}$new_file_name");
    }

    public function moveFile(string $dir, string $file_name, string $move_to): array
    {
        $path_data = pathinfo("{$dir}$file_name");
        $file_type = $path_data['extension'];
        if (ftp_get($this->connection, "TEMPFILE.$file_type", "{$dir}$file_name", FTP_BINARY)) {
            if (ftp_put($this->connection, "$move_to{$file_name}", "TEMPFILE.$file_type", FTP_BINARY)) {
                $this->deleteFile("{$dir}$file_name");
                return array('response' => 'success', 'action' => __FUNCTION__, 'file' => "{$dir}$file_name", 'move_to' => $move_to);
            }
            return array('response' => 'fail', 'action' => __FUNCTION__, 'file' => "{$dir}$file_name", 'move_to' => $move_to);
        }
        return array('response' => 'fail', 'action' => __FUNCTION__, 'file' => "{$dir}$file_name", 'move_to' => $move_to);
    }

    public function downloadFile(string $save_as, string $get_file, int $mode = FTP_BINARY): array
    {
        if (ftp_get($this->connection, $save_as, $get_file, $mode)) {
            return array('response' => 'success', 'action' => __FUNCTION__, 'file' => $get_file, 'save_as' => $save_as);
        }
        return array('response' => 'fail', 'action' => __FUNCTION__, 'file' => $get_file, 'save_as' => $save_as);
    }

    public function downloadFileWithProgress(string $save_as, string $get_file, string $progress_file = 'DOWNLOAD_PERCENT.txt'): void
    {
        $ftp_url = "ftp://$this->storage_name:$this->access_key@" . self::HOSTNAME . "/$this->storage_name/$get_file";
        $size = filesize($ftp_url);
        $in = fopen($ftp_url, "rb") or die("Cannot open source file");
        $out = fopen($save_as, "wb");
        while (!feof($in)) {
            $buf = fread($in, 10240);
            fwrite($out, $buf);
            $file_data = (int)(ftell($out) / $size * 100);
            file_put_contents($progress_file, $file_data);
        }
        fclose($out);
        fclose($in);
    }

    public function downloadAll(string $dir_dl_from = '', string $dl_into = '', int $mode = FTP_BINARY): array
    {
        $array = json_decode(file_get_contents(self::STORAGE_API_URL . "/$this->storage_name" . $dir_dl_from . "/?AccessKey=" . $this->access_key), true);
        $files_downloaded = 0;
        foreach ($array as $value) {
            if ($value['IsDirectory'] === false) {
                $file_name = $value['ObjectName'];
                if (ftp_get($this->connection, $dl_into . "$file_name", $file_name, $mode)) {
                    $files_downloaded++;
                }
            }
        }
        return array('action' => __FUNCTION__, 'files_downloaded' => $files_downloaded);
    }

    public function uploadFile(string $upload, string $upload_as, int $mode = FTP_BINARY): array
    {
        if (ftp_put($this->connection, $upload_as, $upload, $mode)) {
            return array('response' => 'success', 'action' => __FUNCTION__, 'file' => $upload, 'upload_as' => $upload_as);
        }
        return array('response' => 'fail', 'action' => __FUNCTION__, 'file' => $upload, 'upload_as' => $upload_as);
    }

    public function uploadFileWithProgress(string $upload, string $upload_as, string $progress_file = 'UPLOAD_PERCENT.txt'): void
    {
        $ftp_url = "ftp://$this->storage_name:$this->access_key@" . self::HOSTNAME . "/$this->storage_name/$upload_as";
        $size = filesize($upload);
        $out = fopen($ftp_url, "wb");
        $in = fopen($upload, "rb");
        while (!feof($in)) {
            $buffer = fread($in, 10240);
            fwrite($out, $buffer);
            $file_data = (int)(ftell($in) / $size * 100);
            file_put_contents($progress_file, $file_data);
        }
        fclose($in);
        fclose($out);
    }

    public function listAllOG(): array
    {
        return json_decode(file_get_contents(self::STORAGE_API_URL . "/$this->storage_name/?AccessKey=" . $this->access_key), true);
    }

    public function listFiles(string $location = ''): array
    {
        $array = json_decode(file_get_contents(self::STORAGE_API_URL . "/$this->storage_name" . $location . "/?AccessKey=" . $this->access_key), true);
        $items = array('storage_name' => $this->storage_name, 'current_dir' => $location, 'data' => array());
        foreach ($array as $value) {
            if ($value['IsDirectory'] === false) {
                $created = date('Y-m-d H:i:s', strtotime($value['DateCreated']));
                $last_changed = date('Y-m-d H:i:s', strtotime($value['LastChanged']));
                if (isset(pathinfo($value['ObjectName'])['extension'])) {
                    $file_type = pathinfo($value['ObjectName'])['extension'];
                } else {
                    $file_type = null;
                }
                $file_name = $value['ObjectName'];
                $size_kb = (float)($value['Length'] / 1024);
                $guid = $value['Guid'];
                $items['data'][] = array('name' => $file_name, 'file_type' => $file_type, 'size' => $size_kb, 'created' => $created,
                    'last_changed' => $last_changed, 'guid' => $guid);
            }
        }
        return $items;
    }

    public function listFolders(string $location = ''): array
    {
        $array = json_decode(file_get_contents(self::STORAGE_API_URL . "/$this->storage_name" . $location . "/?AccessKey=$this->access_key"), true);
        $items = array('storage_name' => $this->storage_name, 'current_dir' => $location, 'data' => array());
        foreach ($array as $value) {
            $created = date('Y-m-d H:i:s', strtotime($value['DateCreated']));
            $last_changed = date('Y-m-d H:i:s', strtotime($value['LastChanged']));
            $foldername = $value['ObjectName'];
            $guid = $value['Guid'];
            if ($value['IsDirectory'] === true) {
                $items['data'][] = array('name' => $foldername, 'created' => $created,
                    'last_changed' => $last_changed, 'guid' => $guid);
            }
        }
        return $items;
    }

    public function listAll(string $location = ''): array
    {
        $array = json_decode(file_get_contents(self::STORAGE_API_URL . "/$this->storage_name" . $location . "/?AccessKey=" . $this->access_key), true);
        $items = array('storage_name' => $this->storage_name, 'current_dir' => $location, 'data' => array());
        foreach ($array as $value) {
            $created = date('Y-m-d H:i:s', strtotime($value['DateCreated']));
            $last_changed = date('Y-m-d H:i:s', strtotime($value['LastChanged']));
            $file_name = $value['ObjectName'];
            $guid = $value['Guid'];
            if ($value['IsDirectory'] === true) {
                $file_type = null;
                $size_kb = null;
            } else {
                if (isset(pathinfo($value['ObjectName'])['extension'])) {
                    $file_type = pathinfo($value['ObjectName'])['extension'];
                } else {
                    $file_type = null;
                }
                $size_kb = (float)($value['Length'] / 1024);
            }
            $items['data'][] = array('name' => $file_name, 'file_type' => $file_type, 'size' => $size_kb, 'is_dir' => $value['IsDirectory'], 'created' => $created,
                'last_changed' => $last_changed, 'guid' => $guid);
        }
        return $items;
    }

    public function closeConnection(): array
    {
        if (ftp_close($this->connection)) {
            return array('response' => 'success', 'action' => __FUNCTION__);
        }
        return array('response' => 'fail', 'action' => __FUNCTION__);
    }
}