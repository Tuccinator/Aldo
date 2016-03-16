# Aldo

Simple, yet relatively advanced HTML scraper.
---
Most page scrapers built in PHP can be tedious to use, while providing unintended results. These page scrapers iterate through the HTML
for each independent "DOM Extraction", thus making it slow to use. Once the results are received, you still need to manipulate and sort the data yourself, which can be difficult without knowledge of JavaScript.

Aldo aims to make it almost effortless to fetch results from a remote website.

## Installation
Unfortunately this project is not yet up on composer. So for installation as of current, you need to fork the repository or download the ZIP file.

## How to Use
```php
use Aldo\Lexer\Lexer;
use Aldo\Http\Request;

// Create an HTTP request. Can be done with Aldo's own Request class or Guzzle, or your own library
$request = new Request('http://localhost/Aldo/test.html');
$html = $request->fetch();

// Transform the HTML into an array of elements and return the element manager
$lexer = new Lexer();
$elementManager = $lexer->transform($html);
```

## TODO
* [x] HTTP Requests
* [x] Element Manager
* [x] Selectors for ID, class, and tag name
* [x] Sorting
* [x] Filtering (getting emails)
* [ ] Rebuild HTML
* [x] Parent/children
* [x] Set value of element, instead of creating a new array for value
* [x] Handle HTML empty elements: input, br, etc
* [x] Do not include comments in sequence
* [x] Alias functions for certain attributes; href => link(), src => source(), value => val(), etc
* [x] Support multiple classes in element
* [x] Turn arrays into objects
