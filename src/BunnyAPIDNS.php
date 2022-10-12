<?php

namespace Corbpie\BunnyCdn;

class BunnyAPIDNS extends BunnyAPI
{
    public function getDNSZones(int $page = 1, int $per_page = 1000): array
    {
        return $this->APIcall('GET', "dnszone?page=$page&perPage=$per_page");
    }

    public function getDNSZone(int $zone_id): array
    {
        return $this->APIcall('GET', "dnszone/$zone_id");
    }

    public function getDNSZoneStatistics(int $zone_id, $date_from = null, $date_to = null): array
    {
        $url = "dnszone/$zone_id/statistics";
        if (!is_null($date_from) && is_null($date_to)) {
            $url .= "?dateFrom=$date_from";
        } else if (!is_null($date_to) && is_null($date_from)) {
            $url .= "?dateTo=$date_to";
        } elseif (!is_null($date_to) && !is_null($date_from)) {
            $url .= "?dateFrom=$date_from&dateTo=$date_to";
        }
        return $this->APIcall('GET', $url);
    }

    public function addDNSZoneFull(array $parameters): array
    {//Add DNS zone by building up parameters from https://docs.bunny.net/reference/dnszonepublic_add
        return $this->APIcall('POST', "dnszone", $parameters);
    }

    public function addDNSZone(string $domain, bool $logging = false, bool $log_ip_anon = true): array
    {
        $parameters = array(
            "Domain" => $domain, "LoggingEnabled" => $logging, "LoggingIPAnonymizationEnabled" => $log_ip_anon
        );
        return $this->APIcall('POST', "dnszone", $parameters);
    }

    public function updateDNSZoneNameservers(int $zone_id, bool $custom_ns, string $ns_one = '', string $ns_two = ''): array
    {
        $parameters = array(
            "CustomNameserversEnabled" => $custom_ns, "Nameserver1" => $ns_one, "Nameserver2" => $ns_two
        );
        return $this->APIcall('POST', "dnszone/$zone_id", $parameters);
    }

    public function updateDNSZoneLogging(int $zone_id, bool $enable_logging, int $log_anon_type, bool $use_log_anon): array
    {
        $parameters = array(
            "LoggingEnabled" => $enable_logging, "LogAnonymizationType" => $log_anon_type, "LoggingIPAnonymizationEnabled" => $use_log_anon
        );
        return $this->APIcall('POST', "dnszone/$zone_id", $parameters);
    }

    public function updateDNSZoneSoaEmail(int $zone_id, string $soa_email): array
    {
        return $this->APIcall('POST', "dnszone/$zone_id", array("SoaEmail" => $soa_email));
    }

    public function deleteDNSZone(int $zone_id): array
    {
        return $this->APIcall('DELETE', "dnszone/$zone_id");
    }

    public function addDNSRecord(int $zone_id, string $name, string $value, array $parameters = array()): array
    {//Add DNS record by building up parameters from https://docs.bunny.net/reference/dnszonepublic_addrecord
        $parameters = array_merge(
            array(
                'Name' => $name,
                'Value' => $value,
            ),
            $parameters
        );
        return $this->APIcall('PUT', "dnszone/$zone_id/records", $parameters);
    }

    public function addDNSRecordA(int $zone_id, string $hostname, string $ipv4, int $ttl = 300, int $weight = 100): array
    {
        return $this->APIcall('PUT', "dnszone/$zone_id/records", array("Type" => 0, "Value" => $ipv4, "Name" => $hostname, "Ttl" => $ttl, "Weight" => $weight));
    }

    public function addDNSRecordAAAA(int $zone_id, string $hostname, string $ipv6, int $ttl = 300, int $weight = 100): array
    {
        return $this->APIcall('PUT', "dnszone/$zone_id/records", array("Type" => 1, "Value" => $ipv6, "Name" => $hostname, "Ttl" => $ttl, "Weight" => $weight));
    }

    public function addDNSRecordCNAME(int $zone_id, string $hostname, string $target, int $ttl = 300, int $weight = 100): array
    {
        return $this->APIcall('PUT', "dnszone/$zone_id/records", array("Type" => 2, "Value" => $target, "Name" => $hostname, "Ttl" => $ttl, "Weight" => $weight));
    }

    public function addDNSRecordMX(int $zone_id, string $hostname, string $mail_server, int $priority = 2000, int $ttl = 300, int $weight = 100): array
    {
        return $this->APIcall('PUT', "dnszone/$zone_id/records", array("Type" => 4, "Value" => $mail_server, "Name" => $hostname, "Priority" => $priority, "Ttl" => $ttl, "Weight" => $weight));
    }

    public function addDNSRecordTXT(int $zone_id, string $hostname, string $content, int $ttl = 300, int $weight = 100): array
    {
        return $this->APIcall('PUT', "dnszone/$zone_id/records", array("Type" => 3, "Value" => $content, "Name" => $hostname, "Ttl" => $ttl, "Weight" => $weight));
    }

    public function addDNSRecordNS(int $zone_id, string $hostname, string $target, int $ttl = 300, int $weight = 100): array
    {
        return $this->APIcall('PUT', "dnszone/$zone_id/records", array("Type" => 12, "Value" => $target, "Name" => $hostname, "Ttl" => $ttl, "Weight" => $weight));
    }

    public function addDNSRecordRedirect(int $zone_id, string $hostname, string $url, int $ttl = 300, int $weight = 100): array
    {
        return $this->APIcall('PUT', "dnszone/$zone_id/records", array("Type" => 5, "Value" => $url, "Name" => $hostname, "Ttl" => $ttl, "Weight" => $weight));
    }

    public function addDNSRecordPullZone(int $zone_id, string $hostname, int $pullzone_id, int $ttl = 300, int $weight = 100): array
    {
        return $this->APIcall('PUT', "dnszone/$zone_id/records", array("Type" => 7, "PullZoneId" => $pullzone_id, "Name" => $hostname, "Ttl" => $ttl, "Weight" => $weight));
    }

    public function addDNSRecordScript(int $zone_id, string $hostname, int $script_id, int $ttl = 300, int $weight = 100): array
    {
        return $this->APIcall('PUT', "dnszone/$zone_id/records", array("Type" => 11, "ScriptId" => $script_id, "Name" => $hostname, "Ttl" => $ttl, "Weight" => $weight));
    }

    public function updateDNSRecordA(int $zone_id, int $dns_id, string $hostname, string $ipv4): array
    {
        return $this->APIcall('POST', "dnszone/$zone_id/records/$dns_id", array("Type" => 0, "Value" => $ipv4, "Name" => $hostname));
    }

    public function updateDNSRecordAAAA(int $zone_id, int $dns_id, string $hostname, string $ipv6): array
    {
        return $this->APIcall('POST', "dnszone/$zone_id/records/$dns_id", array("Type" => 1, "Value" => $ipv6, "Name" => $hostname));
    }

    public function updateDNSRecordCNAME(int $zone_id, int $dns_id, string $hostname, string $target): array
    {
        return $this->APIcall('POST', "dnszone/$zone_id/records/$dns_id", array("Type" => 2, "Value" => $target, "Name" => $hostname));
    }

    public function updateDNSRecordMX(int $zone_id, int $dns_id, string $hostname, string $mail_server, int $priority = 2000): array
    {
        return $this->APIcall('POST', "dnszone/$zone_id/records/$dns_id", array("Type" => 4, "Value" => $mail_server, "Priority" => $priority, "Name" => $hostname));
    }

    public function updateDNSRecordTXT(int $zone_id, int $dns_id, string $hostname, string $content): array
    {
        return $this->APIcall('POST', "dnszone/$zone_id/records/$dns_id", array("Type" => 3, "Value" => $content, "Name" => $hostname));
    }

    public function updateDNSRecordNS(int $zone_id, int $dns_id, string $hostname, string $target): array
    {
        return $this->APIcall('POST', "dnszone/$zone_id/records/$dns_id", array("Type" => 12, "Value" => $target, "Name" => $hostname));
    }

    public function disableDNSRecord(int $zone_id, int $dns_id): array
    {
        return $this->APIcall('POST', "dnszone/$zone_id/records/$dns_id", array("Disabled" => true));
    }

    public function enableDNSRecord(int $zone_id, int $dns_id): array
    {
        return $this->APIcall('POST', "dnszone/$zone_id/records/$dns_id", array("Disabled" => false));
    }

    public function deleteDNSRecord(int $zone_id, int $dns_id): array
    {
        return $this->APIcall('DELETE', "dnszone/$zone_id/records/$dns_id");
    }

    public function recheckDNSRecord(int $zone_id): array
    {
        return $this->APIcall('POST', "dnszone/$zone_id/recheckdns");
    }

    public function dismissDNSConfigNotice(int $zone_id): array
    {
        return $this->APIcall('POST', "dnszone/$zone_id/dismissnameservercheck");
    }
}