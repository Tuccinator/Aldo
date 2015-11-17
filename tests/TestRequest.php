<?php
require __DIR__ . '/../src/Aldo/Http/Request.php';

use Aldo\Http\Request;

/**
* Test case for request class
*/
class TestRequest extends PHPUnit_Framework_TestCase
{
	/**
	 * Assert that the fetch method of Request returns the correct response
	 */
	public function testFetch()
	{
		$request = new Request('http://localhost/Aldo/test.html');
		$results = $request->fetch();

		$this->assertContains('bye', $results);
	}
}