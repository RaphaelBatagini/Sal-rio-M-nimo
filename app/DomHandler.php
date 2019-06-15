<?php

namespace App;

use GuzzleHttp\Client;

/**
 * Dom Handler
 *
 * @author Raphael Batagini
 */
class DomHandler {

    /**
     * Get page content
     * 
     * Get the content of a page using Guzzle
     *
     * @access private
     * @author Raphael Batagini
     * @param string $url Page address
     * @return string Page content
     */
    public static function getUrlContent($url)
    {
        $client = new Client();
        $response = $client->get($url);
        $content = $response->getBody()->getContents();
        return $content;
    }

    /**
     * HTML to array of values
     * 
     * Get an array of values from a $html string
     *
     * @access private
     * @author Raphael Batagini
     * @param string $html Source HTML
     * @return array HTML source as array
     */
    public static function htmlToArrayOfValues($html) 
    {
        $dom = new \DOMDocument();
        $dom->loadHTML($html);
        return self::domToArray($dom);
    }

    /**
     * DOM to array
     * 
     * Get an array from DOMDocumentType
     *
     * @access private
     * @author Raphael Batagini
     * @param string $root DOMDocument or DOMDocumentType root
     * @return array Array of DOM values
     */
    private static function domToArray($root) {
        $result = array();
        if ($root->hasAttributes()) {
            $attrs = $root->attributes;
            foreach ($attrs as $attr) {
                $result['attributes'][$attr->name] = $attr->value;
            }
        }
        if ($root->hasChildNodes()) {
            $children = $root->childNodes;
            if ($children->length == 1) {
                $child = $children->item(0);
                if ($child->nodeType == XML_TEXT_NODE) {
                    $result['_value'] = $child->nodeValue;
                    return count($result) == 1
                        ? $result['_value']
                        : $result;
                }
            }
            $groups = array();
            foreach ($children as $child) {
                if (!isset($result[$child->nodeName])) {
                    $result[$child->nodeName] = self::domToArray($child);
                } else {
                    if (!isset($groups[$child->nodeName])) {
                        $result[$child->nodeName] = array($result[$child->nodeName]);
                        $groups[$child->nodeName] = 1;
                    }
                    $result[$child->nodeName][] = self::domToArray($child);
                }
            }
        }
        return $result;
    }

    /**
     * Get table data
     * 
     * Extract table data from DOMDocument array
     *
     * @access private
     * @author Raphael Batagini
     * @param string $domArray DOMDocument array
     * @return array Array of DOM table values
     */ 
    public static function getTableData(array $domArray)
    {
        $iterator  = new \RecursiveArrayIterator($domArray);
        $recursive = new \RecursiveIteratorIterator(
            $iterator,
            \RecursiveIteratorIterator::SELF_FIRST
        );
        foreach ($recursive as $key => $value) {
            if ($key === 'table') {
                return self::getValuesFromTableData($value);
            }
        }
    }

    /**
     * Get values from table data
     * 
     * Extract values from table data
     *
     * @access private
     * @author Raphael Batagini
     * @param string $domTableArray DOMDocument table array
     * @return array Array of DOM table inner values
     */ 
    private static function getValuesFromTableData(array $domTablearray)
    {
        $iterator  = new \RecursiveArrayIterator($domTablearray);
        $recursive = new \RecursiveIteratorIterator(
            $iterator,
            \RecursiveIteratorIterator::SELF_FIRST
        );
        foreach ($recursive as $key => $value) {
            if ($key === '_value') {
                $return[] = $value;
            }
        }
        return $return;
    }
}