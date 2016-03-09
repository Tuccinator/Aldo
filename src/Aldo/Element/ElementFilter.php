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
}
