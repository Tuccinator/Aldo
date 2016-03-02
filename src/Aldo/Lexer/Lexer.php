<?php
namespace Aldo\Lexer;

/**
* Lexer class for transforming HTML into objects and arrays
*/
class Lexer
{

	/**
	 * Transform a complete HTML string into individual tokens
	 *
	 * @var $html string HTML
	 */
	public function transform($html)
	{
		$lexemes	= $this->scan($html);
		$tokens		= $this->evaluate($lexemes);

		// return some type of element manager
	}

	/**
	 * Format HTML into individual nodes/lexemes
	 *
	 * @var $html string HTML
	 * @return array Lexemes formatted from HTML
	 */
	public function scan($html)
	{
		// set the delimiters for the scanner
		$delimiters 		= array('<', '>');

		/**
		 * Separate the HTML by the first delimiter '<'
		 */
		$first_sequence 	= explode($delimiters[0], $html);

		/**
		 * Separate each lexeme from the first sequence with the second delimiter '>'
		 */
		$second_sequence 	= function() use($first_sequence, $delimiters) {
			$lexemes = array();

			// iterate through each unformatted lexeme and format it
			foreach($first_sequence as $key => $lexeme_unformatted) {

				// if there is no second delimiter available in lexeme, it's not an HTML tag so move to next lexeme
				if(strstr($lexeme_unformatted, $delimiters[1]) == false) {
					continue;
				}

				// get rid of excess whitespace on rightside of string
				$lexeme_unformatted = rtrim($lexeme_unformatted);

				// separate lexeme and add to array
				$pieces = explode($delimiters[1], $lexeme_unformatted);

				// Do not add comment
				if(strstr($pieces[0], '!--')) {
					continue;
				}

				array_push($lexemes, $pieces[0]);

				// if there is content inside the HTML element, add it to the array aswell
				if(!empty($pieces[1])) {
					array_push($lexemes, $pieces[1]);
				}
			}

			// send back all formatted lexemes
			return $lexemes;
		};

		// execute the second sequence
		$sequence = $second_sequence();

		// send formatted lexemes to evaluator
		return $sequence;
	}

	/**
	 * Format lexemes into usable tokens
	 *
	 * @var $lexemes array All lexemes associated with HTML
	 * @return array Lexemes transformed into tokens
	 */
	public function evaluate($lexemes)
	{
		// array to hold all formatted tokens
		$tokens 	= array();

		// iterate through each lexeme to format it
		for($i = 0; $i < count($lexemes); $i++) {

			// set the attributes array
			$tokens[$i]['attributes'] = array();

			// split each individual lexeme by space
			$lexeme_parts = explode(' ', $lexemes[$i]);

			// set the tag name, if there is a tag name
			$tokens[$i]['tag'] = $lexeme_parts[0];

			// if there are attributes, go through them
			if(count($lexeme_parts) > 1) {
				for($attribute_index = 1; $attribute_index < count($lexeme_parts); $attribute_index++) {
					if(strstr($lexeme_parts[$attribute_index], '=')) {
						$attribute = explode('=', $lexeme_parts[$attribute_index]);
						$tokens[$i]['attributes'][$attribute[0]] = trim($attribute[1], '"');
					} else {
						// if attribute is an "empty attribute", automatically set to true
						$tokens[$i]['attributes'][$lexeme_parts[$attribute_index]] = true;
					}
				}
			}
		}


		// array to hold all incomplete parents
		$parents = array();

		// go through each token to set the parent
		for($token_index = 0; $token_index < count($tokens); $token_index++) {

			// parent is automatically null
			$parent = null;

			// if parent isn't set, set it to last incomplete parent
			if(!isset($tokens[$token_index]['parent'])) {

				// if there is a parent in parents array, set the most recent incomplete parent to current parent
				if(count($parents) > 0) {
					end($parents);
					$parent = key($parents);
				}
			}

			// make sure the current tag is a closing tag
			if(!strstr('/', $tokens[$token_index]['tag'])) {

				// if there is another tag after this one, check if it's a closing tag for current element
				if(isset($tokens[$token_index + 1])) {
					$closingTag = '/' . $tokens[$token_index]['tag'];

					// if the next element is a new element instead of closing tag, add current element as parent
					if($tokens[$token_index + 1]['tag'] != $closingTag) {
						$parents[$token_index] = $tokens[$token_index]['tag'];
					}
				}

			}

			// if there are any parents, check to see if most recent incomplete parent is found
			if(count($parents) > 0) {
				$closingParentTag = '/' . end($parents);

				// if found, remove parent from parents array
				if($tokens[$token_index]['tag'] == $closingParentTag) {
					array_pop($parents);
				}
			}

			// set the token's parent
			$tokens[$token_index]['parent'] = $parent;
		}

		// send tokens back to transformer for any other related formatting
		return $tokens;
	}
}
