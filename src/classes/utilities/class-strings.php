<?php
/**
 * Class containing utility methods for manipulating strings
 *
 * @author Amit Gupta
 *
 * @since  2022-02-05
 */
namespace G3\Utilities\Utilities;

use \G3\Utilities\Traits\Singleton;
use \G3\Utilities\Interfaces\Utility_Driver;
use \ErrorException;

class Strings implements Utility_Driver {

	use Singleton;

	/**
	 * Method to get the driver name of the class
	 *
	 * @return string
	 */
	public static function get_driver_name() : string {
		return 'strings';
	}

	/**
	 * Method to check if a string is empty or not.
	 * This is similar to `empty()` but it does not count whitespace as non-empty string.
	 *
	 * This is multibyte safe.
	 *
	 * @param string $txt
	 *
	 * @return bool
	 */
	public function is_empty( string $txt ) : bool {

		// Strip out all whitespace
		$txt = preg_replace( '/\s+/im', '', $txt );
		$txt = preg_replace( '/[\pZ\pC]+/uim', '', $txt );

		// Now we check if there's anything in the string
		return ( 1 > mb_strlen( $txt ) );

	}

	/**
	 * Method to remove slash from beginning of text string
	 *
	 * @param string $txt
	 *
	 * @return string
	 */
	public function unleadingslashit( string $txt ) : string {
		return ltrim( $txt, '/' );
	}

	/**
	 * Check if a given string is a valid name or not.
	 * The string must have only alphanumeric letters, underscores, hyphens and spaces.
	 *
	 * @param string $txt
	 *
	 * @return bool
	 */
	public function is_name( string $txt ) : bool {

		// We want to make sure our string has more than
		// just spaces, hyphens and underscores
		$txt = preg_replace( '/[\s\-_]+/i', '', $txt );

		return (bool) ( preg_match( '/^[\w]+$/i', $txt ) );

	}

	/**
	 * This is a multibyte version of str_replace() with a couple of caveats.
	 * Unlike the regular str_replace(), this does not accept an array of subjects/haystacks
	 * and unlike the regular str_replace(), it does not tell the number of replacements done.
	 *
	 * This is multibyte safe.
	 *
	 * @param string|array $search  The value being searched for, otherwise known as the needle. An array may be used to designate multiple needles.
	 * @param string|array $replace The replacement value that replaces found search values. An array may be used to designate multiple replacements only if search parameter is an array.
	 * @param string       $subject The string being searched and replaced on, otherwise known as the haystack.
	 *
	 * @return string
	 *
	 * @throws \ErrorException
	 */
	public function search_replace( $search, $replace, string $subject ) : string {

		$original_subject = $subject;

		$search  = ( is_numeric( $search ) ) ? (string) $search : $search;
		$replace = ( is_numeric( $replace ) ) ? (string) $replace : $replace;

		if (
			( ! is_string( $search ) && ! is_array( $search ) )
			|| ( ! is_string( $replace ) && ! is_array( $replace ) )
		) {
			throw new ErrorException(
				sprintf(
					'%1$s::%2$s() expects "search" and "replace" parameters to be of either String or Array type.',
					static::class,
					__FUNCTION__
				)
			);
		}

		if ( ! is_array( $search ) && is_array( $replace ) ) {
			throw new ErrorException(
				sprintf(
					'%1$s::%2$s() expects "replace" parameter to be of String type when "search" parameter is of String type.',
					static::class,
					__FUNCTION__
				)
			);
		}

		if ( is_array( $search ) && is_array( $replace ) && count( $search ) > count( $replace ) ) {
			throw new ErrorException(
				sprintf(
					'%1$s::%2$s() expects "replace" parameter to contain items equal in number to items in "search" parameter.',
					static::class,
					__FUNCTION__
				)
			);
		}

		$search  = ( is_array( $search ) ) ? array_values( $search ) : $search;
		$replace = ( is_array( $replace ) ) ? array_values( $replace ) : $replace;

		// Let's deal with array type on search and/or replace.
		// Recursive is the way!!
		if ( is_array( $search ) ) {

			$search_count     = count( $search );
			$is_replace_array = false;

			if ( is_array( $replace ) ) {

				$is_replace_array = true;

				if ( $search_count < count( $replace ) ) {
					$replace = array_slice( $replace, 0, $search_count );
					$replace = array_values( $replace );
				}

			}

			for ( $i = 0; $i < $search_count; $i++ ) {
				$replacement = ( true === $is_replace_array ) ? $replace[ $i ] : $replace;
				$subject     = $this->search_replace( $search[ $i ], $replacement, $subject );
			}

			return $subject;

		}  // end array handling block

		$subject = mb_split( preg_quote( $search ), $subject );
		$subject = implode( $replace, $subject );

		return $subject;

	}

	/**
	 * Method to get word count from a text. HTML tags and WP Shortcodes are not counted.
	 *
	 * This is multibyte safe.
	 *
	 * @param string $txt
	 *
	 * @return int
	 */
	public function get_word_count( string $txt ) : int {

		if ( $this->is_empty( $txt ) ) {
			return 0;
		}

		$txt = strip_shortcodes( $txt );
		$txt = trim( wp_strip_all_tags( $txt, true ) );

		// Using regex here instead of `str_word_count()` because this method
		// needs to be able to accommodate multibyte strings as well.
		return (int) preg_match_all( '/[\p{L}\'\-\xC2\xAD]+/uim', preg_quote( $txt, '/' ) );

	}

	/**
	 * Method to get the number of minutes needed to read given text.
	 *
	 * This is multibyte safe.
	 *
	 * @param string $txt
	 * @param int    $words_per_minute Average reading speed. By default, it considers 200 words per minute as the reading speed.
	 *
	 * @return int
	 *
	 * @throws \ErrorException
	 */
	public function get_minutes_to_read( string $txt, int $words_per_minute = 200 ) : int {

		$minutes = 0;

		if ( $this->is_empty( $txt ) ) {
			return $minutes;
		}

		$words_per_minute = absint( $words_per_minute );

		return (int) ( floor( $this->get_word_count( $txt ) / $words_per_minute ) );

	}

}  // end class

//EOF
