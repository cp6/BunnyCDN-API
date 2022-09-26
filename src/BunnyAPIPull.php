<?php

namespace Corbpie\BunnyCdn;

class BunnyAPIPull extends BunnyAPI
{
    public function listPullZones(int $page = 0, int $per_page = 100, bool $include_cert = true): array
    {
        return $this->APIcall('GET', 'pullzone', ['page' => $page, 'perPage' => $per_page, 'includeCertificate' => $include_cert]);
    }

    public function getPullZone(int $id): array
    {
        return $this->APIcall('GET', "pullzone/$id");
    }

    public function createPullZone(string $name, string $origin, array $args = array()): array
    {
        $args = array_merge(
            array(
                'Name' => $name,
                'OriginUrl' => $origin,
            ),
            $args
        );
        return $this->APIcall('POST', 'pullzone', $args);
    }

    public function updatePullZone(int $id, array $args = array()): array
    {
        return $this->APIcall('POST', "pullzone/$id", $args);
    }

    public function pullZoneData(int $id): array
    {
        return $this->APIcall('GET', "pullzone/$id");
    }

    public function purgePullZone(int $id): array
    {
        return $this->APIcall('POST', "pullzone/$id/purgeCache");
    }

    public function deletePullZone(int $id): array
    {
        return $this->APIcall('DELETE', "pullzone/$id");
    }

    public function pullZoneHostnames(int $id): ?array
    {
        $data = $this->pullZoneData($id);
        if (isset($this->pullZoneData($id)['Hostnames'])) {
            $hn_count = count($data['Hostnames']);
            $hn_arr = array();
            foreach ($data['Hostnames'] as $a_hn) {
                $hn_arr[] = array(
                    'id' => $a_hn['Id'],
                    'hostname' => $a_hn['Value'],
                    'force_ssl' => $a_hn['ForceSSL']
                );
            }
            return array(
                'hostname_count' => $hn_count,
                'hostnames' => $hn_arr
            );
        }
        return array('hostname_count' => 0);
    }

    public function addHostnamePullZone(int $id, string $hostname): array
    {
        return $this->APIcall('POST', "pullzone/$id/addHostname", array("Hostname" => $hostname));
    }

    public function removeHostnamePullZone(int $id, string $hostname): array
    {
        return $this->APIcall('DELETE', "pullzone/$id/removeHostname", array("Hostname" => $hostname));
    }

    public function addFreeSSLCertificate(string $hostname): array
    {
        return $this->APIcall('GET', 'pullzone/loadFreeCertificate?hostname=' . $hostname);
    }

    public function forceSSLPullZone(int $id, string $hostname, bool $force_ssl = true): array
    {
        return $this->APIcall('POST', "pullzone/$id/setForceSSL", array("Hostname" => $hostname, 'ForceSSL' => $force_ssl));
    }

    public function listBlockedIpPullZone(int $id): array
    {
        $data = $this->pullZoneData($id);
        if (isset($data['BlockedIps'])) {
            $ip_count = count($data['BlockedIps']);
            $ip_arr = array();
            foreach ($data['BlockedIps'] as $a_hn) {
                $ip_arr[] = $a_hn;
            }
            return array(
                'blocked_ip_count' => $ip_count,
                'ips' => $ip_arr
            );
        }
        return array('blocked_ip_count' => 0, 'ips' => []);
    }

    public function resetTokenKey(int $id): array
    {
        return $this->APIcall('POST', "pullzone/$id/resetSecurityKey", array());
    }

    public function addBlockedIpPullZone(int $id, string $ip): array
    {
        return $this->APIcall('POST', "pullzone/$id/addBlockedIp", array("BlockedIp" => $ip));
    }

    public function unBlockedIpPullZone(int $id, string $ip): array
    {
        return $this->APIcall('POST', "pullzone/$id/removeBlockedIp", array("BlockedIp" => $ip));
    }

    public function addAllowedReferrer(int $id, string $hostname): array
    {
        return $this->APIcall('POST', "pullzone/$id/addAllowedReferrer", array("Hostname" => $hostname));
    }

    public function removeAllowedReferrer(int $id, string $hostname): array
    {
        return $this->APIcall('POST', "pullzone/$id/removeAllowedReferrer", array("Hostname" => $hostname));
    }

    public function addBlockedReferrer(int $id, string $hostname): array
    {
        return $this->APIcall('POST', "pullzone/$id/addBlockedReferrer", array("Hostname" => $hostname));
    }

    public function removeBlockedReferrer(int $id, string $hostname): array
    {
        return $this->APIcall('POST', "pullzone/$id/removeBlockedReferrer", array("Hostname" => $hostname));
    }

    public function pullZoneLogs(int $id, string $date): array
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, "https://logging.bunnycdn.com/$date/$id.log");
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json",
            "AccessKey: {$this->api_key}"));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        $result = curl_exec($curl);
        curl_close($curl);
        $linetoline = explode("\n", $result);
        $line = array();
        foreach ($linetoline as $v1) {
            if (isset($v1) && $v1 !== '') {
                $log_format = explode('|', $v1);
                $details = array(
                    'cache_result' => $log_format[0],
                    'status' => (int)$log_format[1],
                    'datetime' => date('Y-m-d H:i:s', round($log_format[2] / 1000, 0)),
                    'bytes' => (int)$log_format[3],
                    'ip' => $log_format[5],
                    'referer' => $log_format[6],
                    'file_url' => $log_format[7],
                    'user_agent' => $log_format[9],
                    'request_id' => $log_format[10],
                    'cdn_dc' => $log_format[8],
                    'zone_id' => (int)$log_format[4],
                    'country_code' => $log_format[11]
                );
                $line[] = $details;
            }
        }
        return $line;
    }
}