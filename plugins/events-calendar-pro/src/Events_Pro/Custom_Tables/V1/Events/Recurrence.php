<?php
/**
 * Build '_EventRecurrence` format data in a fluent manner.
 *
 * @since   6.0.1
 *
 * @package TEC\Events_Pro\Custom_Tables\V1
 */

namespace TEC\Events_Pro\Custom_Tables\V1\Events;

use Closure;
use DateInterval;
use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use TEC\Events_Pro\Custom_Tables\V1\Events\Converter\From_Rset_Converter;
use TEC\Events_Pro\Custom_Tables\V1\Events\Rules\Date_Rule;
use Tribe__Date_Utils as Dates;
use Tribe__Timezones as Timezones;
use Tribe__Events__Pro__Editor__Recurrence__Blocks as Converter;

/**
 * Class Recurrence
 *
 * @since   6.0.1
 *
 * @package TEC\Events_Pro\Custom_Tables\V1
 */
class Recurrence {
	/**
	 * Whether rules should be normalized during creation or not.
	 *
	 * @since 6.0.1
	 *
	 * @var bool
	 */
	private $normalize_rules = false;

	/**
	 * A flag property indicating what is the destination of the current rule; either
	 * `rules` or `exclusions`.
	 *
	 * @since 6.0.1
	 *
	 * @var string
	 */
	private $rules_or_exclusion = 'rules';

	/**
	 * A map from 3-char-lowercase English day names to the corresponding
	 * iCalendar standard day number.
	 *
	 * @since 6.0.1
	 *
	 * @var array<string,int>
	 */
	private static $day_name_to_day_num_map = [
		'mon' => 1,
		'tue' => 2,
		'wed' => 3,
		'thu' => 4,
		'fri' => 5,
		'sat' => 6,
		'sun' => 7,
	];

	/**
	 * A reference to the last rule built by the factory.
	 *
	 * @since 6.0.1
	 *
	 * @var array
	 */
	private $last_rule;

	/**
	 * The format that should be used to build the time entry of a rule.
	 *
	 * @since 6.0.1
	 *
	 * @var string
	 */
	private static $time_format = 'g:ia';

	/**
	 * A map of the rules to build.
	 *
	 * @since 6.0.1
	 *
	 * @var array<string,array|DateTimeInterface|DateTimezone|string>
	 */
	private $build_rules = [
		'EventStartDate' => null,
		'EventEndDate'   => null,
		'EventTimezone'  => null,
		'rules'          => [],
		'exclusions'     => [],
	];

	/**
	 * A reference to the DTSTART object.
	 *
	 * @since 6.0.1
	 *
	 * @var \DateTimeImmutable|null
	 */
	private $dtstart;

	/**
	 * A reference to the DTEND object.
	 *
	 * @since 6.0.1
	 *
	 * @var \DateTimeImmutable|null
	 */
	private $dtend;

	/**
	 * A reference to the timezone object.
	 *
	 * @since 6.0.1
	 *
	 * @var DateTimeZone|null
	 */
	private $timezone;

	/**
	 * A flag property indicating whether the timezone has been locked or not.
	 *
	 * @since 6.0.1
	 *
	 * @var bool
	 */
	private $locked_timezone = false;

	/**
	 * A set of methods whose application has been delayed due to missing date data.
	 *
	 * @since 6.0.1
	 *
	 * @var array<Closure>
	 */
	private $delayed = [];

	/**
	 * Builds an instance of the class from an iCalendar format RSET string and dates.
	 *
	 * @since 6.0.1
	 *
	 * @param string            $string  The iCalendar format RSET string.
	 * @param DateTimeImmutable $dtstart The Event start date.
	 * @param DateTimeImmutable $dtend   The Event end date.
	 *
	 * @return Recurrence A new instance of the class.
	 *
	 * @throws \ReflectionException If there's an issue while introspecting the RSET for state.
	 */
	public static function from_icalendar_string( string $string, DateTimeImmutable $dtstart, DateTimeImmutable $dtend ): Recurrence {
		$instance = new self();
		$instance->with_start_date( $dtstart )->with_end_date( $dtend );
		$event_recurrence = ( new From_Rset_Converter() )->convert_to_event_recurrence_from_dates( $string, $dtstart, $dtend, true );

		// Pre-hidrate the instance rules and exclusion from the converted ones.
		if ( isset( $event_recurrence['rules'] ) ) {
			$instance->build_rules['rules'] = array_merge( $instance->build_rules['rules'], $event_recurrence['rules'] );
		}
		if ( isset( $event_recurrence['exclusions'] ) ) {
			$instance->build_rules['exclusions'] = array_merge( $instance->build_rules['exclusions'], $event_recurrence['exclusions'] );
		}

		return $instance;
	}

	/**
	 * Sets the Event start date (DTSTART) of the rules and exclusions to build.
	 *
	 * @since 6.0.1
	 *
	 * @param int|string|DateTimeInterface $date The Event start date and time.
	 *
	 * @return $this For chaining.
	 */
	public function with_start_date( $date ): Recurrence {
		$this->set_date_property( $date, 'dtstart' );
		$this->build_rules['EventStartDate'] = $this->dtstart->format( Dates::DBDATETIMEFORMAT );

		return $this;
	}

	/**
	 * Sets the Event end date (DTEND) of the rules and exclusions to build.
	 *
	 * @since 6.0.1
	 *
	 * @param int|string|DateTimeInterface $date The Event end date and time.
	 *
	 * @return $this For chaining.
	 */
	public function with_end_date( $date ): Recurrence {
		$this->set_date_property( $date, 'dtend' );
		$this->build_rules['EventEndDate'] = $this->dtend->format(Dates::DBDATETIMEFORMAT);

		return $this;
	}

	/**
	 * Sets the Event timezone of the rules and exclusions to build.
	 *
	 * @since 6.0.1
	 *
	 * @param string|DateTimezone $timezone The Event timezone string or object.
	 *
	 * @return $this For chaining.
	 */
	public function with_timezone( $timezone ): Recurrence {
		$timezone_object = Timezones::build_timezone_object( $timezone );
		$this->timezone = $timezone_object;
		$this->build_rules['EventTimezone'] = $timezone_object->getName();
		$this->locked_timezone = true;

		foreach ( [ 'dtstart', 'dtend' ] as $date_prop ) {
			if ( $this->{$date_prop} instanceof DateTimeImmutable ) {
				$this->{$date_prop} = Dates::immutable( $this->{$date_prop}->format( Dates::DBDATETIMEFORMAT ), $timezone_object );
			}
		}

		return $this;
	}

	/**
	 * Starts the construction of a Weekly recurrence rule.
	 *
	 * Monday is 1, Sunday is 7.
	 *
	 * @since 6.0.1
	 *
	 * @param int        $interval       The number of days between each occurrence.
	 * @param array|null $days           The days of the week on which the rule should apply.
	 * @param bool       $same_time      Whether the rule should apply on the same time as the Event or not.
	 * @param array|null $diff_time_data The time data to use when the rule should not apply on the same time as the
	 *                                   Event. The data should have shape like `[ '8am', '9am', 0]` indicating the
	 *                                   start time, end time and the number of days between start and end (`0` is the
	 *                                   same day).
	 *
	 * @return $this For chaining.
	 */
	public function with_weekly_recurrence( int $interval = 1, array $days = null, bool $same_time = true, array $diff_time_data = null ): Recurrence {
		if ( $this->delay( __FUNCTION__, func_get_args() ) ) {
			return $this;
		}

		if ( null === $days ) {
			$days = [ $this->dtstart->format( 'N' ) ];
		} else {
			$days = array_map( [ $this, 'day_to_number' ], $days );
		}

		if ( $same_time ) {
			$rule = [
				'type'   => 'Custom',
				'custom' =>
					[
						'interval'  => (string) $interval,
						'week'      => [ 'day' => $days, ],
						'same-time' => 'yes',
						'type'      => 'Weekly',
					],
			];
		} else {
			[ $start_time, $end_time, $end_day ] = $this->parse_diff_time_data( $diff_time_data );
			$rule = [
				'type'   => 'Custom',
				'custom' =>
					[
						'interval'   => (string) $interval,
						'week'       => [ 'day' => $days, ],
						'same-time'  => 'no',
						'type'       => 'Weekly',
						'start-time' => $start_time,
						'end-time'   => $end_time,
						'end-day'    => $end_day,
					],
			];
		}
		$this->last_rule = &$rule;
		/** @noinspection UnsupportedStringOffsetOperationsInspection */
		$this->build_rules[ $this->rules_or_exclusion ][] = &$rule;
		$this->with_end( 'after', 10 );

		return $this;
	}

	/**
	 * Sets the limit of the recurrence rule currently being built.
	 *
	 * @since 6.0.1
	 *
	 * @param string     $type    The type of limit to apply, one of `after`, `on`, `never`.
	 * @param string|int ...$args The arguments to use to build the limit; an integer for `after`,
	 *                            a date for `on` and nothing for `never`.
	 *
	 * @return $this For chaining.
	 */
	public function with_end( string $type, ...$args ): Recurrence {
		if ( $this->delay( __FUNCTION__, func_get_args() ) ) {
			return $this;
		}

		$type = strtolower( $type );

		switch ( $type ) {
			default:
			case 'never':
				unset( $this->last_rule['end-count'], $this->last_rule['end'] );
				$this->last_rule['end-type'] = 'Never';
				break;
			case 'after':
				unset( $this->last_rule['end'] );
				$this->last_rule['end-type'] = 'After';
				$this->last_rule['end-count'] = (int) $args[0];
				break;
			case 'on':
				unset( $this->last_rule['end-count'] );
				$this->last_rule['end-type'] = 'On';
				$this->last_rule['end'] = ! empty( $args[0] ) ? Dates::immutable( $args[0], $this->timezone )
					->format( Dates::DBDATEFORMAT ) : '';
				break;
		}

		return $this;
	}

	/**
	 * Sets the limit of the recurrence rule currently being built to never-ending.
	 *
	 * @since 6.0.1
	 *
	 * @return $this For chaining.
	 */
	public function with_end_never(): Recurrence {
		return $this->with_end( 'never' );
	}

	/**
	 * Sets the limit of the recurrence rule currently being built to a number of Occurrences (always including the
	 * DTSTART!);
	 *
	 * @since 6.0.1
	 *
	 * @param int $after The number of occurrences after which the recurrence should end.
	 *
	 * @return $this For chaining.
	 */
	public function with_end_after( int $after ): Recurrence {
		return $this->with_end( 'after', $after );
	}

	/**
	 * Sets the limit of the recurrence rule currently being built to a specific date.
	 *
	 * @since 6.0.1
	 *
	 * @param string|int|DateTimeInterface $date The date on which the recurrence should end.
	 *
	 * @return $this For chaining.
	 */
	public function with_end_on( $date ): Recurrence {
		return $this->with_end( 'on', $date );
	}

	/**
	 * Outputs the recurrence rules and exclusions in the format used by the `_EventRecurrence` meta field.
	 *
	 * @since 6.0.1
	 *
	 * @return array<string,array> The recurrence rules and exclusions in the format used by the `_EventRecurrence`
	 *                             meta field.
	 */
	public function to_event_recurrence_format(): array {
		$this->apply_delayed_methods();

		if ( ! ( $this->dtstart instanceof DateTimeImmutable && $this->dtend instanceof DateTimeImmutable ) ) {
			throw new \BadMethodCallException( 'Cannot output recurrence rules without a valid start and end date' );
		}

		$data = [
			'EventStartDate' => $this->dtstart->format( Dates::DBDATETIMEFORMAT ),
			'EventEndDate'   => $this->dtend->format( Dates::DBDATETIMEFORMAT ),
			'EventDuration'  => $this->dtend->getTimestamp() - $this->dtstart->getTimestamp(),
			'EventTimezone'  => $this->dtstart->getTimezone()->getName(),
			'recurrence'     =>
				[
					'rules'       => [],
					'exclusions'  => [],
					'description' => null,
				],
		];

		foreach ( $this->build_rules['rules'] as $rule ) {
			$rule['EventStartDate'] = $data['EventStartDate'];
			$rule['EventEndDate'] = $data['EventEndDate'];
			$data['recurrence']['rules'][] = $rule;
		}

		foreach ( $this->build_rules['exclusions'] as $exclusion ) {
			$exclusion['EventStartDate'] = $data['EventStartDate'];
			$exclusion['EventEndDate'] = $data['EventEndDate'];
			$data['recurrence']['exclusions'][] = $exclusion;
		}

		return $data;
	}

	/**
	 * Starts the construction of a Daily recurrence rule.
	 *
	 * @since 6.0.1
	 *
	 * @param int        $interval       The number of days between each occurrence.
	 * @param bool       $same_time      Whether the rule should apply on the same time as the Event or not.
	 * @param array|null $diff_time_data The time data to use when the rule should not apply on the same time as the
	 *                                   Event. The data should have shape like `[ '8am', '9am', 0]` indicating the
	 *                                   start time, end time and the number of days between start and end (`0` is the
	 *                                   same day).
	 *
	 * @return Recurrence The recurrence rules and exclusions in the format used by the `_EventRecurrence`
	 *                             meta field.
	 */
	public function with_daily_recurrence( int $interval = 1, bool $same_time = true, array $diff_time_data = null ): Recurrence {
		if ( $this->delay( __FUNCTION__, func_get_args() ) ) {
			return $this;
		}

		if ( $same_time ) {
			$rule = [
				'type'   => 'Custom',
				'custom' =>
					[
						'interval'  => (string) $interval,
						'same-time' => 'yes',
						'type'      => 'Daily',
					],
			];
		} else {
			[ $start_time, $end_time, $end_day ] = $this->parse_diff_time_data( $diff_time_data );
			$rule = [
				'type'   => 'Custom',
				'custom' =>
					[
						'interval'   => (string) $interval,
						'same-time'  => 'no',
						'start-time' => $start_time,
						'end-time'   => $end_time,
						'end-day'    => $end_day,
						'type'       => 'Daily',
					],
			];
		}

		$this->last_rule = &$rule;
		/** @noinspection UnsupportedStringOffsetOperationsInspection */
		$this->build_rules[ $this->rules_or_exclusion ][] = &$rule;
		$this->with_end( 'after', 10 );

		return $this;
	}

	/**
	 * Starts the construction of a Monthly recurrence rule.
	 *
	 * @since 6.0.1
	 *
	 * @param int        $interval       The number of days between each occurrence.
	 * @param bool       $same_day       Whether the rule should apply on the same numeric day as the Event start date
	 *                                   or not.
	 * @param int|string $number         If the rule should not apply on the same day, this can be used to specify the
	 *                                   day-in-month value (e.g. `23` to happen on the `23rd` of the month) or the
	 *                                   position in month (e.g. `2nd` to happen on the `2nd Friday` of the month).
	 * @param string|int $day            If the rule should not apply on the same day, this can be used to specify the
	 *                                   day-of-week the event should occur at; e.g. `1` for Monday, `2` for Tuesday
	 *                                   and
	 *                                   so on; or use the day name, e.g. `$number = 3, $day = 'Fri'` means "On the
	 *                                   third Friday of the month".
	 * @param bool       $same_time      Whether the rule should apply on the same time as the Event or not.
	 * @param array|null $diff_time_data The time data to use when the rule should not apply on the same time as the
	 *                                   Event. The data should have shape like `[ '8am', '9am', 0]` indicating the
	 *                                   start time, end time and the number of days between start and end (`0` is the
	 *                                   same day).
	 *
	 * @return Recurrence The recurrence rules and exclusions in the format used by the `_EventRecurrence`
	 *                             meta field.
	 */
	public function with_monthly_recurrence( int $interval = 1, bool $same_day = true, $number = 1, $day = 'mon', bool $same_time = true, array $diff_time_data = null ): Recurrence {
		if ( $this->delay( __FUNCTION__, func_get_args() ) ) {
			return $this;
		}

		if ( $same_day ) {
			$month = [];
		} else if ( is_numeric( $number ) ) {
			// Something like "On the 3rd of the Month".
			$month = [
				'same-day' => 'no',
				'number'   => $number,
			];
		} else {
			// Something like "On the first Monday of the month".
			$month = [
				'same-day' => 'no',
				'number'   => $number,
				'day'      => $this->day_to_number( $day ),
			];
		}

		if ( $same_time ) {
			$rule = [
				'type'   => 'Custom',
				'custom' =>
					[
						'interval'  => (string) $interval,
						'month'     => $month,
						'same-time' => 'yes',
						'type'      => 'Monthly',
					],
			];
		} else {
			[ $start_time, $end_time, $end_day ] = $this->parse_diff_time_data( $diff_time_data );
			$rule = [
				'type'   => 'Custom',
				'custom' =>
					[
						'interval'   => (string) $interval,
						'month'      => $month,
						'same-time'  => 'no',
						'start-time' => $start_time,
						'end-time'   => $end_time,
						'end-day'    => $end_day,
						'type'       => 'Monthly',
					],
			];
		}

		$this->last_rule = &$rule;
		/** @noinspection UnsupportedStringOffsetOperationsInspection */
		$this->build_rules[ $this->rules_or_exclusion ][] = &$rule;
		$this->with_end( 'after', 10 );

		return $this;
	}


	/**
	 * Starts the construction of a Yearly recurrence rule.
	 *
	 * @since 6.0.1
	 *
	 * @param int          $interval       The number of days between each occurrence.
	 * @param bool         $same_day       Whether the rule should apply on the same numeric day as the Event start
	 *                                     date or not.
	 * @param int|string   $number         If the rule should not apply on the same day, this can be used to specify
	 *                                     the
	 *                                     day-in-month value (e.g. `23` to happen on the `23rd` of the month) or the
	 *                                     position in month (e.g. `2nd` to happen on the `2nd Friday` of the month).
	 * @param string|int   $day            If the rule should not apply on the same day, this can be used to specify
	 *                                     the
	 *                                     day-of-week the event should occur at; e.g. `1` for Monday, `2` for Tuesday
	 *                                     and so on; or use the day name, e.g. `$number = 3, $day = 'Fri'` means "On
	 *                                     the third Friday of the month".
	 * @param string|array $month          The month, or set of months, the event should occur in.
	 * @param bool         $same_time      Whether the rule should apply on the same time as the Event or not.
	 * @param array|null   $diff_time_data The time data to use when the rule should not apply on the same time as the
	 *                                     Event. The data should have shape like `[ '8am', '9am', 0]` indicating the
	 *                                     start time, end time and the number of days between start and end (`0` is
	 *                                     the
	 *                                     same day).
	 *
	 * @return Recurrence For chaining.
	 */
	public function with_yearly_recurrence(
		int $interval = 1,
		bool $same_day = true,
		$number = 1,
		$day = 1,
		$month = null,
		bool $same_time = true,
		array $diff_time_data = null
	): Recurrence {
		if ( $this->delay( __FUNCTION__, func_get_args() ) ) {
			return $this;
		}

		if ( $same_day ) {
			$year = [ 'same-day' => 'yes' ];
		} elseif ( is_numeric( $number ) ) {
			// Something like "On the 22nd of the Month".
			$year = [
				'same-day' => 'no',
				'number'   => $number,
			];
		} else {
			// Something like "On the fourth Sunday of the Month".
			$year = [
				'same-day' => 'no',
				'number'   => $number,
				'day'      => $this->day_to_number( $day )
			];
		}

		if ( ! empty( $month ) ) {
			$year['month'] = is_array( $month ) ? $month : [ $month ];
		} else {
			$year['month'] = [ Dates::immutable( $this->build_rules['EventStartDate'] )->format( 'n' ) ];
		}

		if ( $same_time ) {
			$rule = [
				'type'   => 'Custom',
				'custom' =>
					[
						'interval'  => (string) $interval,
						'year'      => $year,
						'same-time' => 'yes',
						'type'      => 'Yearly',
					],
			];
		} else {
			[ $start_time, $end_time, $end_day ] = $this->parse_diff_time_data( $diff_time_data );
			$rule = [
				'type'   => 'Custom',
				'custom' =>
					[
						'interval'   => (string) $interval,
						'year'       => $year,
						'same-time'  => 'no',
						'start-time' => $start_time,
						'end-time'   => $end_time,
						'end-day'    => $end_day,
						'type'       => 'Yearly',
					],
			];
		}

		$this->last_rule = &$rule;
		/** @noinspection UnsupportedStringOffsetOperationsInspection */
		$this->build_rules[ $this->rules_or_exclusion ][] = &$rule;
		$this->with_end( 'after', 10 );

		return $this;
	}

	/**
	 * Starts the construction of a single Date recurrence rule.
	 *
	 * @since 6.0.1
	 *
	 * @param string|int|DateTimeInterface $date       The date the event should occur on.
	 * @param bool                         $same_time  Whether the rule should apply on the same time as the Event or
	 *                                                 not.
	 * @param string                       $start_time If the rule should not apply on the same time, this can be used
	 *                                                 to specify the start time, e.g. `'8am'`.
	 * @param string                       $end_time   If the rule should not apply on the same time, this can be used
	 *                                                 to specify the end time,  e.g. `'9am'`.
	 * @param int                          $end_day    If the rule should not apply on the same time, this can be used
	 *                                                 to specify the number of days between start and end; `0` means
	 *                                                 the same day.
	 *
	 * @return $this For chaining.
	 */
	public function with_date_recurrence( $date, bool $same_time = true, string $start_time = '8am', string $end_time = '5pm', int $end_day = 0 ): Recurrence {
		if ( $this->delay( __FUNCTION__, func_get_args() ) ) {
			return $this;
		}

		$date_string = Dates::immutable( $date )->format( Dates::DBDATEFORMAT );

		if ( $same_time ) {
			$rule = [
				'type'   => 'Custom',
				'custom' =>
					[
						'interval'  => 1, // RDATEs have the interval set to 1 as int.
						'date'      => [
							'date' => $date_string,
						],
						'same-time' => 'yes',
						'type'      => 'Date',
					],
			];
		} else {
			$rule = [
				'type'   => 'Custom',
				'custom' =>
					[
						'interval'   => '1',
						'date'       => [
							'date' => $date_string,
						],
						'same-time'  => 'no',
						'start-time' => Dates::immutable( $start_time )->format( self::$time_format ),
						'end-time'   => Dates::immutable( $end_time )->format( self::$time_format ),
						'end-day'    => $end_day === 0 ? 'same-day' : (string) $end_day,
						'type'       => 'Date',
					],
			];
		}

		if ( $this->normalize_rules && $this->rules_or_exclusion === 'rules' ) {
			$rule['EventStartDate'] = $this->dtstart->format( Dates::DBDATETIMEFORMAT );
			$rule['EventEndDate'] = $this->dtend->format( Dates::DBDATETIMEFORMAT );
			$rule = Date_Rule::from_event_recurrence_format( $rule )->to_event_recurrence_format();
		}

		$this->last_rule = &$rule;
		/** @noinspection UnsupportedStringOffsetOperationsInspection */
		$this->build_rules[ $this->rules_or_exclusion ][] = &$rule;

		return $this;
	}

	/**
	 * Starts the construction of a Daily exclusion rule.
	 *
	 * @since 6.0.1
	 *
	 * @param int $interval The number of days between each exclusion.
	 *
	 * @return $this For chaining.
	 */
	public function with_daily_exclusion( int $interval = 1 ): Recurrence {
		if ( $this->delay( __FUNCTION__, func_get_args() ) ) {
			return $this;
		}

		$this->rules_or_exclusion = 'exclusions';

		return $this->with_daily_recurrence( $interval );
	}

	/**
	 * Starts the construction of a Weekly exclusion rule.
	 *
	 * @since 6.0.1
	 *
	 * @param int   $interval The number of weeks between each exclusion.
	 * @param array $days     The days of the week to exclude.
	 *
	 * @return $this For chaining.
	 */
	public function with_weekly_exclusion( int $interval = 1, array $days = [ 1 ] ): Recurrence {
		if ( $this->delay( __FUNCTION__, func_get_args() ) ) {
			return $this;
		}

		$this->rules_or_exclusion = 'exclusions';

		return $this->with_weekly_recurrence( $interval, $days );
	}

	/**
	 * Starts the construction of a Monthly exclusion rule.
	 *
	 * @since 6.0.1
	 *
	 * @param int        $interval       The number of months between each exclusion.
	 * @param bool       $same_day       Whether the rule should apply on the same day of the month or not.
	 * @param int|string $number         If the rule should not apply on the same day, this can be used to specify the
	 *                                   day-in-month value (e.g. `23` to happen on the `23rd` of the month) or the
	 *                                   position in month (e.g. `2nd` to happen on the `2nd Friday` of the month).
	 * @param string|int $day            If the rule should not apply on the same day, this can be used to specify the
	 *                                   day-of-week the event should occur at; e.g. `1` for Monday, `2` for Tuesday and
	 *                                   so on; or use the day name, e.g. `$number = 3, $day = 'Fri'` means "On the
	 *                                   third Friday of the month".
	 *
	 * @return $this For chaining.
	 */
	public function with_monthly_exclusion( int $interval = 1, bool $same_day = true, $number = 1, $day = 1 ): Recurrence {
		if ( $this->delay( __FUNCTION__, func_get_args() ) ) {
			return $this;
		}

		$this->rules_or_exclusion = 'exclusions';

		return $this->with_monthly_recurrence( $interval, $same_day, $number, $day );
	}

	/**
	 * Starts the construction of a Yearly exclusion rule.
	 *
	 * @since 6.0.1
	 *
	 * @param int          $interval       The number of days between each occurrence.
	 * @param bool         $same_day       Whether the rule should apply on the same numeric day as the Event start
	 *                                     date or not.
	 * @param int|string   $number         If the rule should not apply on the same day, this can be used to specify
	 *                                     the
	 *                                     day-in-month value (e.g. `23` to happen on the `23rd` of the month) or the
	 *                                     position in month (e.g. `2nd` to happen on the `2nd Friday` of the month).
	 * @param string|int   $day            If the rule should not apply on the same day, this can be used to specify
	 *                                     the
	 *                                     day-of-week the event should occur at; e.g. `1` for Monday, `2` for Tuesday
	 *                                     and so on; or use the day name, e.g. `$number = 3, $day = 'Fri'` means "On
	 *                                     the third Friday of the month".
	 * @param string|array $month          The month, or set of months, the event should occur in.
	 */
	public function with_yearly_exclusion(
		int $interval = 1,
		bool $same_day = true,
		$number = 1,
		$day = 1,
		$month = null
	): Recurrence {
		if ( $this->delay( __FUNCTION__, func_get_args() ) ) {
			return $this;
		}

		$this->rules_or_exclusion = 'exclusions';

		return $this->with_yearly_recurrence( $interval, $same_day, $number, $day, $month );
	}

	/**
	 * Starts the construction of a single Date exclusion rule.
	 *
	 * @since 6.0.1
	 *
	 * @param string|int|DateTimeInterface $date       The date the event should occur on.
	 * @param bool                         $same_time  Whether the rule should apply on the same time as the Event or
	 *                                                 not.
	 * @param string                       $start_time If the rule should not apply on the same time, this can be used
	 *                                                 to specify the start time, e.g. `'8am'`.
	 * @param string                       $end_time   If the rule should not apply on the same time, this can be used
	 *                                                 to specify the end time,  e.g. `'9am'`.
	 * @param int                          $end_day    If the rule should not apply on the same time, this can be used
	 *                                                 to specify the number of days between start and end; `0` means
	 *                                                 the same day.
	 *
	 * @return $this For chaining.
	 */
	public function with_date_exclusion( $date, bool $same_time = true, string $start_time = '8am', string $end_time = '5pm', int $end_day = 0 ): Recurrence {
		if ( $this->delay( __FUNCTION__, func_get_args() ) ) {
			return $this;
		}

		$this->rules_or_exclusion = 'exclusions';

		return $this->with_date_recurrence( $date, $same_time, $start_time, $end_time, $end_day );
	}

	/**
	 * Specifies the start date on an Event recurrence defining it all-day.
	 *
	 * @since 6.0.1
	 * @param string|int|DateTimeInterface $dtstart The start date of the Event.
	 * @param int                          $end_day The number of days between the start date and the end date.
	 *
	 * @return $this  For chaining.
	 *
	 * @throws \Exception If there's an issue building the date interval.
	 */
	public function with_all_day_duration( $dtstart, int $end_day = 0 ): Recurrence {
		$start_date = Dates::immutable( $dtstart );
		$end_date = $start_date->add( new DateInterval( 'P' . $end_day . 'D' ) );
		$start_date_string = tribe_beginning_of_day( $start_date->format( 'Y-m-d' ) );
		$end_date_string = tribe_end_of_day( $end_date->format( 'Y-m-d' ) );

		return $this->with_start_date( $start_date_string )->with_end_date( $end_date_string );
	}

	/**
	 * Parses the content of the different time data.
	 *
	 * @since 6.0.1
	 *
	 * @param array $diff_time_data The different time data.
	 *
	 * @return array The parsed different time data.
	 */
	private function parse_diff_time_data( array $diff_time_data ): array {
		[ $start_time, $end_time, $end_day ] = $diff_time_data;
		$start_time = Dates::immutable( $start_time )->format( 'H:i:s' );
		$end_time = Dates::immutable( $end_time )->format( 'H:i:s' );

		return array( $start_time, $end_time, $end_day );
	}

	/**
	 * Converts a day from the string format to its corresponding number.
	 *
	 * @since 6.0.1
	 * @param string|int $day The day of the week to convert.
	 *
	 * @return int The day of the week in numeric format, `1` is Monday.
	 */
	private function day_to_number( $day ): int {
		if ( is_numeric( $day ) ) {
			return (int) $day;
		}

		// `Friday` to `fri`.
		$day_short_name = strtolower( substr( $day, 0, 3 ) );

		return self::$day_name_to_day_num_map[ $day_short_name ];
	}

	/**
	 * Whether the rules should be normalized during output or not.
	 *
	 * @since 6.0.1
	 *
	 * @param bool $normalize Whether the rules should be normalized during output or not.
	 *
	 * @return $this For chaining.
	 */
	public function normalize_rules( bool $normalize ): Recurrence {
		$this->normalize_rules = $normalize;

		return $this;
	}

	/**
	 * Sets the DTSTART or DTEND property updating the timezone of both, if required.
	 *
	 * @since 6.0.1
	 *
	 * @param string|int|DateTimeInterface $date     The date to set the value of the DTSTART or DTEND property to.
	 * @param string                       $property The name of the property to set.
	 *
	 * @return void
	 */
	private function set_date_property( $date, string $property ): void {
		if ( $date instanceof DateTimeImmutable ) {
			$this->{$property} = $date;
		} else {
			$input = $date instanceof DateTimeInterface ? $date->getTimestamp() : $date;
			$this->{$property} = Dates::immutable( $input );
		}

		if ( ! $this->locked_timezone ) {
			$this->timezone = $date instanceof DateTimeInterface ? $date->getTimezone() : $this->timezone;
		}

		if ( $this->timezone !== null ) {
			$other_property = $property === 'dtstart' ? 'dtend' : 'dtstart';
			$this->{$property} = $this->{$property}->setTimezone( $this->timezone );
			if ( $this->{$other_property} instanceof DateTimeImmutable ) {
				$this->{$other_property} = $this->{$other_property}->setTimezone( $this->timezone );
			}
		}
	}

	/**
	 * Returns a callback that will build the recurrence rules when provided an Event post array
	 * data.
	 *
	 * This method should be used to build the recurrence rules in the context of an ORM create or
	 * update call where the Event start and end dates and times will not be available until creation.
	 *
	 * @since 6.0.1
	 *
	 * @return Closure The callback that will output the recurrence rules in the format used by the
	 *                 `_EventRecurrence` meta field when provided an Event post array data.
	 */
	public function to_postarr_callback(): Closure {
		$callback = function ( array $postarr = null ) use ( &$callback ) {
			if ( ! isset(
				$postarr['meta_input']['_EventStartDate'],
				$postarr['meta_input']['_EventEndDate'],
				$postarr['meta_input']['_EventTimezone']
			) ) {
				// We're still missing the information to resolve, return this callback.
				return $callback;
			}

			$start_date = $postarr['meta_input']['_EventStartDate'];
			$end_date = $postarr['meta_input']['_EventEndDate'];
			$timezone = $postarr['meta_input']['_EventTimezone'];

			$this->with_start_date( $start_date )
				->with_end_date( $end_date )
				->with_timezone( $timezone );

			return $this->to_event_recurrence_format()['recurrence'];
		};

		return $callback;
	}

	/**
	 * Whether the application of a method should be delayed or not depending on the required
	 * DTSTART and DTEND information being available or not.
	 *
	 * @since 6.0.1
	 *
	 * @param string $method The name of the method.
	 * @param array  $args   The arguments of the method.
	 *
	 * @return bool Whether the application of the method should be delayed or not.
	 */
	private function delay( string $method, array $args ): bool {
		if ( $this->dtstart instanceof DateTimeImmutable && $this->dtend instanceof DateTimeImmutable ) {
			return false;
		}

		$this->delayed[] = function () use ( $args, $method ): void {
			$this->{$method}( ...$args );
		};

		return true;
	}

	/**
	 * Applies the delayed methods, if any.
	 *
	 * @since 6.0.1
	 *
	 * @return void The delayed methods are applied.
	 */
	private function apply_delayed_methods(): void {
		foreach ( $this->delayed as $delayed_method ) {
			$delayed_method();
		}
	}


	/**
	 * Builds an instance of the class from an existing Event post.
	 *
	 * @since 6.0.1
	 *
	 * @param int $post_id The ID of the Event post to build the instance from.
	 *
	 * @return self The instance of the class built from the Event post.
	 */
	public static function from_event( int $post_id ): self {
		$instance = new self();
		$start_date = get_post_meta( $post_id, '_EventStartDate', true );
		$end_date = get_post_meta( $post_id, '_EventEndDate', true );
		$timezone = get_post_meta( $post_id, '_EventTimezone', true );

		if ( empty( $start_date ) || empty( $end_date ) || empty( $timezone ) ) {
			throw new \RuntimeException( 'Missing event data.' );
		}

		$instance->with_start_date( $start_date );
		$instance->with_end_date( $end_date );
		$instance->with_timezone( $timezone );

		$recurrence = get_post_meta( $post_id, '_EventRecurrence', true );
		if ( ! empty( $recurrence ) ) {
			$instance->build_rules = array_merge( $instance->build_rules, $recurrence );
		}

		return $instance;
	}

	/**
	 * Returns the recurrence or exclusions rules in the format used by the Blocks Editor.
	 *
	 * @since 6.0.2
	 *
	 * @param string $key The key of the rule to return, either `rules` or `exclusions`.
	 *
	 * @return array<array<string,mixed>> The recurrence or exclusions rules in the format used by the Blocks Editor.
	 */
	public function to_blocks_format( string $key = 'rules' ): array {
		$event_recurrence_format = $this->to_event_recurrence_format()['recurrence'];

		$convert_to_blocks_format = static function ( array $rule ): array {
			$converter = new Converter( $rule );
			$converter->parse();

			return $converter->get_parsed();
		};

		return $key === 'rules' ?
			array_map( $convert_to_blocks_format, $event_recurrence_format['rules'] )
			: array_map( $convert_to_blocks_format, $event_recurrence_format['exclusions'] );
	}
}
