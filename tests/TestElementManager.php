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
        $request = new Request('http://localhost/Aldo/test.html');
		$html = $request->fetch();

		$lexer = new Lexer;
		$elementManager = $lexer->transform($html);

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
	public function testGetElementById(ElementManager $elementManager)
	{
		$element = $elementManager->getElementById('bye-container');

		$this->assertEquals('bye-town', $element->attributes['class']);
	}

	/**
	 * @depends testGetManager
	 */
	public function testGetElementsByClass(ElementManager $elementManager)
	{
		$elements = $elementManager->getElementsByClass('hi-town');

		$this->assertEquals('input', $elements[1]->tag);
	}

	/**
	 * @depends testGetManager
	 */
	public function testGetElementsByClassWithMultiple(ElementManager $elementManager)
	{
		$elements = $elementManager->getElementsByClass(['hi-town', 'tom-town']);

		$this->assertEquals([], $elements);

		$elements = $elementManager->getElementsByClass(['test-multiple', 'hi-town']);

		$this->assertEquals('input', $elements[1]->tag);
	}

	/**
	 * @depends testGetManager
	 */
	public function testGetElementsWithAttributes(ElementManager $elementManager)
	{
		$elements1 = $elementManager->getElementWithAttributes(['id' => 'hi-container', 'class' => 'bob-ville']);
		$this->assertEmpty($elements1);

		$elements2 = $elementManager->getElementWithAttributes(['id' => 'hi-container', 'class' => ['text-center', 'bob-ville']]);
		$this->assertEmpty($elements2);

		$elements3 = $elementManager->getElementWithAttributes(['id' => 'hi-container', 'class' => ['text-center', 'hi-town']]);
		$this->assertCount(1, $elements3);

		$elements4 = $elementManager->getElementWithAttributes(['class' => ['hi-town', 'test-multiple']]);
		$this->assertCount(2, $elements4);
	}

	/**
	 * @depends testGetManager
	 */
	public function testGetElementUsingSelector(ElementManager $elementManager)
	{
		$elements1 = $elementManager->getElement('#hi-container.bob-ville');
		$this->assertEmpty($elements1);

		$elements2 = $elementManager->getElement('#hi-container.text-center.bob-ville');
		$this->assertCount(0, $elements2);

		$elements3 = $elementManager->getElement('#hi-container.text-center.hi-town');
		$this->assertNotEmpty($elements3);

		$elementsA = $elementManager->getElement('a');
		$this->assertCount(2, $elementsA);

		$elements4 = $elementManager->getElement('.hi-town.test-multiple');
		$this->assertCount(2, $elements4);
	}

    /**
     * @depends testGetManager
     */
    public function testGetChildrenByIndex(ElementManager $elementManager)
    {
        $children = $elementManager->getChildrenByIndex(5);
		array_shift($children);

		$child = array_shift($children);

        $this->assertEquals('bye-container', $child->attributes['id']);
    }

	/**
     * @depends testGetManager
     */
    public function testGetChildren(ElementManager $elementManager)
    {
		$element = $elementManager->getElementByIndex(5);
        $children = $elementManager->getChildren($element);

		$child1 = array_shift($children);
		$this->assertEquals('hi-container', $child1->attributes['id']);

		$child2 = array_shift($children);
        $this->assertEquals('bye-town', $child2->attributes['class']);
    }

	/**
	 * @depends testGetManager
	 */
	public function testGetChildrenHasChild(ElementManager $elementManager)
	{
		$parent = 5;
		$element = $elementManager->getElementByIndex($parent);
		$children = $elementManager->getChildren($element);

		foreach($children as $child) {
			if($child->parent != $parent) {
				$this->assertEquals('child-class', $child->attributes['class']);
				break;
			}
		}
	}

	/**
	 * @depends testGetManager
	 */
	public function testGetElementAttributeWithSpaces(ElementManager $elementManager)
	{
		$element = $elementManager->getElement('#titleDiv');

		$this->assertEquals('test spaces in attribute', $element->attributes['title']);
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

	/**
	 * @depends testGetManager
	 */
	public function testGetElementByIndexInput(ElementManager $elementManager)
	{
		$element = $elementManager->getElementByIndex(14);

		$this->assertEquals('input', $element->tag);
	}

	/**
	 * @depends testGetManager
	 */
	public function testGetElementByIndexTwoEmptyElementsInARow(ElementManager $elementManager)
	{
		$element1 = $elementManager->getElementByIndex(14);
		$element2 = $elementManager->getElementByIndex(15);

		// check parents
		$this->assertEquals(5, $element1->parent);
		$this->assertEquals(5, $element2->parent);

		// check tags
		$this->assertEquals('input', $element1->tag);
		$this->assertEquals('hr', $element2->tag);
	}
}
