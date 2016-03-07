<?php
namespace Aldo\Element;

/**
 * Element manager
 */
class ElementManager
{
    /**
     * @var $elements array All elements associated with current manager
     */
    private $elements;

    /**
     * @param $elements array All elements
     */
    public function __construct($elements)
    {
        $this->elements = $elements;
    }

    /**
     * Get all elements
     *
     * @return array
     */
    public function getElements()
    {
        return $this->elements;
    }

    public function getElementWithAttributes($attributes)
    {
        $elements = array();

        foreach($this->elements as $element) {
            $elementValid = true;
            $attributeCheck = true;

            foreach($attributes as $attribute => $value) {
                if(!isset($element->attributes[$attribute])) {
                    $elementValid = false;
                    break;
                }

                if(is_string($element->attributes[$attribute])) {
                    if($element->attributes[$attribute] != $value) {
                        $elementValid = false;
                    }
                }

                if(is_array($element->attributes[$attribute])) {
                    if(is_array($value)) {
                        foreach($value as $extra) {
                            if(!in_array($extra, $element->attributes[$attribute])) {
                                $attributeCheck = false;
                            }
                        }
                    } else {
                        if(!in_array($value, $element->attributes[$attribute])) {
                            $attributeCheck = false;
                        }
                    }
                }
            }

            if($elementValid && $attributeCheck) {
                array_push($elements, $element);
            }
        }

        return $elements;
    }

    /**
     * Get specific element by array index
     *
     * @return array
     */
    public function getElementByIndex($index)
    {
        return $this->elements[$index];
    }

    /**
     * Get element by ID
     *
     * @var $id string ID of HTML element to find
     * @return Aldo\Element\Element
     */
    public function getElementById($id)
    {
        $element = null;

        // go through each element to find the ID
        foreach ($this->elements as $key => $potentialElement) {
            if(isset($potentialElement->attributes['id'])) {

                // when the element is found, set the $element to found element
                if($potentialElement->attributes['id'] == $id) {
                    $element = $potentialElement;
                    break;
                }
            }
        }

        return $element;
    }

    /**
     * Get element by a single class or multiple classes
     *
     * @var $classes string|array HTML classes
     * @return array
     */
    public function getElementsByClass($classes)
    {
        $elements = array();

        // check if it's a single class
        if(is_string($classes)) {

            // go through each element looking for class
            foreach($this->elements as $potentialElement) {
                if(isset($potentialElement->attributes['class'])) {
                    if(count($potentialElement->attributes['class']) == 1) {
                        if($potentialElement->attributes['class'] == $classes) {
                            array_push($elements, $potentialElement);
                        }
                    } else {
                        if(in_array($classes, $potentialElement->attributes['class'])) {
                            array_push($elements, $potentialElement);
                        }
                    }
                }
            }

            return $elements;
        }

        // check if it's multiple classes
        if(is_array($classes)) {

            foreach($this->elements as $potentialElement) {
                if(isset($potentialElement->attributes['class'])) {
                    if(count($potentialElement->attributes['class']) > 1) {

                        $classesToFind = array();

                        foreach($classes as $class) {
                            $classesToFind[$class] = false;
                        }

                        foreach($potentialElement->attributes['class'] as $class) {
                            if(array_key_exists($class, $classesToFind)) {
                                $classesToFind[$class] = true;
                            }
                        }

                        if(!in_array(false, $classesToFind)) {
                            array_push($elements, $potentialElement);
                        }
                    }
                }
            }

            return $elements;
        }

    }

    /**
     * Get parent of element by array index
     *
     * @return array
     */
    public function getParentByIndex($index)
    {
        return $this->elements[$index]->parent;
    }

    /**
     * Get parent using element
     *
     * @return array
     */
    public function getParent($element)
    {
        $parentId = $element->parent;

        return $this->elements[$parentId];
    }

    /**
     * Get children using parent's index
     *
     * @return array
     */
    public function getChildrenByIndex($index)
    {
        $children = array();

        $elements = $this->getElements();

        foreach($elements as $element) {
            if($element->parent == $index) {
                array_push($children, $element);
            }
        }

        return $children;
    }

    /**
     * Get parent using parent
     *
     * @return array
     */
    public function getChildren($element)
    {
        $parentId = $element->id;

        return $this->getChildrenByIndex($parentId);
    }
}
