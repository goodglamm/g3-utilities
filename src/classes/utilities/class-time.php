<?php
/**
 * Class to provide toolkit for manipulating time.
 *
 * @author Amit Gupta
 *
 * @since  2022-11-23
 */
namespace G3\Utilities\Utilities;

use \G3\Utilities\Traits\Singleton;
use \G3\Utilities\Interfaces\Utility_Driver;
use \ErrorException;

class Time implements Utility_Driver {

	use Singleton;

	/**
	 * Method to get the driver name of the class
	 *
	 * @return string
	 */
	public static function get_driver_name() : string {
		return 'time';
	}

	/**
	 * Method to get human-readable time difference between two timestamps.
	 *
	 * @param int $from
	 * @param int $to
	 *
	 * @return string
	 *
	 * @throws \ErrorException
	 */
	public function get_human_readable_diff( int $from, int $to ) : string {

		if ( 0 > $from || 1 > $to ) {
			throw new ErrorException(
				sprintf(
					/* translators: Placeholders are for class and method name. */
					__( '%1$s::%2$s() expects valid timestamps to calculate difference.', 'g3-utilities' ),
					static::class,
					__FUNCTION__
				)
			);
		}

		$readable_diff = [];
		$diff          = (int) ( $to - $from );

		if ( WEEK_IN_SECONDS <= $diff ) {

			$weeks = floor( $diff / WEEK_IN_SECONDS );
			$diff  = ( $diff % WEEK_IN_SECONDS );

			$readable_diff[] = sprintf(
				/* translators: Time difference between two dates, in weeks. %s: Number of weeks. */
				_n( '%s week', '%s weeks', $weeks, 'g3-utilities' ),
				$weeks
			);

		}

		if ( DAY_IN_SECONDS <= $diff ) {

			$days = floor( $diff / DAY_IN_SECONDS );
			$diff = ( $diff % DAY_IN_SECONDS );

			$readable_diff[] = sprintf(
				/* translators: Time difference between two dates, in days. %s: Number of days. */
				_n( '%s day', '%s days', $days, 'g3-utilities' ),
				$days
			);

		}

		if ( HOUR_IN_SECONDS <= $diff ) {

			$hours = floor( $diff / HOUR_IN_SECONDS );
			$diff  = ( $diff % HOUR_IN_SECONDS );

			$readable_diff[] = sprintf(
				/* translators: Time difference between two dates, in hours. %s: Number of hours. */
				_n( '%s hour', '%s hours', $hours, 'g3-utilities' ),
				$hours
			);

		}

		if ( MINUTE_IN_SECONDS <= $diff ) {

			$minutes = floor( $diff / MINUTE_IN_SECONDS );
			$diff    = ( $diff % MINUTE_IN_SECONDS );

			$readable_diff[] = sprintf(
				/* translators: Time difference between two dates, in minutes. %s: Number of minutes. */
				_n( '%s minute', '%s minutes', $minutes, 'g3-utilities' ),
				$minutes
			);

		}

		if ( MINUTE_IN_SECONDS > $diff ) {

			$seconds = $diff;

			$readable_diff[] = sprintf(
				/* translators: Time difference between two dates, in seconds. %s: Number of seconds. */
				_n( '%s second', '%s seconds', $seconds, 'g3-utilities' ),
				$seconds
			);

		}

		$readable_diff = implode( ' ', $readable_diff );

		return $readable_diff;

	}

}  // end class

//EOF
