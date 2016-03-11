<?php
require __DIR__ . '/../src/Aldo/Lexer/Lexer.php';
require __DIR__ . '/../src/Aldo/Http/Request.php';
require __DIR__ . '/../src/Aldo/Element/ElementManager.php';
require __DIR__ . '/../src/Aldo/Element/Element.php';
require __DIR__ . '/../src/Aldo/Element/ElementFilter.php';

use Aldo\Lexer\Lexer;
use Aldo\Http\Request;
use Aldo\Element\ElementManager;
use Aldo\Element\ElementFilter;

/**
* Test the lexer
*/
class TestElement extends PHPUnit_Framework_TestCase
{
	public function testGetManager()
    {
        $request = new Request('http://localhost/Aldo/test.html');
		$html = $request->fetch();

		$lexer = new Lexer;
		$elementManager = $lexer->transform($html);

		return $elementManager;
    }

    /**
     * @depends testGetManager
     */
    public function testGetChildrenFromElement(ElementManager $elementManager)
    {
        $parent = $elementManager->getElement('#parent')[0];

        $children = $parent->getChildren();

        $this->assertEquals('child-class', $children[0]->attributes['class']);
    }
}
