<?php

namespace Myrotvorets\WordPress\Classifier;

use WildWolf\Utils\Singleton;

final class Admin {
	const OPTIONS_MENU_SLUG = 'psb-classifier';
	const OPTION_GROUP      = 'general-settings';
	const OPTION_KEY        = 'psb-classifier';

	use Singleton;

	private function __construct() {
		$this->init();
	}

	private function init(): void {
		load_plugin_textdomain( 'classifier', false, plugin_basename( dirname( __DIR__ ) ) . '/lang/' );

		add_action( 'admin_menu', [ $this, 'admin_menu' ] );
		add_action( 'admin_init', [ $this, 'admin_init' ] );
	}

	public function admin_menu(): void {
		add_options_page(
			_x( 'Crime Classifier', 'Page title', 'classifier' ),
			_x( 'Crime Classifier', 'Menu title', 'classifier' ),
			'manage_options',
			self::OPTIONS_MENU_SLUG,
			[ $this, 'options_page' ]
		);
	}

	public function admin_init(): void {
		add_action( 'admin_enqueue_scripts', [ $this, 'admin_enqueue_scripts' ] );

		register_setting(
			self::OPTION_GROUP,
			self::OPTION_KEY,
			[
				'type'              => 'string',
				'sanitize_callback' => [ __CLASS__, 'sanitize_options' ],
				'default'           => '',
			]
		);

		$section = 'general-settings';
		add_settings_section(
			$section,
			'',
			'__return_empty_string',
			self::OPTIONS_MENU_SLUG
		);

		add_settings_field(
			self::OPTION_KEY,
			_x( 'Crime Classifier', 'Label for text area in plugin settings', 'classifier' ),
			[ __CLASS__, 'textarea' ],
			self::OPTIONS_MENU_SLUG,
			$section,
			[
				'label_for' => self::OPTION_KEY,
			]
		);
	}

	public function options_page(): void {
		require __DIR__ . '/../views/options.php'; // NOSONAR
	}

	public function admin_enqueue_scripts(): void {
		global $post_type;
		if ( isset( $post_type ) && 'criminal' === $post_type ) {
			$classifier = (string) get_option( self::OPTION_KEY );
			if ( $classifier ) {
				$quicktags = sprintf(
					'const classifier = "%1$s"; QTags.addButton( "psb-classifier", "%2$s", classifier, "", "", "%2$s" );',
					esc_js( $classifier ),
					esc_js( _x( 'Classifier', 'Button label', 'classifier' ) ),
				);

				wp_add_inline_script( 'quicktags', $quicktags, 'after' );
			}
		}
	}

	/**
	 * @param mixed $value
	 * @return string
	 */
	public static function sanitize_options( $value ): string {
		$value = trim( (string) $value );

		$lines = explode( "\n", $value );
		$lines = array_map(
			fn ( $s ) => trim( preg_replace( '/\s+/u', ' ', $s ) ),
			$lines
		);

		return join( "\n", $lines );
	}

	/**
	 * @psalm-param array{label_for: string} $args
	 */
	public static function textarea( array $args ): void {
		printf(
			'<textarea name="%s" id="%s" rows="%d" cols="%d" style="width: 100%%">%s</textarea>',
			esc_attr( self::OPTION_KEY ),
			esc_attr( $args['label_for'] ),
			20,
			120,
			esc_textarea( (string) get_option( self::OPTION_KEY, '' ) ),
		);
	}
}
