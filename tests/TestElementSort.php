<?php
require __DIR__ . '/../src/Aldo/Lexer/Lexer.php';
require __DIR__ . '/../src/Aldo/Http/Request.php';
require __DIR__ . '/../src/Aldo/Element/ElementManager.php';
require __DIR__ . '/../src/Aldo/Element/Element.php';
require __DIR__ . '/../src/Aldo/Element/ElementSort.php';

use Aldo\Lexer\Lexer;
use Aldo\Http\Request;
use Aldo\Element\ElementManager;
use Aldo\Element\ElementSort;

/**
* Test the lexer
*/
class TestElementFilter extends PHPUnit_Framework_TestCase
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
    public function testSortById(ElementManager $elementManager)
    {
        $elements = $elementManager->getElements();

        $sorted = ElementSort::orderBy($elements, 'id', 'desc');
        $this->assertEquals('parent', $sorted[5]->attributes['id']);

        $sorted2 = ElementSort::orderBy($elements, 'id', 'asc');
        $this->assertEquals('body', $sorted2[5]->tag);
    }

    /**
     * @depends testGetManager
     */
    public function testSortByTag(ElementManager $elementManager)
    {
        $elements = $elementManager->getElements();

        $sorted = ElementSort::orderBy($elements, 'tag', 'desc');
        $this->assertEquals('title', $sorted[0]->tag);

        $sorted2 = ElementSort::orderBy($elements, 'tag', 'asc');
        $this->assertEquals('/a', $sorted2[0]->tag);
    }
}
