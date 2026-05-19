<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Elementor widget: Markdown Editor
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
		return [ 'marked-js', 'redlab-markdown-render' ];
	}

	public function get_style_depends(): array {
		return [ 'redlab-markdown-style' ];
	}

	// -------------------------------------------------------------------------
	// Controls
	// -------------------------------------------------------------------------

	protected function register_controls(): void {
		$this->register_content_controls();
		$this->register_style_controls();
	}

	private function register_content_controls(): void {
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
	}

	private function register_style_controls(): void {
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
			'selectors' => [ '{{WRAPPER}} .redlab-markdown-output h1, {{WRAPPER}} .redlab-markdown-output h2, {{WRAPPER}} .redlab-markdown-output h3, {{WRAPPER}} .redlab-markdown-output h4, {{WRAPPER}} .redlab-markdown-output h5, {{WRAPPER}} .redlab-markdown-output h6' => 'color: {{VALUE}};' ],
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

		// --- Code ------------------------------------------------------------
		$this->start_controls_section( 'section_style_code', [
			'label' => esc_html__( 'Code', 'redlab-markdown-widget' ),
			'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
		] );

		$this->add_control( 'code_bg', [
			'label'     => esc_html__( 'Code Block Background', 'redlab-markdown-widget' ),
			'type'      => \Elementor\Controls_Manager::COLOR,
			'default'   => '#1e1e1e',
			'selectors' => [ '{{WRAPPER}} .redlab-markdown-output pre' => 'background-color: {{VALUE}};' ],
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
	}

	// -------------------------------------------------------------------------
	// Render
	// -------------------------------------------------------------------------

	protected function render(): void {
		$settings = $this->get_settings_for_display();
		$markdown  = isset( $settings['markdown_text'] ) ? $settings['markdown_text'] : '';
		$sanitized = wp_kses_post( $markdown );
		$data_attr = esc_attr( wp_json_encode( $sanitized ) );
		$preview   = isset( $settings['enable_live_preview'] ) ? $settings['enable_live_preview'] : 'yes';

		$classes = 'redlab-markdown-output';
		if ( 'yes' !== $preview && \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
			$classes .= ' redlab-preview-disabled';
		}
		?>
		<div class="<?php echo esc_attr( $classes ); ?>"
		     data-markdown="<?php echo $data_attr; ?>"
		     data-live-preview="<?php echo esc_attr( $preview ); ?>">
		</div>
		<?php
	}

	/**
	 * Elementor editor JS template (live preview while editing the widget).
	 */
	protected function content_template(): void {
		?>
		<#
		var markdown   = settings.markdown_text || '';
		var livePreview = settings.enable_live_preview;
		var classes    = 'redlab-markdown-output';
		if ( 'yes' !== livePreview ) {
			classes += ' redlab-preview-disabled';
		}
		#>
		<div class="{{ classes }}"
		     data-markdown="{{ JSON.stringify( markdown ) }}"
		     data-live-preview="{{ livePreview }}">
		</div>
		<?php
	}

	// -------------------------------------------------------------------------
	// Demo content
	// -------------------------------------------------------------------------

	private function get_demo_markdown(): string {
		return <<<'MD'
# Markdown Editor Widget

**RedLab Markdown Widget** renders *CommonMark* markdown on your site using [marked.js](https://marked.js.org).

---

## Features

- Full **CommonMark** support
- GFM tables and task lists
- Syntax-highlighted code blocks
- Responsive images

## Inline Styles

You can use **bold**, *italic*, ~~strikethrough~~, and `inline code` freely.

## Code Block

```javascript
const greet = (name) => `Hello, ${name}!`;
console.log(greet('World'));
```

## Blockquote

> "The best way to predict the future is to invent it."
> — Alan Kay

## Table

| Feature       | Supported |
|---------------|-----------|
| Headings      | ✅        |
| Tables        | ✅        |
| Code blocks   | ✅        |
| Images        | ✅        |
| Links         | ✅        |

## Image

![WordPress Logo](https://s.w.org/style/images/about/WordPress-logotype-standard.png)

## Ordered List

1. Install the plugin
2. Add the Markdown Editor widget
3. Write your markdown
4. Publish!
MD;
	}
}
