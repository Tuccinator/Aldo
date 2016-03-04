<?php
namespace Aldo\Element;

/**
 * Element class
 */
class Element
{
    /**
     * @var $id integer ID in Element\ElementManager elements array
     */
    public $id;

    /**
     * @var $tag string HTML tag name
     */
    public $tag;

    /**
     * @var $attributes array Element's HTML attributes
     */
    public $attributes;

    /**
     * @var $parent integer|null ID of parent in Element\ElementManager elements array
     */
    public $parent;

    /**
     * @var $value string|null Inner text or value of element
     */
    public $value;
}
