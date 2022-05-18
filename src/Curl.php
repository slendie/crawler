<?php
namespace Slendie\Crawler;

class Curl
{
    protected $options = [
        CURLOPT_RETURNTRANSFER      => true,        // Setting cURL's option to return the webpage data
        CURLOPT_FOLLOWLOCATION      => true,        // Setting cURL to follow 'location' HTTP headers
        CURLOPT_AUTOREFERER         => true,        // Automatically set the referer where following 'location' HTTP headers
        CURLOPT_HEADER              => true,        // 
        CURLOPT_CONNECTTIMEOUT      => 1200,        // Setting the amount of time (in seconds) before the request times out
        CURLOPT_TIMEOUT             => 1200,        // Setting the maximum amount of time for cURL to execute queries
        CURLOPT_MAXREDIRS           => 10,          // Setting the maximum number of redirections to follow
        CURLOPT_USERAGENT           => "Googlebot/2.1 (+http://www.googlebot.com/bot.html)",    // Setting the user agent
        CURLOPT_URL                 => '',          // Setting cURL's URL option
        CURLOPT_ENCODING            => 'gzip,deflate',  // 
    ];

    protected $ch = null;
    protected $data = null;
    protected $http_code = null;
    protected $is_error = false;

    public function __construct( string $url ) {
        $this->ch = curl_init();        // Initialising cURL

        // ampersand
        // $url = str_replace( '&', '&&', $url );

        $this->options[CURLOPT_URL] = $url;

        curl_setopt_array( $this->ch, $this->options );  // Setting cURL's options using the previously assigned array data
    }

    public function parse()
    {
        $this->data = curl_exec( $this->ch );       // Executing the cURL request and assiging the returned data to the $data attribute.

        $this->http_code = curl_getinfo( $this->ch, CURLINFO_HTTP_CODE );   // To check whether any error occur or not

        if ( $this->http_code != "200" ) {
            $this->is_error = true;
        }

        return $this->data;                         // Returning the data from the function
    }
}
