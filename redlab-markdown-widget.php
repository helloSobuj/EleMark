<?php
/**
 * Plugin Name: RedLab Markdown Widget
 * Plugin URI:  https://redboltit.com
 * Description: Adds a Markdown Editor widget to Elementor with live preview, syntax highlighting, themes, and rich content features.
 * Version:     1.1.0
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

define( 'REDLAB_MW_VERSION', '1.1.0' );
define( 'REDLAB_MW_PATH', plugin_dir_path( __FILE__ ) );
define( 'REDLAB_MW_URL', plugin_dir_url( __FILE__ ) );
define( 'REDLAB_MW_MIN_ELEMENTOR', '3.0.0' );

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
		// Priority 5 = register before enqueueing (priority 10).
		add_action( 'wp_enqueue_scripts', [ $this, 'register_assets' ], 5 );
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_frontend_assets' ], 10 );
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
			esc_html__( '"%1$s" requires "%2$s" to be installed and activated.', 'redlab-markdown-widget' ),
			'<strong>RedLab Markdown Widget</strong>',
			'<strong>Elementor</strong>'
		);
		printf( '<div class="notice notice-warning is-dismissible"><p>%s</p></div>', $message );
	}

	public function admin_notice_old_elementor(): void {
		$message = sprintf(
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
	// Asset registration (priority 5 — runs before enqueue at priority 10)
	// Registering separately lets get_script_depends() / get_style_depends()
	// reference handles that are already known to WordPress.
	// -------------------------------------------------------------------------

	public function register_assets(): void {
		// --- marked.js -------------------------------------------------------
		$marked_local = REDLAB_MW_PATH . 'assets/js/marked.min.js';
		if ( file_exists( $marked_local ) ) {
			wp_register_script( 'marked-js', REDLAB_MW_URL . 'assets/js/marked.min.js', [], '15.0.12', true );
		} else {
			wp_register_script( 'marked-js', 'https://cdn.jsdelivr.net/npm/marked@15.0.12/marked.min.js', [], '15.0.12', true );
		}

		// --- Prism core + language bundle ------------------------------------
		$prism_local = REDLAB_MW_PATH . 'assets/js/prism.min.js';
		if ( file_exists( $prism_local ) ) {
			wp_register_script( 'prism-js', REDLAB_MW_URL . 'assets/js/prism.min.js', [], '1.29.0', true );
		} else {
			// jsDelivr combine: core + all required language components in one request.
			wp_register_script( 'prism-js',
				'https://cdn.jsdelivr.net/combine/'
				. 'npm/prismjs@1.29.0/prism.min.js,'
				. 'npm/prismjs@1.29.0/components/prism-markup-templating.min.js,'
				. 'npm/prismjs@1.29.0/components/prism-php.min.js,'
				. 'npm/prismjs@1.29.0/components/prism-python.min.js,'
				. 'npm/prismjs@1.29.0/components/prism-bash.min.js,'
				. 'npm/prismjs@1.29.0/components/prism-sql.min.js,'
				. 'npm/prismjs@1.29.0/components/prism-json.min.js,'
				. 'npm/prismjs@1.29.0/components/prism-typescript.min.js,'
				. 'npm/prismjs@1.29.0/components/prism-jsx.min.js,'
				. 'npm/prismjs@1.29.0/components/prism-yaml.min.js,'
				. 'npm/prismjs@1.29.0/components/prism-markdown.min.js,'
				. 'npm/prismjs@1.29.0/components/prism-go.min.js,'
				. 'npm/prismjs@1.29.0/components/prism-rust.min.js,'
				. 'npm/prismjs@1.29.0/components/prism-java.min.js,'
				. 'npm/prismjs@1.29.0/components/prism-c.min.js,'
				. 'npm/prismjs@1.29.0/components/prism-cpp.min.js',
				[], '1.29.0', true
			);
		}

		// --- Prism line-numbers plugin ----------------------------------------
		$ln_local = REDLAB_MW_PATH . 'assets/js/prism-line-numbers.min.js';
		if ( file_exists( $ln_local ) ) {
			wp_register_script( 'prism-line-numbers-js', REDLAB_MW_URL . 'assets/js/prism-line-numbers.min.js', [ 'prism-js' ], '1.29.0', true );
		} else {
			wp_register_script( 'prism-line-numbers-js',
				'https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/plugins/line-numbers/prism-line-numbers.min.js',
				[ 'prism-js' ], '1.29.0', true
			);
		}

		wp_register_style( 'prism-line-numbers-css',
			'https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/plugins/line-numbers/prism-line-numbers.min.css',
			[], '1.29.0'
		);

		// --- Prism highlight themes ------------------------------------------
		foreach ( $this->prism_theme_urls() as $key => $url ) {
			wp_register_style( "prism-theme-{$key}", $url, [], '1.29.0' );
		}

		// --- Our render script -----------------------------------------------
		// Load order: marked → prism → prism-line-numbers → our script.
		wp_register_script(
			'redlab-markdown-render',
			REDLAB_MW_URL . 'assets/js/markdown-render.js',
			[ 'marked-js', 'prism-js', 'prism-line-numbers-js' ],
			REDLAB_MW_VERSION,
			true
		);

		// --- Our stylesheet (loads after prism-line-numbers-css) -------------
		wp_register_style(
			'redlab-markdown-style',
			REDLAB_MW_URL . 'assets/css/markdown-style.css',
			[ 'prism-line-numbers-css' ],
			REDLAB_MW_VERSION
		);
	}

	// -------------------------------------------------------------------------
	// Frontend asset enqueueing
	// -------------------------------------------------------------------------

	public function enqueue_frontend_assets(): void {
		if ( ! $this->page_uses_widget() ) {
			return;
		}

		wp_enqueue_script( 'marked-js' );
		wp_enqueue_script( 'prism-js' );
		wp_enqueue_script( 'prism-line-numbers-js' );
		wp_enqueue_style( 'prism-line-numbers-css' );

		// Enqueue only the highlight theme chosen in widget settings.
		$theme = $this->get_page_widget_setting( 'code_highlight_theme', 'tomorrow' );
		if ( ! array_key_exists( $theme, $this->prism_theme_urls() ) ) {
			$theme = 'tomorrow';
		}
		wp_enqueue_style( "prism-theme-{$theme}" );

		wp_enqueue_script( 'redlab-markdown-render' );
		// markdown-style.css must load LAST so our rules override Prism defaults.
		wp_enqueue_style( 'redlab-markdown-style' );
	}

	// -------------------------------------------------------------------------
	// Helpers
	// -------------------------------------------------------------------------

	/** Map of highlight theme slug → CDN URL. */
	private function prism_theme_urls(): array {
		return [
			'tomorrow'  => 'https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism-tomorrow.min.css',
			'okaidia'   => 'https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism-okaidia.min.css',
			'solarized' => 'https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism-solarizedlight.min.css',
			'ghcolors'  => 'https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism.min.css',
			'vsc-dark'  => 'https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism-vsc-dark-plus.min.css',
		];
	}

	/**
	 * Scan the page's Elementor data JSON for a specific widget setting value.
	 * Uses a fast regex instead of full JSON decode.
	 */
	private function get_page_widget_setting( string $key, string $default = '' ): string {
		// In the Elementor preview iframe, settings aren't saved yet — use default.
		if ( isset( $_GET['elementor-preview'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			return $default;
		}

		$post_id = get_the_ID();
		if ( ! $post_id ) {
			return $default;
		}

		$data = get_post_meta( $post_id, '_elementor_data', true );
		if ( ! $data || ! is_string( $data ) ) {
			return $default;
		}

		if ( preg_match( '/"' . preg_quote( $key, '/' ) . '"\s*:\s*"([^"]*)"/', $data, $matches ) ) {
			return sanitize_key( $matches[1] );
		}

		return $default;
	}

	/**
	 * Returns true when the current page uses the widget so assets only load
	 * on pages that actually need them.
	 */
	private function page_uses_widget(): bool {
		if ( isset( $_GET['elementor-preview'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			return true;
		}

		$post_id = get_the_ID();
		if ( ! $post_id ) {
			return false;
		}

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
