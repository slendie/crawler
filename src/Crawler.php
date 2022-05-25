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
    }

    public function html()
    {
        return $this->doc->saveHTML();
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
        // $nodes = $this->xpath->query('//div[class="g"]');

        // Google Maps
        $nodes = $this->xpath->query("//div[@class='X7NTVe']");
        $r = [];

        foreach( $nodes as $node ) {
            echo self::innerHTML( $node ) . PHP_EOL;
            $c = 0;
            $el = $node;
            $i = [];
            while ( $el->childElementCount > 0 ) {
                $c++;
                $class = $el->getAttribute('class');
                $classes = explode(' ', $class);
                echo "02.{$c} " . get_class( $el ) . ", " . $el->tagName . " (" . $class . "): " . $el->childElementCount  . PHP_EOL;


                switch ( $el->tagName ) {
                    case 'a':
                        $i['href'] = $el->getAttribute('href');
                        break;

                    // case 'h3':
                    //     $i['title'] = self::innerHTML( $el );
                    //     break;
                        
                    case 'div':
                        if ( in_array( 'BNeawe', $classes ) ) {
                            $i['name'] = $el->textContent;
                        } 
                        if ( in_array( 'Hk2yDb', $classes ) ) {
                            $i['classification'] = self::innerHTML( $el );
                        }
                        break;

                    case 'span':
                        
                        if ( in_array( 'UMOHqf', $classes ) ) {
                            $i['title'] = self::innerHTML( $el );
                        }
                        if ( in_array( 'oqSTJd', $classes ) ) {
                            $i['classification'] = self::innerHTML( $el );
                        }
                        break;

                }
                $el = $el->firstElementChild;
            }
            var_dump($i);
        }

        return $r;
    }

    private static function innerHTML( $node ) {
        return implode( array_map( [$node->ownerDocument,"saveHTML"], 
                                   iterator_to_array($node->childNodes) ) );
    }
}