#!/usr/bin/php -q
<?php
require("LocationVerification.php");

//first we create a new CreditCardFraudDetection object
$locv = new LocationVerification;

//Enter your license key here
// $h["license_key"] = "YOUR_LICENSE_KEY_HERE";

// Set inputs and store them in a hash

// Required fields
$h["i"] = "24.24.24.24";             // set the client ip address
$h["city"] = "New York";             // set the billing city
$h["region"] = "NY";                 // set the billing state
$h["postal"] = "10011";              // set the billing zip code
$h["country"] = "US";                // set the billing country

// If you want to disable Secure HTTPS or don't have Curl and OpenSSL installed
// uncomment the next line
// $locv->isSecure = 0;

// how many seconds to cache the ip addresses
$locv->wsIpaddrRefreshTimeout = 3600*5;
// file to store the ip address
$locv->wsIpaddrCacheFile = "/tmp/maxmind.ws.cache";
// if useDNS is 1 then use DNS, otherwise use ip addresses directly
// $locv->useDNS = 1;

// set the time out to be five seconds
$locv->timeout = 5;

// uncomment to turn on debugging
$locv->debug = 1;

// next we set up the input hash
$locv->input($h);

// then we query the server
$locv->query();

// then we get the result from the server
$h = $locv->output();

// then finally we print out the result
$outputkeys = array_keys($h);
$numoutputkeys = count($h);
for ($i = 0; $i < $numoutputkeys; $i++) {
  $key = $outputkeys[$i];
  $value = $h[$key];
  print $key . " = " . $value . "\n";
}
?>
