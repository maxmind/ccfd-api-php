#!/usr/bin/php -q
<?php
require("TelephoneVerification.php");

$tv = new TelephoneVerification;

// Set inputs and store them in a hash
// See http://www.maxmind.com/app/telephone_form for more details on the input fields

// Enter your license key here
// $h["l"] = "YOUR_LICENSE_KEY_HERE";

// Enter your telephone number here
// $h["phone"] = "YOUR_TELEPHONE_NUMBER_HERE";

// $h["verify_code"] = "5783";

// If you want to disable Secure HTTPS or don't have Curl and OpenSSL installed
// uncomment the next line
// $tv->isSecure = 0;

//set the time out to be 30 seconds
$tv->timeout = 30;

//uncomment to turn on debugging
// $tv->debug = 1;

//how many seconds the cache the ip addresses
$ccfs->wsIpaddrRefreshTimeout = 3600*5;

//where to store the ip address
$ccfs->wsIpaddrCacheFile = "/tmp/maxmind.ws.cache";

// if useDNS is 1 then use DNS, otherwise use ip addresses directly
$ccfs->useDNS = 0;

// next we set up the input hash to be passed to the server
$tv->input($h);

// then we query the server
$tv->query();

// then we get the result from the server
$h = $tv->output();

//then finally we print out the result
$outputkeys = array_keys($h);
$numoutputkeys = count($h);
for ($i = 0; $i < $numoutputkeys; $i++) {
  $key = $outputkeys[$i];
  $value = $h[$key];
  print $key . " = " . $value . "\n";
}
?>
