<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

abstract class WOE_Formatter {
	var $has_output_filter;
	var $mode;
	var $settings;
	var $labels;
	var $handle;
	var $format;

	public function __construct( $mode, $filename, $settings, $format, $labels ) {
		$this->has_output_filter = has_filter( "woe_{$format}_output_filter" );
		$this->mode              = $mode;
		$this->settings          = $settings;
		$this->labels            = $labels;
		$this->handle            = fopen( $filename, 'a' );
		if ( ! $this->handle ) {
			throw new Exception( $filename . __( 'can not open for output', 'woo-order-export-lite' ) );
		}
		$this->format            = $format;
	}

	public function start( $data = '' ) {
		do_action("woe_formatter_start", $data);
		do_action("woe_formatter_" .$this->format. "_start", $data);
	}

	public function output( $rec ) {
		$this->handle = apply_filters( "woe_formatter_set_handler_for_" . $this->format . "_row", $this->handle );
	}

	public function finish() {
		fclose( $this->handle );
		do_action("woe_formatter_finish", $this);
		do_action("woe_formatter_" .$this->format. "_finished", $this);
	}
	
	public function finish_partial() {
		// child must fully implement this method
		fclose( $this->handle );
		do_action("woe_formatter_finish_partial", $this);
		do_action("woe_formatter_" .$this->format. "_finished_partially", $this);
	}

	public function truncate() {
		ftruncate( $this->handle, 0 );
	}

	protected function convert_literals( $s ) {
		$s = str_replace( '\r', "\r", $s );
		$s = str_replace( '\t', "\t", $s );
		$s = str_replace( '\n', "\n", $s );

		return $s;
	}
}