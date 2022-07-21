<?php
/**
 * Trait to implement the Singleton pattern in a class
 *
 * @author Amit Gupta
 *
 * @since  2021-12-06
 */

namespace G3\Utilities\Traits;

trait Singleton {

	/**
	 * @var object
	 */
	protected static $_instance;

	/**
	 * Class constructor.
	 *
	 * This has been set to `protected` visibility to prevent direct
	 * object creation.
	 *
	 * It is meant to be overridden in the classes which use this trait.
	 * It works like a normal constructor, runs on class instantiation
	 * but since this is implementing Singleton pattern, the class instantiation
	 * will happen only once pet code execution cycle.
	 */
	protected function __construct() {}

	/**
	 * Let's avoid any object cloning.
	 */
	final protected function __clone() {}

	/**
	 * Method to return Singleton object of the
	 * current class.
	 *
	 * This is a variadic method which is able to accept
	 * unspecified number of arguments. All of those arguments
	 * are passed to the constructor of the class using this Trait
	 * as individual arguments. Whether those arguments
	 * are used or not depends on the class using this Trait.
	 *
	 * This method has been set as final intentionally,
	 * because it is not meant to be overridden.
	 *
	 * @param array $args
	 *
	 * @return object
	 */
	final public static function get_instance( ...$args ) : self {

		if ( ! is_a( static::$_instance, static::class ) ) {
			static::$_instance = new static( ...$args );
		}

		return static::$_instance;

	}

}  // end trait

//EOF
