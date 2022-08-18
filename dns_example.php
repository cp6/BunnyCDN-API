<?php
require __DIR__ . '/vendor/autoload.php';

use Corbpie\BunnyCdn\BunnyAPI;

$bunny = new BunnyAPI();

//Returns all DNS zones
$bunny->getDNSZones();

//Returns all single DNS zone details
$bunny->getDNSZone(1234);

//Create a DNS zone with logging enable
$bunny->addDNSZone('zonedomain.net',  true);

//Delete DNS zone (1234 is the DNS zone id)
$bunny->deleteDNSZone(1234);

//Returns DNS zone statistics
$bunny->getDNSZoneStatistics(1234);

//Add DNS zone (with logging and anonymous logging)
$bunny->addDNSZone('mydomainname.com',  true, true);

//Update DNS nameservers
$bunny->updateDNSZoneNameservers(12345,  true, 'nameserverone.com', 'nameservertwo.com');

//Update DNS SOA email
$bunny->updateDNSZoneSoaEmail(12345,   'mass_contact@mymail.com');

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
$bunny->updateDNSRecordA(12345,   9876,'thehost.com', '12001:0db8:85a3:0000:0000:8a2e:0370:6225');

//Disable a DNS record
$bunny->disableDNSRecord(12345,   9876);

//Enable a DNS record
$bunny->enableDNSRecord(12345,   9876);

//Delete DNS record
$bunny->deleteDNSRecord(12345,   9876);