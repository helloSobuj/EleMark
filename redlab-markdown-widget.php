<?php
/**
 * Plugin Name: RedLab Markdown Widget
 * Plugin URI:  https://redboltit.com
 * Description: Adds a Markdown Editor widget to Elementor with live preview and full CommonMark support.
 * Version:     1.0.0
 * Author:      RedBolt IT
 * License:     GPL-2.0-or-later
 * Text Domain: redlab-markdown-widget
 *
 * Requires at least: 5.9
 * Requires PHP:      7.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'REDLAB_MW_VERSION', '1.0.0' );
define( 'REDLAB_MW_PATH', plugin_dir_path( __FILE__ ) );
define( 'REDLAB_MW_URL', plugin_dir_url( __FILE__ ) );
define( 'REDLAB_MW_MIN_ELEMENTOR', '3.0.0' );

/**
 * Main plugin class — singleton to avoid duplicate loading.
 */
final class RedLab_Markdown_Widget {

	private static ?self $instance = null;

	public static function instance(): self {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'plugins_loaded', [ $this, 'init' ] );
	}

	public function init(): void {
		if ( ! $this->check_elementor() ) {
			return;
		}

		add_action( 'elementor/elements/categories_registered', [ $this, 'register_category' ] );
		add_action( 'elementor/widgets/register', [ $this, 'register_widget' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_frontend_assets' ] );
		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), [ $this, 'plugin_action_links' ] );
	}

	// -------------------------------------------------------------------------
	// Elementor presence check
	// -------------------------------------------------------------------------

	private function check_elementor(): bool {
		if ( ! did_action( 'elementor/loaded' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_missing_elementor' ] );
			return false;
		}

		if ( ! version_compare( ELEMENTOR_VERSION, REDLAB_MW_MIN_ELEMENTOR, '>=' ) ) {
			add_action( 'admin_notices', [ $this, 'admin_notice_old_elementor' ] );
			return false;
		}

		return true;
	}

	public function admin_notice_missing_elementor(): void {
		$message = sprintf(
			/* translators: 1: Plugin name, 2: Elementor */
			esc_html__( '"%1$s" requires "%2$s" to be installed and activated.', 'redlab-markdown-widget' ),
			'<strong>RedLab Markdown Widget</strong>',
			'<strong>Elementor</strong>'
		);
		printf( '<div class="notice notice-warning is-dismissible"><p>%s</p></div>', $message );
	}

	public function admin_notice_old_elementor(): void {
		$message = sprintf(
			/* translators: 1: Plugin name, 2: Elementor, 3: Required version */
			esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'redlab-markdown-widget' ),
			'<strong>RedLab Markdown Widget</strong>',
			'<strong>Elementor</strong>',
			REDLAB_MW_MIN_ELEMENTOR
		);
		printf( '<div class="notice notice-warning is-dismissible"><p>%s</p></div>', $message );
	}

	// -------------------------------------------------------------------------
	// Widget category & registration
	// -------------------------------------------------------------------------

	public function register_category( \Elementor\Elements_Manager $manager ): void {
		$manager->add_category( 'redlab-widgets', [
			'title' => esc_html__( 'RedLab Widgets', 'redlab-markdown-widget' ),
			'icon'  => 'fa fa-plug',
		] );
	}

	public function register_widget( \Elementor\Widgets_Manager $manager ): void {
		require_once REDLAB_MW_PATH . 'includes/class-markdown-widget.php';
		$manager->register( new RedLab_Markdown_Editor_Widget() );
	}

	// -------------------------------------------------------------------------
	// Frontend assets
	// -------------------------------------------------------------------------

	public function enqueue_frontend_assets(): void {
		// Only load on pages that actually use the widget.
		if ( ! $this->page_uses_widget() ) {
			return;
		}

		// marked.js — bundled copy, CDN fallback.
		$marked_local = REDLAB_MW_PATH . 'assets/js/marked.min.js';
		if ( file_exists( $marked_local ) ) {
			wp_enqueue_script(
				'marked-js',
				REDLAB_MW_URL . 'assets/js/marked.min.js',
				[],
				'15.0.12',
				true
			);
		} else {
			wp_enqueue_script(
				'marked-js',
				'https://cdn.jsdelivr.net/npm/marked@15.0.12/marked.min.js',
				[],
				'15.0.12',
				true
			);
		}

		wp_enqueue_script(
			'redlab-markdown-render',
			REDLAB_MW_URL . 'assets/js/markdown-render.js',
			[ 'marked-js' ],
			REDLAB_MW_VERSION,
			true
		);

		wp_enqueue_style(
			'redlab-markdown-style',
			REDLAB_MW_URL . 'assets/css/markdown-style.css',
			[],
			REDLAB_MW_VERSION
		);
	}

	/**
	 * Returns true when the current page/post contains the widget, preventing
	 * unnecessary asset loading on every page.
	 */
	private function page_uses_widget(): bool {
		// Always load in Elementor preview iframe.
		if ( isset( $_GET['elementor-preview'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			return true;
		}

		$post_id = get_the_ID();
		if ( ! $post_id ) {
			return false;
		}

		// Check Elementor meta for widget presence.
		$data = get_post_meta( $post_id, '_elementor_data', true );
		if ( $data && is_string( $data ) && strpos( $data, 'redlab_markdown_editor' ) !== false ) {
			return true;
		}

		return false;
	}

	// -------------------------------------------------------------------------
	// Plugin action links
	// -------------------------------------------------------------------------

	public function plugin_action_links( array $links ): array {
		$settings_link = sprintf(
			'<a href="%s">%s</a>',
			esc_url( admin_url( 'admin.php?page=elementor' ) ),
			esc_html__( 'Open Elementor', 'redlab-markdown-widget' )
		);
		array_unshift( $links, $settings_link );
		return $links;
	}
}

RedLab_Markdown_Widget::instance();
