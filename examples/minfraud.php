#!/usr/bin/php -q
<?php
require("../src/CreditCardFraudDetection.php");

// Create a new CreditCardFraudDetection object
$ccfs = new CreditCardFraudDetection();

// Set inputs and store them in a hash
// See http://www.maxmind.com/app/ccv for more details on the input fields

// Enter your license key here (Required)
$input["license_key"] = "YOUR_LICENSE_KEY_HERE";

// Required fields
$input['i']       = '24.24.24.24';        // set the client ip address
$input['city']    = 'New York';           // set the billing city
$input['region']  = 'NY';                 // set the billing state
$input['postal']  = '11434';              // set the billing zip code
$input['country'] = 'US';                 // set the billing country

// Recommended fields
$input['domain']      = 'yahoo.com';      // Email domain
$input['bin']         = '549099';         // bank identification number
$input['forwardedIP'] = '24.24.24.25';    // X-Forwarded-For or Client-IP HTTP Header

/**
 * CreditCardFraudDetection.php will take MD5 hash of e-mail address passed
 * to emailMD5 if it detects '@' in the string.
 */
$input['emailMD5'] = 'Adeeb@Hackstyle.com';

/**
 * CreditCardFraudDetection.php will take the MD5 hash of the username/password
 * if the length of the string is not 32.
 */
$input['usernameMD5'] = 'test_carder_username';
$input['passwordMD5'] = 'test_carder_password';

// Optional fields
$input['binName']         = 'MBNA America Bank';      // bank name
$input['binPhone']        = '800-421-2110';           // bank customer service phone number on back of credit card
$input['custPhone']       = '212-242';                // Area-code and local prefix of customer phone number
$input['requested_type']  = 'premium';                // minFraud service type to use ('free', 'standard', 'premium')
$input['shipAddr']        = '145-50 157TH STREET';    // Shipping Address
$input['shipCity']        = 'Jamaica';                // the City to Ship to
$input['shipRegion']      = 'NY';                     // the Region to Ship to
$input['shipPostal']      = '11434';                  // the Postal Code to Ship to
$input['shipCountry']     = 'US';                     // the country to Ship to11
$input['txnID']           = '1234';                   // Transaction ID
$input['sessionID']       = 'abcd9876';               // Session ID
$input['accept_language'] = 'de-de';
$input['user_agent']      = 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10_5_5; de-de) AppleWebKit/525.18 (KHTML, like Gecko) Version/3.1.2 Safari/525.20.1';

/**
 * If you want to enable Secure HTTPS, have Curl and OpenSSL
 * installed, change the next line to true.
 */
// $ccfs->isSecure = false;

// Set the timeout to be five seconds.
$ccfs->timeout = 10;

// Convert the output to UTF-8 (it is ISO 8859-1 by default)
$ccfs->useUtf8(true);

// Uncomment to turn on debugging.
// $ccfs->debug = true;

// Add the input array to the object.
$ccfs->input($input);

// Query the server.
$ccfs->query();

// Get the result from the server.
$output = $ccfs->output();

// Finally we print out the result.
print_r($output);
