# Aldo

Simple, yet relatively advanced HTML scraper.
---
Most page scrapers built in PHP can be tedious to use, while providing unintended results. These page scrapers iterate through the HTML
for each independent "DOM Extraction", thus making it slow to use. Once the results are received, you still need to manipulate and sort the data yourself, which can be difficult without knowledge of JavaScript.

Aldo aims to make it almost effortless to fetch results from a remote website.

# TODO
* [x] HTTP Requests
* [x] Element Manager
* [ ] Selectors for ID, class and other types
* [ ] Sorting
* [ ] Filtering
* [ ] Rebuild HTML

## Small TODO
* [x] Parent/children
* [x] Set value of element, instead of creating a new array for value
* [x] Handle HTML empty elements: input, br, etc
* [x] Do not include comments in sequence
* [x] Alias functions for certain attributes; href => link(), src => source(), value => val(), etc
* [x] Support multiple classes in element
* [x] Turn arrays into objects
