<?php
require __DIR__ . '/vendor/autoload.php';

use Corbpie\BunnyCdn\BunnyAPIDNS;

$bunny = new BunnyAPIDNS();

//Returns all DNS zones
$bunny->getDNSZones();

//Returns all single DNS zone details
$bunny->getDNSZone(1234);

//Create a DNS zone with logging enable
$bunny->addDNSZone('zonedomain.com',  true);

//Create a DNS zone with parameters from https://docs.bunny.net/reference/dnszonepublic_add
$parameters = array(
    'Domain' => 'zonedomain.com', 'NameserversDetected' => true, 'CustomNameserversEnabled' => true,
    'Nameserver1' => 'customns1.com', 'Nameserver2' => 'customns2.com', 'SoaEmail' => 'contact@zonedomain.com',
    'DateModified' => '2022-08-18 23:59:59', 'DateCreated' => '2022-08-18 23:59:59', 'NameserversNextCheck' => '2022-08-28 23:59:59',
    'LoggingEnabled' => true, 'LoggingIPAnonymizationEnabled' => true
);
$bunny->addDNSZoneFull($parameters);

//Delete DNS zone (1234 is the DNS zone id)
$bunny->deleteDNSZone(1234);

//Returns DNS zone statistics
$bunny->getDNSZoneStatistics(1234);

//Update DNS nameservers
$bunny->updateDNSZoneNameservers(12345,  true, 'nameserverone.com', 'nameservertwo.com');

//Update DNS SOA email
$bunny->updateDNSZoneSoaEmail(12345,   'mass_contact@mymail.com');

//Add a DNS record by using parameters of choice https://docs.bunny.net/reference/dnszonepublic_addrecord
$parameters = array('Type' => 0, 'Ttl' => 120, 'Accelerated' => true, 'Weight' => 200);
$bunny->addDNSRecord(12345,   'thehost.com', '114.12.219.52', $parameters);

//Add DNS A record
$bunny->addDNSRecordA(12345,   'thehost.com', '199.99.99.99');

//Add DNS AAAA record
$bunny->addDNSRecordAAAA(12345,   'thehost.com', '2001:0db8:85a3:0000:0000:8a2e:0370:7334');

//Add DNS CNAME record
$bunny->addDNSRecordCNAME(12345,   'thehost.com', 'sometarget.com');

//Add DNS MX record (priority of 600)
$bunny->addDNSRecordMX(12345,   'thehost.com', 'mailserver.com', 600);

//Add DNS TXT record
$bunny->addDNSRecordTXT(12345,   'thehost.com', 'the TXT content');

//Add DNS NS record
$bunny->addDNSRecordNS(12345,   'thehost.com', 'targetns.com');

//Add DNS redirect record
$bunny->addDNSRecordRedirect(12345,   'thehost.com', 'theurl.com');

//Update DNS A record (9876 is the DNS record id)
$bunny->updateDNSRecordA(12345,   9876,'diffdomain.com', '162.55.44.12');

//Update DNS AAAA record (9876 is the DNS record id)
$bunny->updateDNSRecordAAAA(12345,   9876,'thehost.com', '12001:0db8:85a3:0000:0000:8a2e:0370:6225');

//Disable a DNS record
$bunny->disableDNSRecord(12345,   9876);

//Enable a DNS record
$bunny->enableDNSRecord(12345,   9876);

//Delete DNS record
$bunny->deleteDNSRecord(12345,   9876);