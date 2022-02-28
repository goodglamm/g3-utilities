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
	 * This method has been set as final intentionally,
	 * because it is not meant to be overridden.
	 *
	 * @return object
	 */
	final public static function get_instance() : object {

		if ( ! is_a( static::$_instance, static::class ) ) {
			static::$_instance = new static();
		}

		return static::$_instance;

	}

}  // end trait

//EOF
