<?php
namespace Aldo\Element;

/**
 * Element filter
 */
class ElementFilter
{

    /**
     * Get all emails associated with converted HTML
     *
     * TODO: Validate against a certain attribute, not just a value
     *
     * @var $elements array Elements returned from Aldo\Element\ElementManager
     * @return array
     */
    public static function getEmails($elements)
    {
        $emails = [];

        // go through each element
        foreach($elements as $element) {

            // check if there is a value
            if(!is_null($element->value)) {

                // check if the value is an email and add to email list
                if(filter_var($element->value, FILTER_VALIDATE_EMAIL)) {
                    array_push($emails, $element->value);
                }
            }
        }

        return $emails;
    }

    /**
     * Get all elements that have a specific word as it's value
     *
     * TODO: Search specific attributes for word
     *
     * @var $elements array HTML elements
     * @var $word string Word to find within the element
     * @return array
     */
    public static function getElementsWithWord($elements, $word)
    {
        $wordElements = [];

        // go through each element
        foreach($elements as $element) {

            // check if element has a value
            if(!is_null($element->value)) {

                // check if the specific word is inside the element's value, if so, add to new array
                if(strstr($element->value, $word)) {
                    array_push($wordElements, $element);
                }
            }
        }

        return $wordElements;
    }

    /**
     * Get all URLs associated with HTML
     *
     * @var $elements array HTML elements
     * @return array
     */
    public static function getUrls($elements)
    {
        $urls = [];

        // go through each element
        foreach($elements as $element) {

            // check if element is an anchor
            if($element->tag == 'a') {

                // check if there is a href attached to anchor
                if(isset($element->attributes['href'])) {

                    // make sure it's not an empty href and add to urls array
                    if(strlen($element->attributes['href']) > 0) {
                        array_push($urls, $element->attributes['href']);
                    }
                }
            }
        }

        return $urls;
    }
}
