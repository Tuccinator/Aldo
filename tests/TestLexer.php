<?php
require __DIR__ . '/../src/Aldo/Lexer/Lexer.php';
require __DIR__ . '/../src/Aldo/Http/Request.php';
require __DIR__ . '/../src/Aldo/Element/Element.php';

use Aldo\Lexer\Lexer;
use Aldo\Http\Request;

/**
* Test the lexer
*/
class TestLexer extends PHPUnit_Framework_TestCase
{
	public function testScan()
	{
		$request = new Request('http://localhost/Aldo/test.html');
		$html = $request->fetch();

		$lexer = new Lexer;
		$sequence = $lexer->scan($html);

		$this->assertContains('head', $sequence[1]);

		return $sequence;
	}

	/**
	 * @depends testScan
	 */
	public function testEvaluateClass(array $sequence)
	{
		$lexer = new Lexer;
		$tokens = $lexer->evaluate($sequence);

		$this->assertContains('bye-town', $tokens[8]->attributes['class']);
	}

	/**
	 * @depends testScan
	 */
	public function testEvaluateMultipleClasses(array $sequence)
	{
		$lexer = new Lexer;
		$tokens = $lexer->evaluate($sequence);

		$this->assertContains('text-center', $tokens[6]->attributes['class'][0]);
		$this->assertContains('hi-town', $tokens[6]->attributes['class'][1]);
	}

	/**
	 * @depends testScan
	 */
	public function testEvaluateId(array $sequence)
	{
		$lexer = new Lexer;
		$tokens = $lexer->evaluate($sequence);

		$this->assertContains('hi-container', $tokens[6]->attributes['id']);
	}

	/**
	 * @depends testScan
	 */
	public function testEvaluateRequired(array $sequence)
	{
		$lexer = new Lexer;
		$tokens = $lexer->evaluate($sequence);

		$this->assertTrue($tokens[8]->attributes['required']);
	}

	/**
	 * @depends testScan
	 */
	public function testEvaluateParent(array $sequence)
	{
		$lexer = new Lexer;
		$tokens = $lexer->evaluate($sequence);

		$this->assertEquals(5, $tokens[8]->parent);
	}

	/**
	 * @depends testScan
	 */
	public function testEvaluateParentIsNull(array $sequence)
	{
		$lexer = new Lexer;
		$tokens = $lexer->evaluate($sequence);

		$this->assertEquals(null, $tokens[0]->parent);
	}
}
