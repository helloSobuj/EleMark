<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Elementor widget: Markdown Editor (v1.1.0)
 */
class RedLab_Markdown_Editor_Widget extends \Elementor\Widget_Base {

	public function get_name(): string {
		return 'redlab_markdown_editor';
	}

	public function get_title(): string {
		return esc_html__( 'Markdown Editor', 'redlab-markdown-widget' );
	}

	public function get_icon(): string {
		return 'eicon-text-editor';
	}

	public function get_categories(): array {
		return [ 'redlab-widgets' ];
	}

	public function get_keywords(): array {
		return [ 'markdown', 'md', 'text', 'editor', 'code' ];
	}

	public function get_script_depends(): array {
		return [ 'marked-js', 'prism-js', 'prism-line-numbers-js', 'redlab-markdown-render' ];
	}

	public function get_style_depends(): array {
		// Register all Prism theme handles so the Elementor editor always has them.
		return [
			'prism-theme-tomorrow',
			'prism-line-numbers-css',
			'redlab-markdown-style',
		];
	}

	// -------------------------------------------------------------------------
	// Controls
	// -------------------------------------------------------------------------

	protected function register_controls(): void {
		$this->register_content_controls();
		$this->register_style_controls();
	}

	private function register_content_controls(): void {

		// --- Markdown Content ------------------------------------------------
		$this->start_controls_section( 'section_content', [
			'label' => esc_html__( 'Markdown Content', 'redlab-markdown-widget' ),
			'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
		] );

		$this->add_control( 'markdown_text', [
			'label'       => esc_html__( 'Write Markdown Here', 'redlab-markdown-widget' ),
			'description' => esc_html__( 'Supports full CommonMark syntax', 'redlab-markdown-widget' ),
			'type'        => \Elementor\Controls_Manager::CODE,
			'language'    => 'markdown',
			'rows'        => 25,
			'default'     => $this->get_demo_markdown(),
		] );

		$this->add_control( 'enable_live_preview', [
			'label'        => esc_html__( 'Live Preview in Editor', 'redlab-markdown-widget' ),
			'type'         => \Elementor\Controls_Manager::SWITCHER,
			'label_on'     => esc_html__( 'On', 'redlab-markdown-widget' ),
			'label_off'    => esc_html__( 'Off', 'redlab-markdown-widget' ),
			'return_value' => 'yes',
			'default'      => 'yes',
		] );

		$this->end_controls_section();

		// --- Display Options -------------------------------------------------
		$this->start_controls_section( 'section_display_options', [
			'label' => esc_html__( 'Display Options', 'redlab-markdown-widget' ),
			'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
		] );

		$this->add_control( 'show_line_numbers', [
			'label'        => esc_html__( 'Show Line Numbers', 'redlab-markdown-widget' ),
			'description'  => esc_html__( 'Display line numbers in code blocks.', 'redlab-markdown-widget' ),
			'type'         => \Elementor\Controls_Manager::SWITCHER,
			'label_on'     => esc_html__( 'On', 'redlab-markdown-widget' ),
			'label_off'    => esc_html__( 'Off', 'redlab-markdown-widget' ),
			'return_value' => 'yes',
			'default'      => 'no',
		] );

		$this->add_control( 'show_reading_info', [
			'label'        => esc_html__( 'Show Reading Time & Word Count', 'redlab-markdown-widget' ),
			'type'         => \Elementor\Controls_Manager::SWITCHER,
			'label_on'     => esc_html__( 'On', 'redlab-markdown-widget' ),
			'label_off'    => esc_html__( 'Off', 'redlab-markdown-widget' ),
			'return_value' => 'yes',
			'default'      => 'no',
		] );

		$this->add_control( 'reading_info_position', [
			'label'     => esc_html__( 'Badge Position', 'redlab-markdown-widget' ),
			'type'      => \Elementor\Controls_Manager::SELECT,
			'default'   => 'top',
			'options'   => [
				'top'    => esc_html__( 'Above Content', 'redlab-markdown-widget' ),
				'bottom' => esc_html__( 'Below Content', 'redlab-markdown-widget' ),
			],
			'condition' => [ 'show_reading_info' => 'yes' ],
		] );

		$this->add_control( 'reading_speed', [
			'label'       => esc_html__( 'Reading Speed (words/min)', 'redlab-markdown-widget' ),
			'description' => esc_html__( 'Average adult reading speed is 200 wpm.', 'redlab-markdown-widget' ),
			'type'        => \Elementor\Controls_Manager::NUMBER,
			'default'     => 200,
			'min'         => 100,
			'max'         => 400,
			'condition'   => [ 'show_reading_info' => 'yes' ],
		] );

		$this->end_controls_section();
	}

	private function register_style_controls(): void {

		// --- Content Theme (FIRST in Style tab) ------------------------------
		$this->start_controls_section( 'section_style_theme', [
			'label' => esc_html__( 'Content Theme', 'redlab-markdown-widget' ),
			'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
		] );

		$this->add_control( 'content_theme', [
			'label'   => esc_html__( 'Theme Preset', 'redlab-markdown-widget' ),
			'type'    => \Elementor\Controls_Manager::SELECT,
			'default' => 'default',
			'options' => [
				'default' => esc_html__( 'Default (Custom)', 'redlab-markdown-widget' ),
				'github'  => esc_html__( 'GitHub Light', 'redlab-markdown-widget' ),
				'notion'  => esc_html__( 'Notion Style', 'redlab-markdown-widget' ),
				'medium'  => esc_html__( 'Medium Article', 'redlab-markdown-widget' ),
				'dark'    => esc_html__( 'Dark Mode', 'redlab-markdown-widget' ),
				'minimal' => esc_html__( 'Minimal Clean', 'redlab-markdown-widget' ),
			],
		] );

		$this->end_controls_section();

		// --- Code Block ------------------------------------------------------
		$this->start_controls_section( 'section_style_code', [
			'label' => esc_html__( 'Code Block', 'redlab-markdown-widget' ),
			'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
		] );

		$this->add_control( 'code_highlight_theme', [
			'label'   => esc_html__( 'Highlight Theme', 'redlab-markdown-widget' ),
			'type'    => \Elementor\Controls_Manager::SELECT,
			'default' => 'tomorrow',
			'options' => [
				'tomorrow'  => esc_html__( 'Tomorrow Night (dark)', 'redlab-markdown-widget' ),
				'okaidia'   => esc_html__( 'Okaidia (dark orange)', 'redlab-markdown-widget' ),
				'solarized' => esc_html__( 'Solarized Light', 'redlab-markdown-widget' ),
				'ghcolors'  => esc_html__( 'GitHub Style (light)', 'redlab-markdown-widget' ),
				'vsc-dark'  => esc_html__( 'VS Code Dark', 'redlab-markdown-widget' ),
			],
		] );

		$this->add_control( 'code_bg', [
			'label'     => esc_html__( 'Code Block Background', 'redlab-markdown-widget' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '#1e1e1e',
			'selectors' => [ '{{WRAPPER}} .redlab-markdown-output pre' => 'background-color: {{VALUE}} !important;' ],
		] );

		$this->add_control( 'code_color', [
			'label'     => esc_html__( 'Code Text Color', 'redlab-markdown-widget' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '#d4d4d4',
			'selectors' => [
				'{{WRAPPER}} .redlab-markdown-output pre code' => 'color: {{VALUE}};',
				'{{WRAPPER}} .redlab-markdown-output pre'      => 'color: {{VALUE}};',
			],
		] );

		$this->add_control( 'code_font_size', [
			'label'      => esc_html__( 'Code Font Size', 'redlab-markdown-widget' ),
			'type'       => \Elementor\Controls_Manager::SLIDER,
			'size_units' => [ 'px', 'em' ],
			'range'      => [ 'px' => [ 'min' => 10, 'max' => 20 ] ],
			'default'    => [ 'unit' => 'px', 'size' => 14 ],
			'selectors'  => [
				'{{WRAPPER}} .redlab-markdown-output code' => 'font-size: {{SIZE}}{{UNIT}};',
				'{{WRAPPER}} .redlab-markdown-output pre'  => 'font-size: {{SIZE}}{{UNIT}};',
			],
		] );

		$this->end_controls_section();

		// --- Container -------------------------------------------------------
		$this->start_controls_section( 'section_style_container', [
			'label' => esc_html__( 'Container', 'redlab-markdown-widget' ),
			'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
		] );

		$this->add_control( 'container_bg', [
			'label'     => esc_html__( 'Background Color', 'redlab-markdown-widget' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}} .redlab-markdown-output' => 'background-color: {{VALUE}};' ],
		] );

		$this->add_responsive_control( 'container_padding', [
			'label'      => esc_html__( 'Padding', 'redlab-markdown-widget' ),
			'type'       => \Elementor\Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', 'em', '%' ],
			'selectors'  => [ '{{WRAPPER}} .redlab-markdown-output' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
		] );

		$this->add_responsive_control( 'container_margin', [
			'label'      => esc_html__( 'Margin', 'redlab-markdown-widget' ),
			'type'       => \Elementor\Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', 'em', '%' ],
			'selectors'  => [ '{{WRAPPER}} .redlab-markdown-output' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
		] );

		$this->end_controls_section();

		// --- Typography ------------------------------------------------------
		$this->start_controls_section( 'section_style_typography', [
			'label' => esc_html__( 'Typography', 'redlab-markdown-widget' ),
			'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
		] );

		$this->add_control( 'text_color', [
			'label'     => esc_html__( 'Text Color', 'redlab-markdown-widget' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'selectors' => [ '{{WRAPPER}} .redlab-markdown-output' => 'color: {{VALUE}};' ],
		] );

		$this->add_control( 'font_size', [
			'label'      => esc_html__( 'Font Size', 'redlab-markdown-widget' ),
			'type'       => \Elementor\Controls_Manager::SLIDER,
			'size_units' => [ 'px', 'em', 'rem' ],
			'range'      => [ 'px' => [ 'min' => 12, 'max' => 24 ] ],
			'default'    => [ 'unit' => 'px', 'size' => 16 ],
			'selectors'  => [ '{{WRAPPER}} .redlab-markdown-output' => 'font-size: {{SIZE}}{{UNIT}};' ],
		] );

		$this->add_control( 'line_height', [
			'label'     => esc_html__( 'Line Height', 'redlab-markdown-widget' ),
			'type'      => \Elementor\Controls_Manager::SLIDER,
			'range'     => [ 'px' => [ 'min' => 1.2, 'max' => 2.5, 'step' => 0.1 ] ],
			'default'   => [ 'unit' => 'px', 'size' => 1.7 ],
			'selectors' => [ '{{WRAPPER}} .redlab-markdown-output' => 'line-height: {{SIZE}};' ],
		] );

		$this->add_group_control( \Elementor\Group_Control_Typography::get_type(), [
			'name'     => 'body_typography',
			'selector' => '{{WRAPPER}} .redlab-markdown-output',
		] );

		$this->end_controls_section();

		// --- Headings --------------------------------------------------------
		$this->start_controls_section( 'section_style_headings', [
			'label' => esc_html__( 'Headings', 'redlab-markdown-widget' ),
			'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
		] );

		$this->add_control( 'heading_color', [
			'label'     => esc_html__( 'Heading Color', 'redlab-markdown-widget' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'selectors' => [
				'{{WRAPPER}} .redlab-markdown-output h1,{{WRAPPER}} .redlab-markdown-output h2,{{WRAPPER}} .redlab-markdown-output h3,{{WRAPPER}} .redlab-markdown-output h4,{{WRAPPER}} .redlab-markdown-output h5,{{WRAPPER}} .redlab-markdown-output h6' => 'color: {{VALUE}};',
			],
		] );

		$this->add_control( 'h1_font_size', [
			'label'      => esc_html__( 'H1 Font Size', 'redlab-markdown-widget' ),
			'type'       => \Elementor\Controls_Manager::SLIDER,
			'size_units' => [ 'px', 'em', 'rem' ],
			'range'      => [ 'px' => [ 'min' => 18, 'max' => 72 ] ],
			'default'    => [ 'unit' => 'px', 'size' => 36 ],
			'selectors'  => [ '{{WRAPPER}} .redlab-markdown-output h1' => 'font-size: {{SIZE}}{{UNIT}};' ],
		] );

		$this->add_control( 'h2_font_size', [
			'label'      => esc_html__( 'H2 Font Size', 'redlab-markdown-widget' ),
			'type'       => \Elementor\Controls_Manager::SLIDER,
			'size_units' => [ 'px', 'em', 'rem' ],
			'range'      => [ 'px' => [ 'min' => 16, 'max' => 56 ] ],
			'default'    => [ 'unit' => 'px', 'size' => 28 ],
			'selectors'  => [ '{{WRAPPER}} .redlab-markdown-output h2' => 'font-size: {{SIZE}}{{UNIT}};' ],
		] );

		$this->end_controls_section();

		// --- Links -----------------------------------------------------------
		$this->start_controls_section( 'section_style_links', [
			'label' => esc_html__( 'Links', 'redlab-markdown-widget' ),
			'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
		] );

		$this->add_control( 'link_color', [
			'label'     => esc_html__( 'Link Color', 'redlab-markdown-widget' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '#0073aa',
			'selectors' => [ '{{WRAPPER}} .redlab-markdown-output a' => 'color: {{VALUE}};' ],
		] );

		$this->add_control( 'link_hover_color', [
			'label'     => esc_html__( 'Link Hover Color', 'redlab-markdown-widget' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '#005177',
			'selectors' => [ '{{WRAPPER}} .redlab-markdown-output a:hover' => 'color: {{VALUE}};' ],
		] );

		$this->end_controls_section();
	}

	// -------------------------------------------------------------------------
	// Render
	// -------------------------------------------------------------------------

	protected function render(): void {
		$settings = $this->get_settings_for_display();

		$markdown        = isset( $settings['markdown_text'] )        ? $settings['markdown_text']        : '';
		$preview         = isset( $settings['enable_live_preview'] )   ? $settings['enable_live_preview']   : 'yes';
		$content_theme   = isset( $settings['content_theme'] )         ? $settings['content_theme']         : 'default';
		$highlight_theme = isset( $settings['code_highlight_theme'] )  ? $settings['code_highlight_theme']  : 'tomorrow';
		$line_numbers    = isset( $settings['show_line_numbers'] )      ? $settings['show_line_numbers']      : 'no';
		$show_reading    = isset( $settings['show_reading_info'] )      ? $settings['show_reading_info']      : 'no';
		$reading_pos     = isset( $settings['reading_info_position'] )  ? $settings['reading_info_position']  : 'top';
		$reading_speed   = isset( $settings['reading_speed'] )          ? intval( $settings['reading_speed'] ) : 200;

		$sanitized = wp_kses_post( $markdown );
		$data_attr = esc_attr( wp_json_encode( $sanitized ) );

		// Build class list.
		$classes = 'redlab-markdown-output';
		if ( $content_theme && 'default' !== $content_theme ) {
			$classes .= ' elemark-theme-' . sanitize_html_class( $content_theme );
		}
		if ( 'yes' !== $preview && \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
			$classes .= ' redlab-preview-disabled';
		}
		?>
		<div class="<?php echo esc_attr( $classes ); ?>"
		     data-markdown="<?php echo $data_attr; ?>"
		     data-live-preview="<?php echo esc_attr( $preview ); ?>"
		     data-highlight-theme="<?php echo esc_attr( $highlight_theme ); ?>"
		     data-line-numbers="<?php echo esc_attr( $line_numbers ); ?>"
		     data-show-reading="<?php echo esc_attr( $show_reading ); ?>"
		     data-reading-speed="<?php echo esc_attr( $reading_speed ); ?>"
		     data-reading-position="<?php echo esc_attr( $reading_pos ); ?>">
		</div>
		<?php
	}

	/** Elementor editor JS template — live preview while editing. */
	protected function content_template(): void {
		?>
		<#
		var markdown       = settings.markdown_text || '';
		var livePreview    = settings.enable_live_preview;
		var contentTheme   = settings.content_theme   || 'default';
		var highlightTheme = settings.code_highlight_theme || 'tomorrow';
		var lineNumbers    = settings.show_line_numbers    || 'no';
		var showReading    = settings.show_reading_info    || 'no';
		var readingPos     = settings.reading_info_position || 'top';
		var readingSpeed   = settings.reading_speed        || 200;

		var classes = 'redlab-markdown-output';
		if ( contentTheme && contentTheme !== 'default' ) {
			classes += ' elemark-theme-' + contentTheme;
		}
		if ( 'yes' !== livePreview ) {
			classes += ' redlab-preview-disabled';
		}
		#>
		<div class="{{ classes }}"
		     data-markdown="{{ JSON.stringify( markdown ) }}"
		     data-live-preview="{{ livePreview }}"
		     data-highlight-theme="{{ highlightTheme }}"
		     data-line-numbers="{{ lineNumbers }}"
		     data-show-reading="{{ showReading }}"
		     data-reading-speed="{{ readingSpeed }}"
		     data-reading-position="{{ readingPos }}">
		</div>
		<?php
	}

	// -------------------------------------------------------------------------
	// Demo content
	// -------------------------------------------------------------------------

	private function get_demo_markdown(): string {
		return <<<'MD'
# Markdown Editor Widget

**RedLab Markdown Widget** renders *CommonMark* markdown on your site using [marked.js](https://marked.js.org) with syntax highlighting powered by [Prism.js](https://prismjs.com).

---

## Features

- Full **CommonMark** support
- Syntax highlighting for 16+ languages
- Copy button on code blocks
- Language labels on code blocks
- Line numbers (toggle)
- Theme presets (GitHub, Notion, Medium, Dark, Minimal)
- Reading time & word count badge
- Smooth anchor links on headings

## Code Blocks

```javascript
const greet = (name) => `Hello, ${name}!`;
console.log(greet('World'));
```

```python
def fibonacci(n):
    a, b = 0, 1
    for _ in range(n):
        yield a
        a, b = b, a + b

print(list(fibonacci(10)))
```

```php
<?php
function sayHello(string $name): string {
    return "Hello, {$name}!";
}
echo sayHello('World');
```

## Blockquote

> "The best way to predict the future is to invent it."
> — Alan Kay

## Table

| Feature             | Status |
|---------------------|--------|
| Syntax Highlighting | ✅     |
| Copy Button         | ✅     |
| Line Numbers        | ✅     |
| Theme Presets       | ✅     |
| Reading Time        | ✅     |
| Anchor Links        | ✅     |

## Ordered List

1. Install the plugin
2. Add the Markdown Editor widget
3. Write your markdown
4. Publish!
MD;
	}
}
