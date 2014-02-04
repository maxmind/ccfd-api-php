<?php

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

require_once 'HTTPBase.php';
class CreditCardFraudDetection extends HTTPBase
{
    /**
     * Default servers to query.
     *
     * @var array
     */
    public $server = array(
        'minfraud.maxmind.com',
        'minfraud-us-east.maxmind.com',
        'minfraud-us-west.maxmind.com'
    );

    /**
     * The numbers of servers to query.
     *
     * @var int
     */
    public $numservers = 0;

    /**
     * The URL Path to use.
     *
     * @var string
     */
    public $url = 'app/ccv2r';

    /**
     * Set isSecure to true by default.
     *
     * @var bool
     */
    public $isSecure = true;

    /**
     * Set the default allowed fields.
     *
     * @var array
     */
    public $allowed_fields = array(
        'i'               => true,
        'domain'          => true,
        'city'            => true,
        'region'          => true,
        'postal'          => true,
        'country'         => true,
        'bin'             => true,
        'binName'         => true,
        'binPhone'        => true,
        'custPhone'       => true,
        'license_key'     => true,
        'requested_type'  => true,
        'forwardedIP'     => true,
        'emailMD5'        => true,
        'shipAddr'        => true,
        'shipCity'        => true,
        'shipRegion'      => true,
        'shipPostal'      => true,
        'shipCountry'     => true,
        'txnID'           => true,
        'sessionID'       => true,
        'usernameMD5'     => true,
        'passwordMD5'     => true,
        'user_agent'      => true,
        'accept_language' => true,
        'avs_result'      => true,
        'cvv_result'      => true,
        'order_amount'    => true,
        'order_currency'  => true,
        'shopID'          => true,
        'txn_type'        => true
    );

    /**
     * Constuctor.
     */
    public function __construct()
    {
        // Set the number of allowed fields.
        $this->num_allowed_fields = count($this->allowed_fields);

        // Set the number of servers to query.
        $this->numservers = count($this->server);
    }

    /**
     * If key matches one of 'emailMD5', 'usernameMD5' or 'passwordMD5',
     * convert value to lowercase and return the md5.
     *
     * If key does not match one of the above, just return the value.
     *
     * @see HTTPBase::filter_field()
     * @param string $key
     * @param string $value
     * @return string
     */
    public function filter_field($key, $value)
    {
        if ($key == 'emailMD5' && false !== strpos($value, '@')) {
            return md5(strtolower($value));
        }

        if (($key == 'usernameMD5' || $key == 'passwordMD5')
            && strlen($value) != 32
        ) {
            return md5(strtolower($value));
        }

        return $value;
    }
}
