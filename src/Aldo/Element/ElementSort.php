<?php
namespace Aldo\Element;

/**
 * Element sorting
 */
class ElementSort
{
    public static function orderBy($elements, $name, $direction = 'asc')
    {
        usort($elements, function($a, $b) use($name, $direction) {
            switch($name) {
                case 'id':
                    $a = $a->id;
                    $b = $b->id;
                break;

                case 'value':
                    $a = $a->value;
                    $b = $b->value;
                break;

                case 'tag':
                    $a = $a->tag;
                    $b = $b->tag;
                break;

                default:
                    $a = 0;
                    $b = 0;

                    if(isset($a->attributes[$name])) {
                        $a = $a->attributes[$name];
                    }

                    if(isset($b->attributes[$name])) {
                        $b = $b->attributes[$name];
                    }
            }

            if($a == $b) {
                return 0;
            }

            if($direction == 'desc') {
                return ($a < $b) ? 1 : -1;
            }

            return ($a < $b) ? -1 : 1;
        });

        return $elements;
    }
}
