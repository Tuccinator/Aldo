<?php
require __DIR__ . '/../src/Aldo/Lexer/Lexer.php';
require __DIR__ . '/../src/Aldo/Http/Request.php';

use Aldo\Lexer\Lexer;
use Aldo\Http\Request;

/**
* Test the lexer
*/
class TestLexer extends PHPUnit_Framework_TestCase
{
	public function testScan()
	{
		$request 	= new Request('http://localhost/Aldo/test.html');
		$html 		= $request->fetch();

		$lexer 		= new Lexer;
		$sequence 	= $lexer->scan($html);

		$this->assertContains('html', $sequence[1]);

		return $sequence;
	}

	/**
	 * @depends testScan
	 */
	public function testEvaluateClass(array $sequence)
	{
		$lexer 	= new Lexer;
		$tokens = $lexer->evaluate($sequence);

		$this->assertContains('text-center', $tokens[8]['attributes']['class']);
	}

	/**
	 * @depends testScan
	 */
	public function testEvaluateId(array $sequence)
	{
		$lexer 	= new Lexer;
		$tokens = $lexer->evaluate($sequence);

		$this->assertContains('hi-container', $tokens[8]['attributes']['id']);
	}

	/**
	 * @depends testScan
	 */
	public function testEvaluateRequired(array $sequence)
	{
		$lexer 	= new Lexer;
		$tokens = $lexer->evaluate($sequence);

		$this->assertTrue($tokens[11]['attributes']['required']);
	}
}
