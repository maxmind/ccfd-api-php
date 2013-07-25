<?php

namespace Maxmind\Minfraud;

/* CreditCardFraudDetection.php
 *
 * Copyright (C) 2008 MaxMind, Inc.
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

class CreditCardFraudDetection extends HTTPBase {
  public $server;
  public $numservers;
  public $API_VERSION;

  function __construct() {
	parent::__construct();

    $this->isSecure = 1;    // use HTTPS by default

    //set the allowed_fields hash
    $this->allowed_fields["i"] = 1;
    $this->allowed_fields["domain"] = 1;
    $this->allowed_fields["city"] = 1;
    $this->allowed_fields["region"] = 1;
    $this->allowed_fields["postal"] = 1;
    $this->allowed_fields["country"] = 1;
    $this->allowed_fields["bin"] = 1;
    $this->allowed_fields["binName"] = 1;
    $this->allowed_fields["binPhone"] = 1;
    $this->allowed_fields["custPhone"] = 1;
    $this->allowed_fields["license_key"] = 1;
    $this->allowed_fields["requested_type"] = 1;
    $this->allowed_fields["forwardedIP"] = 1;
    $this->allowed_fields["emailMD5"] = 1;
    $this->allowed_fields["shipAddr"] = 1;
    $this->allowed_fields["shipCity"] = 1;
    $this->allowed_fields["shipRegion"] = 1;
    $this->allowed_fields["shipPostal"] = 1;
    $this->allowed_fields["shipCountry"] = 1;
    $this->allowed_fields["txnID"] = 1;
    $this->allowed_fields["sessionID"] = 1;
    $this->allowed_fields["usernameMD5"] = 1;
    $this->allowed_fields["passwordMD5"] = 1;
    $this->allowed_fields["user_agent"] = 1;
    $this->allowed_fields["accept_language"] = 1;
    $this->allowed_fields["avs_result"] = 1;
    $this->allowed_fields["cvv_result"] = 1;
    $this->allowed_fields["order_amount"] = 1;
    $this->allowed_fields["order_currency"] = 1;
    $this->allowed_fields["shopID"] = 1;
    $this->allowed_fields["txn_type"] = 1;

    $this->num_allowed_fields = count($this->allowed_fields);

    //set the url of the web service
    $this->url = "app/ccv2r";
    $this->check_field = "countryMatch";
    $this->server = array( "minfraud.maxmind.com", "minfraud-us-east.maxmind.com", "minfraud-us-west.maxmind.com");
    $this->numservers = count($this->server);
    $this->API_VERSION = 'PHP/1.52';
  }

  function filter_field($key, $value) {
    if ($key == 'emailMD5'){
      if (strpos($value, '@') !== false){
	return md5(strtolower($value));
      }
    } else if ($key == 'usernameMD5' || $key == 'passwordMD5') {
      if (strlen($value) != 32) {
	return md5(strtolower($value));
      }
    }
    return $value;
  }
}
?>
