<?php
require __DIR__ . '/../src/Aldo/Lexer/Lexer.php';
require __DIR__ . '/../src/Aldo/Http/Request.php';
require __DIR__ . '/../src/Aldo/Element/ElementManager.php';
require __DIR__ . '/../src/Aldo/Element/Element.php';

use Aldo\Lexer\Lexer;
use Aldo\Http\Request;
use Aldo\Element\ElementManager;

/**
* Test the lexer
*/
class TestElementManager extends PHPUnit_Framework_TestCase
{
	public function testGetManager()
    {
        $request 	= new Request('http://localhost/Aldo/test.html');
		$html 		= $request->fetch();

		$lexer            = new Lexer;
		$elementManager   = $lexer->transform($html);

		return $elementManager;
    }

    /**
     * @depends testGetManager
     */
    public function testGetElements(ElementManager $elementManager)
    {
        $elements = $elementManager->getElements();
        $this->assertEquals('bye-town', $elements[8]->attributes['class']);
    }

    /**
     * @depends testGetManager
     */
    public function testGetElementByIndexWithParentIndex(ElementManager $elementManager)
    {
        $parentId = $elementManager->getParentByIndex(8);
        $parent = $elementManager->getElementByIndex($parentId);

        $this->assertEquals('body', $parent->tag);
    }

	/**
     * @depends testGetManager
     */
    public function testGetElementByIndexWithParent(ElementManager $elementManager)
    {
		$element = $elementManager->getElementByIndex(8);
        $parent = $elementManager->getParent($element);

        $this->assertEquals('body', $parent->tag);
    }

    /**
     * @depends testGetManager
     */
    public function testGetChildrenByIndex(ElementManager $elementManager)
    {
        $children = $elementManager->getChildrenByIndex(5);

        $this->assertEquals('bye-container', $children[1]->attributes['id']);
    }

	/**
     * @depends testGetManager
     */
    public function testGetChildren(ElementManager $elementManager)
    {
		$element = $elementManager->getElementByIndex(5);
        $children = $elementManager->getChildren($element);

		$this->assertEquals('hi-container', $children[0]->attributes['id']);
        $this->assertEquals('bye-town', $children[1]->attributes['class']);
    }

	/**
	 * @depends testGetManager
	 */
	public function testElementAliasLink(ElementManager $elementManager)
	{
		$element = $elementManager->getElementByIndex(10);

		$this->assertEquals('/test/url', $element->link());
	}

	/**
	 * @depends testGetManager
	 */
	public function testElementAliasSource(ElementManager $elementManager)
	{
		$element = $elementManager->getElementByIndex(12);

		$this->assertEquals('http://example.com/test.js', $element->source());
	}

	/**
	 * @depends testGetManager
	 */
	public function testElementAliasVal(ElementManager $elementManager)
	{
		$element = $elementManager->getElementByIndex(8);

		$this->assertEquals('bye', $element->val());
	}
}
