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
    public function testGetEmails(ElementManager $elementManager)
    {
        $elements = $elementManager->getElements();

        $filter = new ElementFilter;

        $emails = $filter->getEmails($elements);

        $this->assertCount(3, $emails);
        $this->assertEquals('test2@example.com', $emails[1]);
        $this->assertEquals('test3@example.com', $emails[2]);
    }
}
