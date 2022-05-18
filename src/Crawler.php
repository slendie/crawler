<?php
namespace Slendie\Crawler;

use Slendie\Crawler\Curl;
use DOMDocument;
use DOMXpath;

class Crawler
{
    protected $url = '';
    protected $doc = '';
    protected $xml = '';

    protected $links = [];

    public function __construct( $url ) 
    {
        $this->url = $url;

        $curl = new Curl( $this->url );
        $data = $curl->parse();

        $this->doc = new DOMDocument();

        // fix html5/svg errors
        libxml_use_internal_errors(true);

        $this->doc->loadHTML( $data );
        $this->xpath = new DOMXpath( $this->doc );


        // echo $this->doc->saveHTML();
    }

    public function getMetas()
    {
        $metas = $this->xpath->query('//meta');
        $m = [];
        if ( !is_null( $metas )) {
            foreach( $metas as $meta ) {
                $name = $meta->getAttribute('name');
                if ( !empty( $name ) ) {
                    $m[] = [
                        'name'      => $name,
                        'content'   => $meta->getAttribute('content')
                    ];
                }
            }
        }
        return $m;

    }

    public function getLinks()
    {
        // $links = $this->doc->getElementsByTagName('a');
        $links = $this->xpath->query('//a[@href]');

        $l = [];
        if ( !is_null( $links ) ) {
            $this->links = $links;

            foreach( $links as $link ) {
                $href = $link->getAttribute('href');
                if ( !empty( $href ) ) {
                    $l[] = [
                        'href'      =>  $href,
                        'element'   =>  $link->C14N(),
                        'html'      =>  trim( $this->innerHTML( $link ) ),
                        'content'   =>  $link->textContent  // $link->nodeValue
                    ];
                }
            }
        }
        return $l;
    }

    public function googleSearchResults()
    {
        if ( count( $this->links ) == 0 ) {
            $this->getLinks();
        }
        
        $r = [];

        $organic_pattern = '/url\?q=(.*)&sa=/';
        foreach( $this->links as $link ) {
            $href = $link->getAttribute('href');
            $title = '';
            $extra = '';

            foreach( $link->childNodes as $node ) {
                if ( $node->nodeName == 'h3' ) {
                    $title = $node->nodeValue;
                } else {
                    $extra = $node->nodeValue;
                }
            }

            preg_match( $organic_pattern, $href, $match );
            if ( count( $match ) > 0 ) {
                $r[] = [
                    'link'  => $match[1],
                    'title' => $title,
                    'extra' => $extra,
                ];
            }
        }

        return $r;
    }

    private function innerHTML( $node ) {
        return implode( array_map( [$node->ownerDocument,"saveHTML"], 
                                   iterator_to_array($node->childNodes) ) );
    }
}