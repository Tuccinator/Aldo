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
     * Get parent of element by array index
     *
     * @return array
     */
    public function getParentByIndex($index)
    {
        return $this->elements[$index]['parent'];
    }

    /**
     * Get parent using element
     *
     * @return array
     */
    public function getParent($element)
    {
        $parentId = $element['parent'];

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
            if($element['parent'] == $index) {
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
        $parentId = $element['id'];

        return $this->getChildrenByIndex($parentId);
    }
}
