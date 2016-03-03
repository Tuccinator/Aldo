<?php
namespace Aldo\Element;

/**
 * Element manager
 */
class ElementManager
{

    private $elements;

    public function __construct($elements)
    {
        $this->elements = $elements;
    }

    public function getElements()
    {
        return $this->elements;
    }

    public function getElementByIndex($index)
    {
        return $this->elements[$index];
    }

    public function getParentByIndex($index)
    {
        return $this->elements[$index]['parent'];
    }

    public function getParent($element)
    {
        $parentId = $element['parent'];

        return $this->elements[$parentId];
    }

    public function getChildrenByIndex($index)
    {
        $children = array();

        $elements = $this->getElements();

        foreach($elements as $element) {
            if($element['parent'] == $index) {
                array_push($children, $element);
            }
        }

        return $children;
    }

    public function getChildren($element)
    {
        $parentId = $element['id'];

        return $this->getChildrenByIndex($parentId);
    }
}
