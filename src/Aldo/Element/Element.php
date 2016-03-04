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

    /**
     * Alias to return href of current element
     *
     * @return string|null
     */
    public function link()
    {
        if(isset($this->attributes['href'])) {
            return $this->attributes['href'];
        }

        return null;
    }

    /**
     * Alias to return src of current element
     *
     * @return string|null
     */
    public function source()
    {
        if(isset($this->attributes['src'])) {
            return $this->attributes['src'];
        }

        return null;
    }

    /**
     * Alias to return value of current element
     *
     * @return string|null
     */
    public function val()
    {
        if(!empty($this->value)) {
            return $this->value;
        }

        return null;
    }
}
