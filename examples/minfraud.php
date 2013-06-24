#!/usr/bin/php -q
<?php
require("../src/CreditCardFraudDetection.php");

// Create a new CreditCardFraudDetection object
$ccfs = new CreditCardFraudDetection;

// Set inputs and store them in a hash
// See http://www.maxmind.com/app/ccv for more details on the input fields

// Enter your license key here (Required)
$h["license_key"] = "YOUR_LICENSE_KEY_HERE";

// Required fields
$h["i"] = "24.24.24.24";             // set the client ip address
$h["city"] = "New York";             // set the billing city
$h["region"] = "NY";                 // set the billing state
$h["postal"] = "11434";              // set the billing zip code
$h["country"] = "US";                // set the billing country

// Recommended fields
$h["domain"] = "yahoo.com";		// Email domain
$h["bin"] = "549099";			// bank identification number
$h["forwardedIP"] = "24.24.24.25";	// X-Forwarded-For or Client-IP HTTP Header
// CreditCardFraudDetection.php will take
// MD5 hash of e-mail address passed to emailMD5 if it detects '@' in the string
$h["emailMD5"] = "Adeeb@Hackstyle.com";
// CreditCardFraudDetection.php will take the MD5 hash of the username/password if the length of the string is not 32
$h["usernameMD5"] = "test_carder_username";
$h["passwordMD5"] = "test_carder_password";

// Optional fields
$h["binName"] = "MBNA America Bank";	// bank name
$h["binPhone"] = "800-421-2110";	// bank customer service phone number on back of credit card
$h["custPhone"] = "212-242";		// Area-code and local prefix of customer phone number
$h["requested_type"] = "premium";	// Which level (free, city, premium) of CCFD to use
$h["shipAddr"] = "145-50 157TH STREET";	// Shipping Address
$h["shipCity"] = "Jamaica";	// the City to Ship to
$h["shipRegion"] = "NY";	// the Region to Ship to
$h["shipPostal"] = "11434";	// the Postal Code to Ship to
$h["shipCountry"] = "US";	// the country to Ship to

$h["txnID"] = "1234";			// Transaction ID
$h["sessionID"] = "abcd9876";		// Session ID

$h["accept_language"] = "de-de";
$h["user_agent"] = "Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_5_5; de-de) AppleWebKit/525.18 (KHTML, like Gecko) Version/3.1.2 Safari/525.20.1";

// If you want to disable Secure HTTPS or don't have Curl and OpenSSL installed
// uncomment the next line
// $ccfs->isSecure = 0;

// set the timeout to be five seconds
$ccfs->timeout = 10;

// uncomment to turn on debugging
// $ccfs->debug = 1;

// how many seconds to cache the ip addresses
// $ccfs->wsIpaddrRefreshTimeout = 3600*5;

// file to store the ip address for minfraud3.maxmind.com, minfraud1.maxmind.com and minfraud2.maxmind.com
// $ccfs->wsIpaddrCacheFile = "/tmp/maxmind.ws.cache";

// if useDNS is 1 then use DNS, otherwise use ip addresses directly
$ccfs->useDNS = 0;

$ccfs->isSecure = 0;

// next we set up the input hash
$ccfs->input($h);

// then we query the server
$ccfs->query();

// then we get the result from the server
$h = $ccfs->output();

// then finally we print out the result
$outputkeys = array_keys($h);
$numoutputkeys = count($h);
for ($i = 0; $i < $numoutputkeys; $i++) {
  $key = $outputkeys[$i];
  $value = $h[$key];
  print $key . " = " . $value . "\n";
}
?>
