=== RedLab Markdown Widget ===
Contributors: redboltit
Tags: elementor, markdown, widget, editor, commonmark
Requires at least: 5.9
Tested up to: 6.9
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Adds a full-featured Markdown Editor widget to Elementor with live preview and CommonMark support.

== Description ==

**RedLab Markdown Widget** integrates seamlessly with Elementor to let you write content in Markdown and render it as clean, styled HTML on the frontend.

= Key Features =

* Full **CommonMark** markdown support (headings, bold, italic, lists, links, images, tables, code blocks, blockquotes, horizontal rules)
* **GitHub Flavoured Markdown (GFM)** — tables, task lists, strikethrough
* **Live preview** inside the Elementor editor as you type
* Rich **Style tab** with controls for typography, colors, padding, heading sizes, link colors, and code block theming
* Powered by [marked.js](https://marked.js.org) — a fast, spec-compliant markdown parser
* All output sanitized with `wp_kses_post`
* Assets loaded only on pages that use the widget — no unnecessary overhead
* Compatible with Elementor Free and Elementor Pro

= Requirements =

* WordPress 5.9+
* Elementor 3.0.0+
* PHP 7.4+

== Installation ==

1. Upload the `redlab-markdown-widget` folder to `/wp-content/plugins/`.
2. Activate the plugin from the **Plugins** screen in WordPress admin.
3. Open Elementor on any page or post.
4. In the widget search panel, type "Markdown" — the **Markdown Editor** widget will appear under the **RedLab Widgets** category.
5. Drag the widget onto your page and start writing Markdown in the Content tab.

== Usage ==

1. **Content Tab**
   - Write your Markdown in the *Write Markdown Here* code editor.
   - Toggle *Live Preview in Editor* to see your rendered output update in real time.

2. **Style Tab**
   - **Container** — set background color, padding, and margin.
   - **Typography** — adjust font size, line height, font family, and text color.
   - **Headings** — set a unified heading color, and individual sizes for H1 and H2.
   - **Links** — set link color and hover color.
   - **Code** — customize code block background, text color, and font size.

== Frequently Asked Questions ==

= Does this work without Elementor? =
No. The widget requires Elementor 3.0.0 or higher to function.

= Which Elementor plan do I need? =
The widget works with both **Elementor Free** and **Elementor Pro**.

= Can I use HTML inside my Markdown? =
Yes. Inline HTML is supported and sanitized through `wp_kses_post`.

= Is marked.js loaded on every page? =
No. The plugin checks whether the widget is present on the current page and only loads assets when needed.

== Changelog ==

= 1.0.0 =
* Initial release.

== Upgrade Notice ==

= 1.0.0 =
Initial release — no upgrade steps required.
