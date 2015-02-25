<?php

/* HTTPBase.php
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

 /**
  * @property string|array $server The host to use as the server.
  * @property bool $isSecure Set to true to use secure connection.
  * @property int $timeout The timeout in seconds to use when connecting to
  *                        the server
  * @property string $API_VERSION The version of the API.
  */
abstract class HTTPBase
{
    /**
     * Constant to define the version of this
     * @var string
     */
    const API_VERSION = 'PHP/1.60';

    /**
     * @var string|array
     */
    protected $server = '';

    /**
     * @var int
     */
    protected $numservers = 0;

    /**
     * @var string
     */
    protected $url = '';

    /**
     * @var array
     */
    protected $queries = array();

    /**
     * @var array
     */
    protected $allowed_fields = array();

    /**
     * @var int
     */
    protected $num_allowed_fields;

    /**
     * @var array
     */
    protected $outputstr = array();

    /**
     * @var bool
     */
    protected $isSecure = false;

    /**
     * @var int
     */
    protected $timeout = 0;

    /**
     * @var bool
     */
    protected $debug = false;

    /**
     * Use countryMatch to validate the results.
     * It is available in all minfraud answers.
     *
     * @var string
     */
    protected $check_field = 'countryMatch';

    private $curlCaInfo;
    private $useUtf8;

    /**
     * Public getter for class properties.
     *
     * @param string $key
     * @return mixed|NULL Returns the property value,
     *                     or null if it doesn't exist.
     */
    public function __get($key)
    {
        if (property_exists($this, $key)) {
            return $this->$key;
        }
        return null;
    }

    /**
     * Public setter.
     *
     * @param string $key
     * @param mixed $val
     */
    public function __set($key, $val)
    {
        // Only set properties the exist.
        if (property_exists($this, $key)) {
            $this->$key = $val;
        }
    }

    /**
     * Sets the path to the SSL certificate to be used by cURL. If this is
     * not set, the default certificate is used.
     *
     * @param string $cert The path to the certificate to be used by cURL.
     */
    public function setCurlCaInfo($cert)
    {
        $this->curlCaInfo = $cert;
    }

    /**
     * If set to true, the outputs from the web service call will be converted
     * from ISO 8859-1 to UTF-8. Defaults to false.
     *
     * @param boolean $useUtf8
     */
    public function useUtf8($useUtf8)
    {
        $this->useUtf8 = $useUtf8;
    }

    /**
     * Sets the checked field.
     *
     * @param string $f
     */
    public function set_check_field($f)
    {
        $this->check_field = $f;
    }

    /**
     * Set the allowed fields.
     *
     * @param array $i
     */
    public function set_allowed_fields($i)
    {
        $this->allowed_fields     = $i;
        $this->num_allowed_fields = count($i);
    }

    /**
     * Query each server.
     *
     * @return false|string
     */
    public function query()
    {
        // Query every server using it's domain name.
        for ($i = 0; $i < $this->numservers; $i++) {
            $result = $this->querySingleServer($this->server[$i]);
            if ($this->debug) {
                echo "server: {$this->server[$i]}\n";
                echo "result: $result\n";
            }

            if ($result) {
                return $result;
            }
        }
        return false;
    }

    /**
     * Validates and stores the inputVars in the queries array.
     *
     * @param $inputVars
     */
    public function input($inputVars)
    {
        foreach ($inputVars as $key => $val) {
            if (empty($this->allowed_fields[$key])) {
                echo "Invalid input $key - perhaps misspelled field?\n";
                return false;
            }
            $this->queries[$key] = urlencode($this->filter_field($key, $val));
        }
        $this->queries['clientAPI'] = self::API_VERSION;
    }

    /**
     * Child classes should override this if it needs to filter inputs.
     *
     * @param string $key
     * @param string $value
     * @return string
     */
    public function filter_field($key, $value)
    {
        return $value;
    }

    /**
     * Return the output from the server.
     *
     * @return array
     */
    public function output()
    {
        return $this->outputstr;
    }

    /**
     * Queries a single server. Returns true if the query was successful,
     * otherwise false.
     *
     * @param string $server
     * @return bool
     */
    public function querySingleServer($server)
    {
        // Check if we using the Secure HTTPS proctol.
        $scheme = $this->isSecure ? 'https://' : 'http://';

        // Build a query string from the queries array.
        $numQueries = count($this->queries);
        $queryKeys  = array_keys($this->queries);
        if ($this->debug) {
            echo "Number of query keys {$numQueries}\n";
        }

        $queryString = '';

        for ($i = 0; $i < $numQueries; $i++) {
            /**
             * For each element in the array, append the key
             * and value of the element to the query string.
             */
            $key   = $queryKeys[$i];
            $value = $this->queries[$key];

            if ($this->debug) {
                echo " query key {$key} query value {$value}\n";
            }

            $queryString .= $key . '=' . $value;
            if ($i < $numQueries - 1) {
                $queryString .= '&';
            }
        }

        $url     = $scheme . $server . "/" . $this->url;

        // Check if the curl module exists.
        if (extension_loaded('curl')) {
            // Use curl.
            if ($this->debug) {
                echo "Using curl\n";
            }

            // Open curl.
            $ch = curl_init();

            // Set curl options
            if ($this->debug) {
                echo "url {$url}\n";
            }
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

            if ($this->curlCaInfo) {
                curl_setopt($ch, CURLOPT_CAINFO, $this->curlCaInfo);
            }

            // This option lets you store the result in a string.
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $queryString);

            // Get the content.
            $content = curl_exec($ch);

            curl_close($ch);
        } else {
            /**
             * The curl extension is not loaded.
             * Use the fsockopen, fgets, and fclose functions.
             */
            if ($this->debug) {
                echo "Using fsockopen for querySingleServer\n";
            }

            $url .= "?{$queryString}";
            if ($this->debug) {
                echo "url {$url}\n";
            }

            // Check if we are using regular HTTP.
            if ($this->isSecure == false) {
                //parse the url to get host, path and query.
                $url3  = parse_url($url);
                $host  = $url3["host"];
                $path  = $url3["path"];
                $query = $url3["query"];

                // Open the connection.
                $fp = fsockopen($host, 80, $errno, $errstr, $this->timeout);

                // There was a problem opening the connection.
                if (!$fp) {
                    return false;
                }

                // Send the request.
                $post = "POST $path HTTP/1.0\n"
                      . "Host: {$host}\n"
                      . "Content-type: application/x-www-form-urlencoded\n"
                      . "User-Agent: Mozilla 4.0\n"
                      . "Content-length: "
                      .     strlen($query)
                      . "\nConnection: close\n\n"
                      . $query;

                fputs($fp, $post);
                $buf = '';
                while (!feof($fp)) {
                    $buf .= fgets($fp, 128);
                }
                $lines = explode("\n", $buf);

                // Get the content.
                $content = $lines[count($lines) - 1];

                // Close the connection.
                fclose($fp);
            } else {
                // Secure HTTPS requires CURL
                echo 'Error: you need to install curl if you want secure HTTPS '
                   . 'or specify the variable to be $ccfs->isSecure = false';
                return false;
            }
        }

        if ($this->debug) {
            echo "content = {$content}\n";
        }

        if (empty($content)) {
            echo "Returned content is empty!\n";
            return false;
        }

        if ($this->useUtf8) {
            $content = utf8_encode($content);
        }

        /**
         * Get the keys and values from the string content
         * and store them in the outputstr array.
         */

        // Split content into pairs containing both the key and the value.
        $keyValuePairs = explode(';', $content);

        // Get the number of key and value pairs.
        $numKeyValuePairs = count($keyValuePairs);

        // For each pair store key and value into the outputstr array.
        $this->outputstr = array();
        for ($i = 0; $i < $numKeyValuePairs; $i++) {
            // Split the pair into a key and a value.
            list($key, $value) = explode('=', $keyValuePairs[$i]);
            if ($this->debug) {
                echo " output {$key} = {$value}\n";
            }

            $this->outputstr[$key] = $value;
        }

        // One other way to do it.
        if (!array_key_exists($this->check_field, $this->outputstr)) {
            return false;
        }

        return true;
    }
}
