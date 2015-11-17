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
		$sequence 	= $lexer->_scan($html);

		var_dump($sequence);

		$this->assertContains('html', $sequence[1]);
	}
}