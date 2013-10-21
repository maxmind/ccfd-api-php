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

class HTTPBase
{
    public $server;
    public $numservers;
    public $url;
    public $queries;
    public $allowed_fields;
    public $num_allowed_fields;
    public $outputstr;
    public $isSecure;
    public $timeout;
    public $debug;
    public $check_field;

    public function HTTPBase()
    {
        $this->isSecure = 0;
        $this->debug = 0;
        $this->timeout = 0;
        // use countryMatch to validate the results. It is avail in all minfraud answeres
        $this->check_field = "countryMatch";
    }

    // this function sets the checked field
    public function set_check_field($f)
    {
        $this->check_field = $f;
    }

    // this function sets the allowed fields
    public function set_allowed_fields($i)
    {
        $this->allowed_fields = $i;
        $this->num_allowed_fields = count($i);
    }

    //this function queries the servers
    public function query()
    {
        // query every server using its domain name
        for ($i = 0; $i < $this->numservers; $i++) {
            $result = $this->querySingleServer($this->server[$i]);
            if ($this->debug == 1) {
                print "server: " . $this->server[$i] . "\nresult: " . $result . "\n";
            }
            if ($result) {
                return $result;
            }
        }
        return 0;
    }

    // this function takes a input hash and stores it in the hash named queries
    public function input($vars)
    {
        $numinputkeys = count($vars); // get the number of keys in the input hash
        $inputkeys = array_keys($vars); // get a array of keys in the input hash
        for ($i = 0; $i < $numinputkeys; $i++) {
            $key = $inputkeys[$i];
            if ($this->allowed_fields[$key] == 1) {
                //if key is a allowed field then store it in
                //the hash named queries
                $this->queries[$key] = urlencode($this->filter_field($key, $vars[$key]));
            } else {
                print "invalid input $key - perhaps misspelled field?";
                return 0;
            }
        }
        $this->queries["clientAPI"] = $this->API_VERSION;
    }

    //sub-class should override this if it needs to filter inputs
    public function filter_field($key, $value)
    {
        return $value;
    }

    //this function returns the output from the server
    public function output()
    {
        return $this->outputstr;
    }

    // this function queries a single server
    public function querySingleServer($server)
    {
        // check if we using the Secure HTTPS proctol
        if ($this->isSecure == 1) {
            $scheme = "https://"; // Secure HTTPS proctol
        } else {
            $scheme = "http://"; // Regular HTTP proctol
        }

        // build a query string from the hash called queries
        $numquerieskeys = count($this->queries); // get the number of keys in the hash called queries
        $querieskeys = array_keys($this->queries); // get a array of keys in the hash called queries
        if ($this->debug == 1) {
            print "number of query keys " . $numquerieskeys . "\n";
        }

        $query_string = "";

        for ($i = 0; $i < $numquerieskeys; $i++) {
            //for each element in the hash called queries
            //append the key and value of the element to the query string
            $key = $querieskeys[$i];
            $value = $this->queries[$key];
            //encode the key and value before adding it to the string
            //$key = urlencode($key);
            //$value = urlencode($value);
            if ($this->debug == 1) {
                print " query key " . $key . " query value " . $value . "\n";
            }
            $query_string = $query_string . $key . "=" . $value;
            if ($i < $numquerieskeys - 1) {
                $query_string = $query_string . "&";
            }
        }

        $content = "";

        //check if the curl module exists
        if (extension_loaded('curl')) {
            //use curl
            if ($this->debug == 1) {
                print "using curl\n";
            }

            //open curl
            $ch = curl_init();

            $url = $scheme . $server . "/" . $this->url;

            //set curl options
            if ($this->debug == 1) {
                print "url " . $url . "\n";
            }
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

            //this option lets you store the result in a string
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $query_string);

            //get the content
            $content = curl_exec($ch);

            curl_close($ch);
        } else {
            //curl does not exist
            //use the fsockopen function,
            //the fgets function and the fclose function
            if ($this->debug == 1) {
                print "using fsockopen for querySingleServer\n";
            }

            $url = $scheme . $server . "/" . $this->url . "?" . $query_string;
            if ($this->debug == 1) {
                print "url " . $url . " " . "\n";
            }

            //now check if we are using regular HTTP
            if ($this->isSecure == 0) {
                //we using regular HTTP

                //parse the url to get
                //host, path and query
                $url3 = parse_url($url);
                $host = $url3["host"];
                $path = $url3["path"];
                $query = $url3["query"];

                //open the connection
                $fp = fsockopen($host, 80, $errno, $errstr, $this->timeout);
                if ($fp) {
                    //send the request
                    $post = "POST $path HTTP/1.0\nHost: " . $host . "\nContent-type: application/x-www-form-urlencoded\nUser-Agent: Mozilla 4.0\nContent-length: " . strlen(
                            $query
                        ) . "\nConnection: close\n\n$query";
                    fputs($fp, $post);
                    while (!feof($fp)) {
                        $buf .= fgets($fp, 128);
                    }
                    $lines = explode("\n", $buf);
                    // get the content
                    $content = $lines[count($lines) - 1];
                    //close the connection
                    fclose($fp);
                } else {
                    return 0;
                }
            } else {
                //secure HTTPS requires CURL
                print "error: you need to install curl if you want secure HTTPS or specify the variable to be $ccfs->isSecure = 0";
                return 0;
            }
        }

        if ($this->debug == 1) {
            print "content = " . $content . "\n";
        }
        // get the keys and values from
        // the string content and store them
        // the hash named outputstr

        // split content into pairs containing both
        // the key and the value
        $keyvaluepairs = explode(";", $content);

        //get the number of key and value pairs
        $numkeyvaluepairs = count($keyvaluepairs);

        //for each pair store key and value into the
        //hash named outputstr
        $this->outputstr = array();
        for ($i = 0; $i < $numkeyvaluepairs; $i++) {
            //split the pair into a key and a value
            list($key, $value) = explode("=", $keyvaluepairs[$i]);
            if ($this->debug == 1) {
                print " output " . $key . " = " . $value . "\n";
            }
            //store the key and the value into the
            //hash named outputstr
            $this->outputstr[$key] = $value;
        }
        //one other way to do it
        if (!array_key_exists($this->check_field, $this->outputstr)) {
            return 0;
        }
        return 1;
    }
}
