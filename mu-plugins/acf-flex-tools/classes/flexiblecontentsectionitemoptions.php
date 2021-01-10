<?php

/**
 * Class FlexibleContentSectionItemOptions holds the callback function and has padding flag used in determining
 * where and when to add padding to a FlexibleContentSectionItem
 */
class FlexibleContentSectionItemOptions {

	/**
	 * @var null
	 */
	protected $func = null;
	/**
	 * @var bool
	 */
	protected $has_padding = false;

    /**
     * @var callable|null
     */
	protected $padding_filter = null;

	/**
	 * FlexibleContentSectionItemOptions constructor.
	 *
	 * @param callback $func
	 * @param bool $has_padding
     * @param callback $padding_filter
	 */
	public function __construct($func, $has_padding, $padding_filter) {
		$this->setFunc($func);
		$this->setHasPadding($has_padding);
		$this->setPaddingFilter($padding_filter);
	}

	/**
	 * @return null
	 */
	public function getFunc() {
		return $this->func;
	}

	/**
	 * @param null $func
	 */
	public function setFunc( $func ) {
		$this->func = $func;
	}

	public function getPaddingFilter() {
	    return $this->padding_filter;
    }

    /**
     * @param $padding_filter
     */
	public function setPaddingFilter ($padding_filter) {
	    $this->padding_filter = $padding_filter;
    }

	/**
	 * @return boolean
	 */
	public function getHasPadding() {
		return $this->has_padding;
	}

	/**
	 * @param boolean $has_padding
	 */
	public function setHasPadding( $has_padding ) {
		$this->has_padding = $has_padding;
	}
}
