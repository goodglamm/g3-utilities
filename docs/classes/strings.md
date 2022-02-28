
# `g3-utilities`

## class `Strings`

The `Strings` class is used as a utility driver with the `G3` class to provide utilities when working with data of `string` type. It can be used directly on its own as well.

### `get_instance()`

`Strings::get_instance()` method is available on the class since it uses the `Singleton` trait. This method returns a `Singleton` object of this class.

### `is_empty( string  $txt ) : bool`

This method can be used to determine if a `string` var is empty or has any data in it. This is slightly different than the [`empty()`](https://www.php.net/manual/en/function.empty.php) function in PHP because that one returns `false` even if a string has nothing but whitespace. So if a string needs to be checked for existence of non-whitespace data, then `is_empty()` can be used for that purpose.

Consider following examples

```php
$a = "	 \n";  // tab, space and new line
$b = '	abcd ';
$c = "	\xc2\xa0 \n";  // tab, unicode whitespace, space and new line
$d = "	संतरा \n";  // tab, multibyte string, space and new line

var_dump( G3::strings()->is_empty( $a ) );  // will return TRUE
var_dump( G3::strings()->is_empty( $b ) );  // will return FALSE
var_dump( G3::strings()->is_empty( $c ) );  // will return TRUE
var_dump( G3::strings()->is_empty( $d ) );  // will return FALSE
```

This method takes care of unicode whitespace characters as well and works with multibyte strings as well.

### `unleadingslashit( string  $txt ) : string`

This method is similar to the [`untrailingslashit()`](https://developer.wordpress.org/reference/functions/untrailingslashit/) in WordPress. The method from WP removes trailing slash from a string and `Strings::unleadingslashit()` removes the leading slashes from a string.

### `is_name( string  $txt ) : bool`

This method provides a quick way to check if a string is a valid name or not. The string must have only alphanumeric letters, underscores, hyphens and spaces. It returns `TRUE` if string is a valid name else it returns `FALSE`.

### `search_replace( $search, $replace, string  $subject ) : string`

This is a multibyte version of [`str_replace()`](https://www.php.net/manual/en/function.str-replace.php) with a couple of caveats. Unlike the regular `str_replace()`, this does not:
- accept an array of subjects/haystacks
- tell the number of replacements done

### `get_word_count( string  $txt ) : int`

This method can be used to get word count from a text. HTML tags and WordPress Shortcodes are ignored in the count. This method works with multibyte strings.

### `get_minutes_to_read( string  $txt, int  $words_per_minute = 200 ) : int`

This method can be used to get the number of minutes needed to read given text. This method works with multibyte strings. By default it assumes a reading speed of 200 words per minute.
