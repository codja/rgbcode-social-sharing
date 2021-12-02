<?php
/*
Plugin Name: Rgbcode Social Sharing
Plugin URI: https://rgbcode.com/
Description: Plugin for creating share buttons in social networks
Version: 1.0
Author: RGBCODE
Author URI: https://rgbcode.com/
Licence: GPLv2 or later
Text Domain: rgbcode-social-sharing
*/

namespace Rgbcode;

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

define( 'RGBCSS_PATH', plugin_dir_path( __FILE__ ) );
define( 'RGBCSS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

add_action( 'plugins_loaded', array( __NAMESPACE__ . '\\Rgbcode_Social_Sharing', 'init' ) );

class Rgbcode_Social_Sharing {

	protected static $instance;

	public static function init() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	const PROVIDERS = [
		'facebook',
		'twitter',
		'linkedin',
		'whatsapp',
	];

	public function __construct() {
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue' ] );
		add_action( 'init', [ $this, 'register_shortcode' ] );
	}

	public function enqueue() {
		wp_enqueue_script(
			'rgbc_sharing_script',
			plugins_url( '/assets/js/rgbcode-social-sharing.min.js', __FILE__ ),
			array(),
			filemtime( RGBCSS_PATH . 'assets/js/rgbcode-social-sharing.min.js' ),
			true
		);
		wp_enqueue_style(
			'rgbc_sharing_styles',
			plugins_url( '/assets/css/rgbcode-social-sharing.css', __FILE__ ),
			array(),
			filemtime( RGBCSS_PATH . 'assets/css/rgbcode-social-sharing.css' )
		);
	}

	public function register_shortcode() {
		add_shortcode( 'rgbc_add_sharing', [ $this, 'rgbc_sharing_shortcode' ] );
	}

	public function rgbc_sharing_shortcode( $atts = array() ): ?string {
		$provider = $atts['provider'] ?? '';
		$classes  = $atts['classes'] ?? '';
		$text     = $atts['text'] ?? '';

		return $this->get_rgbc_add_sharing( $provider, $classes, $text );
	}

	public function rgbc_add_sharing( string $provider, string $classes = '', string $text_whatsapp = '' ) {
		echo wp_kses_post( $this->get_rgbc_add_sharing( $provider, $classes, $text_whatsapp ) );
	}

	public function get_rgbc_add_sharing( string $provider, string $classes = '', string $text_whatsapp = '' ): ?string {
		if ( ! $provider || ! in_array( $provider, self::PROVIDERS, true ) ) {
			return null;
		}

		switch ( $provider ) {
			default:
			case 'facebook':
				$url    = 'https://www.facebook.com/sharer.php?u=[page_url]';
				$result = $this->get_base_icon( $provider, $classes, $url );
				break;
			case 'twitter':
				$url    = 'https://twitter.com/intent/tweet?text=' . get_the_title() . ' [page_url]';
				$result = $this->get_base_icon( $provider, $classes, $url );
				break;
			case 'linkedin':
				$url    = 'https://www.linkedin.com/shareArticle?mini=true&url=[page_url]';
				$result = $this->get_base_icon( $provider, $classes, $url );
				break;
			case 'whatsapp':
				$prepare_text = str_replace( ' ', '+', $text_whatsapp );
				$img          = $this->get_img( $provider );
				$result       = sprintf(
					'<a class="%s" href="%s" target="_blank">%s</a>',
					esc_attr( $classes ),
					esc_url( "https://api.whatsapp.com/send/?text={$prepare_text}" ),
					$img
				);
				break;
		}
		return $result;
	}

	private function get_base_icon( $provider, $classes, $url ): string {
		$img = $this->get_img( $provider );
		return sprintf(
			'<span class="rgbcss %s" data-url="%s">%s</span>',
			esc_attr( $classes ),
			esc_attr( $url ),
			$img
		);
	}

	private function get_img( $provider ): string {
		$icon_path = apply_filters( "rgbc_sharing_{$provider}_icon", RGBCSS_PLUGIN_URL . "img/{$provider}.svg" );
		return sprintf(
			'<img src="%s" alt="%s"/>',
			esc_url( $icon_path ),
			esc_attr( $provider )
		);
	}

}
