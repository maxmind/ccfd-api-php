# Maxmind minFraud PHP API #

## Install via Composer ##

We recommend installing this package with [Composer](http://getcomposer.org/).

### Download Composer ###

To download Composer, run in the root directory of your project:

```bash
curl -sS https://getcomposer.org/installer | php
```

You should now have the file `composer.phar` in your project directory.

### Install Dependencies ###

Run in your project root:

```
php composer.phar require minfraud/http:~1.70
```

You should now have the files `composer.json` and `composer.lock` as well as
the directory `vendor` in your project directory. If you use a version control
system, `composer.json` should be added to it.

### Require Autoloader ###

After installing the dependencies, you need to require the Composer autoloader
from your code:

```php
require 'vendor/autoload.php';
```

## Install without Composer ##

Place the files in the `src` directory in the `include_path` as specified in
your `php.ini` file or place them in the same directory as your PHP scripts.

## Example Scripts ##

See `examples/minfraud.php` for complete example how to use this API with the
minFraud service.

These scripts can be run from the shell.

## Usage ##

```php
<?php
require_once 'vendor/autoload.php';

$inputs = array(
    "license_key" => "YOUR_LICENSE_KEY_HERE",
    "i"           => "24.24.24.24",
    "city"        => "New York",
    "region"      => "NY",
    "postal"      => "11434",
    "country"     => "US",
    // Other inputs from http://dev.maxmind.com/minfraud/
);

$ccfs = new CreditCardFraudDetection;
$ccfs->input($inputs);
$ccfs->query();
$outputs = $ccfs->output();

print_r($outputs);
```

### $ccfs->isSecure ###

If isSecure is set to 0 then it uses regular HTTP. If isSecure is set to 1
then it uses Secure HTTPS (requires Curl PHP binding).

## $ccfs->useUtf8($bool)

If set to true, the output values will be converted from ISO 8859-1 to UTF-8.
Defaults to false.

## $ccfs->setCurlCaInfo($cert)

Sets the path to the SSL certificate to be used by cURL. If this is not set,
the default certificate is used. If no certificates are available and isSecure
has not been disabled, the query will fail.

### $ccfs->input($array) ###

Takes an array of key/value pairs to use as input for the server. See
http://dev.maxmind.com/minfraud/ for details on input fields.

### $ccfs->query() ###

Queries the server with the fields passed to the input method
and stores the output.

### $ccfs->output();

Returns the output from the server. See http://dev.maxmind.com/minfraud/
for details on output fields.

## Secure HTTPS ##

Secure HTTPS is on by default. In order to use it, you need to have
the Curl PHP binding, the curl library and the OpenSSL library.

You can download the curl libary at:
http://curl.haxx.se/download.html
http://curl.haxx.se/docs/install.html for installation details

It is recommended that you install these packages in this order:

1. OpenSSL library
2. Curl library
3. Curl PHP binding

===============================
Copyright (c) 2014, MaxMind, Inc

All rights reserved.  This package is licensed under the LGPL.  For details
see the COPYING file.
