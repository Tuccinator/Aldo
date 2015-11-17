<?php
namespace Aldo\Lexer;

/**
* Lexer class for transforming HTML into objects and arrays
*/
class Lexer
{

	public function transform($html)
	{
		$lexemes	= $this->_scan($html);
		$tokens		= $this->_evaluate($lexemes);

		// return some type of element manager
	}

	public function _scan($html)
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
				array_push($lexemes, $pieces[0]);

				// if there is content inside the HTML element, add it to the array aswell
				if(isset($pieces[1])) {
					if(!empty($pieces[1])) {
						array_push($lexemes, $pieces[1]);
					}
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
}