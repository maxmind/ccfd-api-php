#!/usr/bin/php -q
<?php
require("../src/TelephoneVerification.php");

$tv = new TelephoneVerification;

// Set inputs and store them in a hash
// See http://www.maxmind.com/app/telephone_form for more details on the input fields

// Enter your license key here
$h["l"] = "YOUR_LICENSE_KEY_HERE";

// Enter your telephone number here
$h["phone"] = "YOUR_TELEPHONE_NUMBER_HERE";

// $h["verify_code"] = "5783";

// If you want to disable Secure HTTPS or don't have Curl and OpenSSL installed
// uncomment the next line
// $tv->isSecure = false;

//set the time out to be 30 seconds
$tv->timeout = 30;

//uncomment to turn on debugging
// $tv->debug = 1;

// next we set up the input hash to be passed to the server
$tv->input($h);

// then we query the server
$tv->query();

// then we get the result from the server
$h = $tv->output();

//then finally we print out the result
print_r($h);
