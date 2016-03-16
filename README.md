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

// Create an HTTP request. Can be done with Aldo's own Request class, Guzzle, or your own library
$request = new Request('http://localhost/Aldo/test.html');
$html = $request->fetch();

// Transform the HTML into an array of elements and return the element manager
$lexer = new Lexer();
$elementManager = $lexer->transform($html);
```

## Elements
This is what a typical element looks like
```
Element => {
    [id] => index of elements array,
    [tag] => HTML tag name,
    [value] => If the element has inner text or value attribute,
    [attributes] =>
        [class] => array|string depending on number of classes
        [id] => ID of element,
        any other attributes can be found here
    [parent] => index of parent in elements array
}
```

## Element Management
```php

// Getting an element
$elementManager->getElement('a#bob.class-here'); // using a selector, only supports tag name, id and class. Optional 2nd parameter for an elements array returned from getChildren()
$elementManager->getElementWithAttributes(['tag' => 'a', 'id' => 'bob', 'class' => ['class-here']]); // class can also be a string if it is one class
$elementManager->getElementByIndex(0); // Gets the element by the index in the elements array. Both the opening and close tag count as 2 elements
$elementManager->getElementById('bob'); // Gets the element by HTML id
$elementManager->getElementsByClass('class-here'); // Gets the element using classes, can be either string or array

// Getting parent of element
$elementManager->getParent($element); // Retrieve parent from already fetched element
$elementManager->getParentByIndex(1); // Retrieve parent from index in the elements array. In this case the parent would be <html>
$element->getParent(); // Retrieve parent directly from element

// Getting children of element
$elementManager->getChildren($element); // Retrieve children from already fetched element
$elementManager->getChildrenByIndex(0); // Retrieve children from index in the elements array. This would return everything inside <html>
$element->getChildren(); // Retrieve children directly from element
```

## Element Filtering
As of right now, there is only 3 ways of filtering but more will be added in the future.
```php
use Aldo\Element\ElementFilter;

$emails = ElementFilter::getEmails($elements, $attribute); // Retrieve all emails from the elements array. Optional second parameter for searching within a specific attribute

$words = ElementFilter::getElementsWithWord($elements, $word, $attribute); // Retrieve all elements that have a specific word in them. Optional second parameter for searching within an attribute

$urls = ElementFilter::getUrls($elements, $attribute); // Retrieve all urls from the elements array. Optional second parameter for searching within an attribute

```

## Element Sorting
```php
use Aldo\Element\ElementSort;

$sortedElements = ElementSort::orderBy($elements, $attribute, $direction = 'asc'|'desc'); // Sort the elements by the attribute, and which direction you choose, default direction is asc.

// Please be aware that the closing tags are included within the elements array. Sorting by tag may result in the closing tags appearing first.
```

## Element Aliases
There are a few aliases to help retrieve certain attributes
```php
$element->link(); // Retrieves the href attribute, assuming it's an anchor tag

$element->source(); // Retrieves the src attribute

$element->val(); //Retrieves the value attribute or the inner text of element
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
