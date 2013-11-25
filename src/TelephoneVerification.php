<?php
require_once 'HTTPBase.php';

class TelephoneVerification extends HTTPBase
{
    public $server;
    public $numservers;
    const API_VERSION = 'PHP/1.54';


    public function __construct()
    {
        $this->isSecure = 1; // use HTTPS by default

        //set the allowed_fields hash
        $this->allowed_fields["l"] = 1;
        $this->allowed_fields["phone"] = 1;
        $this->allowed_fields["verify_code"] = 1;
        $this->num_allowed_fields = count($this->allowed_fields);

        //set the url of the web service
        $this->url = "app/telephone_http";
        $this->check_field = "refid";
        $this->server = array("www.maxmind.com", "www2.maxmind.com");
        $this->numservers = count($this->server);
        $this->timeout = 30;
    }
}
