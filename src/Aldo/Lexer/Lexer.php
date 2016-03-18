<?php
namespace Aldo\Lexer;

use Aldo\Element\ElementManager;
use Aldo\Element\Element;

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

		$elementManager = new ElementManager($tokens);

		foreach($tokens as $token) {
			$token->setElementManager($elementManager);
		}

		return $elementManager;
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

				$lexeme = [
					$pieces[0],
					'value' => null
				];

				// if there is content inside the HTML element, add the value to current element
				if(!empty($pieces[1])) {
					$lexeme['value'] = $pieces[1];
				}

				array_push($lexemes, $lexeme);
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

			// split each individual lexeme by space
			$lexeme_parts = explode(' ', $lexemes[$i][0]);

			$tokens[$i] = new Element;

			// set the attributes array
			$tokens[$i]->attributes = array();

			// set the tag name, if there is a tag name
			$tokens[$i]->tag = $lexeme_parts[0];

			// set the value
			$tokens[$i]->value = $lexemes[$i]['value'];

			// set the current ID
			$tokens[$i]->id = $i;

			// if there are attributes, go through them
			if(count($lexeme_parts) > 1) {
				$last_attribute = '';
				$end_quote_found = false;

				for($attribute_index = 1; $attribute_index < count($lexeme_parts); $attribute_index++) {
					if(strstr($lexeme_parts[$attribute_index], '=')) {
						$attribute = explode('=', $lexeme_parts[$attribute_index]);

						// check if element is an input field with value attribute
						if($attribute[0] == 'value') {
							$tokens[$i]->value = trim($attribute[1], '"');
							continue;
						}

						$tokens[$i]->attributes[$attribute[0]] = trim($attribute[1], '"');

						// if there is only one class, don't check for more
						if($attribute[0] == 'class' && substr($lexeme_parts[$attribute_index], strlen($lexeme_parts[$attribute_index]) - 1, 1) == '"') {
							$end_quote_found = true;
						}

						if(isset($attribute[1])) {
							if(substr($attribute[1], strlen($attribute[1]) - 1, 1) != '"') {
								$last_attribute = $attribute[0];
								$end_quote_found = false;
							}
						}

					} else {

						if($lexeme_parts[$attribute_index] == 'required') {
							$tokens[$i]->attributes['required'] = true;
						}

						// check for multiple classes
						if($last_attribute == 'class' && $end_quote_found == false) {

							if(strstr($lexeme_parts[$attribute_index], '"')) {
								$end_quote_found = true;
							}

							// check if class is already set
							if(is_string($tokens[$i]->attributes['class'])) {
								$last_class = $tokens[$i]->attributes['class'];
								$tokens[$i]->attributes['class'] = array();
								$tokens[$i]->attributes['class'][] = $last_class;
							}

							$tokens[$i]->attributes['class'][] = trim($lexeme_parts[$attribute_index], '"');

							continue;
						}

						if(!strstr($lexeme_parts[$attribute_index], '"') && $end_quote_found == false) {
							if(strlen($last_attribute) > 1) {
								if(is_array($tokens[$i]->attributes[$last_attribute])) {
									$tokens[$i]->attributes[$last_attribute][] = $lexeme_parts[$attribute_index];
								} else {
									$tokens[$i]->attributes[$last_attribute] .= ' ' . $lexeme_parts[$attribute_index];
								}
							}
							continue;
						} else {
							if(strlen($last_attribute) > 1) {
								if(is_array($tokens[$i]->attributes[$last_attribute])) {
									$tokens[$i]->attributes[$last_attribute][] = trim($lexeme_parts[$attribute_index], '"');
								} else {
									$tokens[$i]->attributes[$last_attribute] .= ' ' . trim($lexeme_parts[$attribute_index], '"');
								}
								$end_quote_found = true;
							}
							continue;
						}

						// if attribute is an "empty attribute", automatically set to true
						$tokens[$i]->attributes[$lexeme_parts[$attribute_index]] = true;
					}
				}
			}
		}


		// array to hold all incomplete parents
		$parents 		= array();
		$empty_elements = array('link', 'track', 'param', 'area', 'command',
								'col', 'base', 'meta', 'hr', 'br', 'source',
								'img', 'keygen', 'wbr', 'input');

		// go through each token to set the parent
		for($token_index = 0; $token_index < count($tokens); $token_index++) {

			// parent is automatically null
			$parent = null;

			// make sure the current tag is not a closing tag
			if(!strstr($tokens[$token_index]->tag, '/')) {

				// if parent isn't set, set it to last incomplete parent
				if(!isset($tokens[$token_index]->parent)) {

					// if there is a parent in parents array, set the most recent incomplete parent to current parent
					if(count($parents) > 0) {
						end($parents);
						$parent = key($parents);
					}
				}

				// if there is another tag after this one, check if it's a closing tag for current element
				if(isset($tokens[$token_index + 1]) && !in_array($tokens[$token_index]->tag, $empty_elements)) {
					$closing_tag = '/' . $tokens[$token_index]->tag;

					// if the next element is a new element instead of closing tag, add current element as parent
					if($tokens[$token_index + 1]->tag != $closing_tag) {
						$parents[$token_index] = $tokens[$token_index]->tag;
					}
				}

				if(isset($tokens[$token_index]->attributes['/'])) {
					unset($tokens[$token_index]->attributes['/']);
				}

			}

			// if there are any parents, check to see if most recent incomplete parent is found
			if(count($parents) > 0) {
				$closing_parent_tag = '/' . end($parents);

				// if found, remove parent from parents array
				if($tokens[$token_index]->tag == $closing_parent_tag) {
					array_pop($parents);
				}
			}

			// set the token's parent
			$tokens[$token_index]->parent = $parent;
		}

		// send tokens back to transformer for any other related formatting
		return $tokens;
	}

	/**
	 * Rebuild an elements array into html
	 *
	 * @var $elements array Elements from evaluator
	 */
	public function rebuild($elements, $name = 'rebuild')
	{
		$empty_elements = array('link', 'track', 'param', 'area', 'command',
								'col', 'base', 'meta', 'hr', 'br', 'source',
								'img', 'keygen', 'wbr', 'input');

		$html = '';
		$parents = array();

		// go through elements
		for($i = 0; $i < count($elements); $i++) {

			// start the new element to be added to html
			$newElement = '';

			// check if this is the closing tag of last parent
			if(isset($elements[$i - 1])) {
				if('/' . end($parents) == $elements[$i]->tag) {
					array_pop($parents);
				}
			}

			// add a tab for each parent
			for($tab = 0; $tab < count($parents); $tab++) {
				if(isset($elements[$i - 1])) {
					if('/' . $elements[$i - 1]->tag != $elements[$i]->tag) {
						$newElement .= "\t";
					}
				}
			}

			// make sure the element isn't empty and a closing tag
			if(isset($elements[$i + 1])) {
				if(substr($elements[$i]->tag, 0, 1) != '/') {
					if(('/' . $elements[$i]->tag != $elements[$i + 1]->tag) && (!in_array($elements[$i]->tag, $empty_elements))) {
						array_push($parents, $elements[$i]->tag);
					}
				}
			}

			// add to the new elemnt
			$newElement .= '<';
			$newElement .= $elements[$i]->tag;

			// add all attributes
			if(count($elements[$i]->attributes) > 0) {
				$newElement .= ' ';

				// go through each attribute
				foreach($elements[$i]->attributes as $attribute => $value) {
					$newElement .= $attribute . '="';

					// if attribute is an array of values, implode with space
					if(is_array($value)) {
						$newElement .= implode(' ', $value);
					} else {
						$newElement .= $value;
					}

					$newElement .= '"';

					// check if this is last attribute so we can remove space
					end($elements[$i]->attributes);
					$lastAttribute = key($elements[$i]->attributes);
					if($lastAttribute !== $attribute) {
						$newElement .= ' ';
					}
				}
			}


			// check if element is an empty element and self-close it
			if(in_array($elements[$i]->tag, $empty_elements)) {
				if(!is_null($elements[$i]->value)) {
					$newElement .= ' value="' . $elements[$i]->value . '"';
				}
				$newElement .= ' />' . "\n";
				$html .= $newElement;
				continue;
			}

			// if there is a value of element, add to element
			if(!is_null($elements[$i]->value)) {
				$newElement .= '>' . $elements[$i]->value;
			} else {
				$newElement .= '>';
			}

			// if element is one line, don't make a line break
			if(isset($elements[$i + 1])) {
				if($elements[$i + 1]->tag == '/' . $elements[$i]->tag) {
					$html .= $newElement;
					continue;
				}
			}

			$newElement .= "\n";
			$html .= $newElement;
		}

		// open html file and write to it
		$file_handler = fopen(__DIR__ . '/../../../' . $name . '.html', 'w');
		fwrite($file_handler, $html);
		fclose($file_handler);
	}
}
