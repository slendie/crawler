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
        CURLOPT_SSL_VERIFYHOST      => false,
        CURLOPT_SSL_VERIFYPEER      => false,
    ];

    protected $url = null;
    protected $ch = null;
    protected $data = null;
    protected $err = null;
    protected $errmsg = null;
    protected $header = null;
    protected $has_error = false;
    protected $code = null;
    protected $location = null;

    public function __construct( string $url ) {
        $this->setUrl( $url );
    }

    public function parse()
    {
        $this->ch = curl_init();        // Initialising cURL

        $this->options[CURLOPT_URL] = $this->url;

        curl_setopt_array( $this->ch, $this->options );     // Setting cURL's options using the previously assigned array data

        $this->data         = curl_exec( $this->ch );           // Executing the cURL request and assiging the returned data to the $data attribute.
        $this->err          = curl_errno( $this->ch );
        $this->errmsg       = curl_error( $this->ch );
        $this->header       = curl_getinfo( $this->ch );        // To check whether any error occur or not
        $this->code         = curl_getinfo( $this->ch, CURLINFO_HTTP_CODE );
        $this->content_type = curl_getinfo( $this->ch, CURLINFO_CONTENT_TYPE );
        $this->location     = curl_getinfo( $this->ch, CURLINFO_EFFECTIVE_URL );

        curl_close( $this->ch );

        if ( $this->header['http_code'] != "200" ) {
            $this->has_error = true;
            return false;
        }
        return true;
    }

    public function header()
    {
        $this->ch = curl_init();        // Initialising cURL

        $this->options[CURLOPT_URL]     = $this->url;

        $options = $this->options;
        $options[CURLOPT_NOBODY]    = true;
        $options[CURLOPT_HEADER]    = true;
        $options[CURLOPT_FILETIME]  = true;

        curl_setopt_array( $this->ch, $options );           // Setting cURL's options using the previously assigned array data

        $output = curl_exec( $this->ch );           // Executing the cURL request and assiging the returned data to the $data attribute.

        $this->header       = curl_getinfo( $this->ch );        // To check whether any error occur or not
        $this->code         = curl_getinfo( $this->ch, CURLINFO_HTTP_CODE );
        $this->content_type = curl_getinfo( $this->ch, CURLINFO_CONTENT_TYPE );
        $this->location     = curl_getinfo( $this->ch, CURLINFO_EFFECTIVE_URL );

        curl_close( $this->ch );

        return $this->header;                               // Returning the data from the function
    }

    public function setUrl( $url )
    {
        $this->url = self::sanitize( $url );
    }

    public function getHeader()
    {
        return $this->header;
    }

    public function getCode()
    {
        return $this->code;
    }
    
    public function getLocation()
    {
        return $this->location;
    }

    public function getContentType()
    {
        return $this->content_type;
    }
    
    public function getContent()
    {
        return $this->data;
    }

    public function getErrNo()
    {
        return $this->err;
    }

    public function getErrMsg()
    {
        return $this->errmsg;
    }

    public static function sanitize( $url )
    {
        // $pattern = '/^([\w]+:\/\/)(.*)/';
        // if ( preg_match( $pattern, $url, $match ) ) {
        //     $protocol = $match[1];
        //     $address = $match[2];
            
        //     $address = str_replace( 'º', urlencode('º'), $address );
        //     $address = str_replace( 'ª', urlencode('ª'), $address );
    
        //     return $protocol . $address;
        // } else {
        //     return $url;
        // }
        $url = str_replace( 'º', urlencode('º'), $url );
        $url = str_replace( 'ª', urlencode('ª'), $url );
        return $url;
    }
}
