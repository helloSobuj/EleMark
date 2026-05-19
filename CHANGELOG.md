# Changelog

All notable changes to **EleMark — RedLab Markdown Widget** are documented here.  
Format follows [Keep a Changelog](https://keepachangelog.com/en/1.1.0/).

---

## [1.1.0] — 2026-05-19

### Added
- **Syntax highlighting** via Prism.js v1.29.0 — 16 languages supported: JavaScript, TypeScript, Python, PHP, Bash/Shell, CSS, HTML, JSON, SQL, JSX, YAML, Markdown, Go, Rust, Java, C, C++.
- **5 highlight themes** selectable per widget: Tomorrow Night, Okaidia, Solarized Light, GitHub Colors, VS Code Dark+. Only the chosen theme CSS is enqueued.
- **Copy button** on every fenced code block — uses Clipboard API with `execCommand` fallback for older browsers.
- **Language label badge** displayed in the top-right corner of fenced code blocks (e.g. "Python", "JavaScript").
- **Line numbers** toggle in the Content tab — powered by the Prism line-numbers plugin.
- **5 content theme presets** in the Style tab: GitHub, Notion, Medium, Dark, Minimal. Each applies a complete visual style to all markdown elements.
- **Reading time + word count badge** — configurable words-per-minute (100–400), position top or bottom.
- **Smooth anchor links** — every heading gets an auto-generated ID slug. Clicking the `#` icon copies the direct URL to clipboard and scrolls smoothly to the section. Duplicate headings get `-2`, `-3` suffixes.
- Prism.js core + 16 language components bundled locally (`assets/js/prism.min.js`) — no external CDN required at runtime.
- Prism line-numbers plugin bundled locally (`assets/js/prism-line-numbers.min.js`).
- `register_assets()` (priority 5) now separates asset registration from enqueueing, ensuring handles are available to `get_script_depends()` / `get_style_depends()`.
- `get_page_widget_setting()` helper scans `_elementor_data` JSON via regex to read per-page widget settings without a full JSON decode.
- Settings fingerprint (`data-elm-fingerprint`) prevents redundant re-renders when nothing changed between Elementor preview refreshes.
- Dynamic Prism theme switching in the Elementor editor preview iframe via `<link id="elemark-prism-theme-dynamic">` injection.
- External links in rendered output automatically get `target="_blank" rel="noopener noreferrer"`.

### Changed
- Plugin version bumped to `1.1.0`.
- `enqueue_frontend_assets()` now enqueues only the selected Prism highlight theme CSS (previously no theme support).
- `markdown-render.js` completely rewritten — all 7 features implemented in spec-defined execution order; marked.js Renderer subclass removed in favour of DOM post-processing to avoid v15 API incompatibility.
- `markdown-style.css` extended with styles for all new UI components (copy button, language label, reading badge, anchor links, theme presets).
- `class-markdown-widget.php` — added "Display Options" section (line numbers, reading time controls) and "Content Theme" + "Code Block" style sections; `render()` and `content_template()` updated with 7 new `data-*` attributes.
- `readme.txt` updated to reflect v1.1.0 features and changelog.

---

## [1.0.0] — 2025-01-01

### Added
- Initial release of the RedLab Markdown Widget for Elementor.
- Full CommonMark + GitHub Flavoured Markdown (GFM) support via marked.js v15.
- Live preview inside the Elementor editor as you type.
- Style tab with controls for typography, colors, padding, heading sizes, link colors, and code block theming.
- Output sanitized with `wp_kses_post`.
- Smart asset loading — assets only enqueued on pages that use the widget.
- Compatible with Elementor Free and Elementor Pro (3.0.0+), WordPress 5.9+, PHP 7.4+.

---

[1.1.0]: https://github.com/helloSobuj/EleMark/compare/v1.0.0...v1.1.0
[1.0.0]: https://github.com/helloSobuj/EleMark/releases/tag/v1.0.0
