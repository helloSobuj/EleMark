# EleMark — Elementor Markdown Widget

![EleMark Banner](assets/banner-1544x500.png)

[![WordPress](https://img.shields.io/badge/WordPress-5.0%2B-blue?logo=wordpress)](https://wordpress.org)
[![Elementor](https://img.shields.io/badge/Elementor-3.0%2B-pink?logo=elementor)](https://elementor.com)
[![License](https://img.shields.io/badge/License-GPL%20v2-green)](https://www.gnu.org/licenses/gpl-2.0.html)
[![Version](https://img.shields.io/badge/Version-1.0.0-orange)](https://github.com/redboltit/elemark/releases)

> Write in Markdown. Publish as beautiful HTML. No coding required.

EleMark adds a powerful **Markdown Editor widget** to Elementor — the world's leading WordPress page builder. Just write your content in Markdown syntax and EleMark renders it as clean, styled HTML instantly on your frontend.

---

## ✨ Features (Free)

- 📝 **Full CommonMark Markdown** — Headings, bold, italic, lists, links, images, tables, code blocks, blockquotes, horizontal rules
- 👁️ **Live Preview in Editor** — See rendered output in real-time inside Elementor
- 🎨 **Style Controls** — Typography, colors, padding, heading sizes via Elementor Style tab
- 💻 **Code Block Styling** — Dark themed code blocks with monospace font
- 🔗 **HTML inside Markdown** — Supports raw HTML for buttons, divs, and custom elements
- 📱 **Fully Responsive** — Works on desktop, tablet, and mobile
- ⚡ **Lightweight** — No bloat, loads only when widget is present

---

## 💎 EleMark Red (Pro)

The premium version of EleMark with advanced features for power users and developers.

| Feature | EleMark (Free) | EleMark Red (Pro) |
|---|---|---|
| Full Markdown Support | ✅ | ✅ |
| Live Preview | ✅ | ✅ |
| Syntax Highlighting | ✅ | ✅ |
| Theme Presets | ✅ | ✅ |
| Copy Button on Code | ✅ | ✅ |
| Markdown Toolbar | ❌ | ✅ |
| .md File Import/Export | ❌ | ✅ |
| Table of Contents (Auto) | ❌ | ✅ |
| Mermaid Diagram Support | ❌ | ✅ |
| Math / LaTeX (KaTeX) | ❌ | ✅ |
| Template Library | ❌ | ✅ |
| Reading Time Badge | ❌ | ✅ |
| Priority Support | ❌ | ✅ |

> 🔴 **EleMark Red** — Coming Soon at [redboltit.com](https://redboltit.com)

---

## 📦 Installation

### From WordPress Admin (Recommended)
1. Go to **Plugins → Add New**
2. Search for **"EleMark"**
3. Click **Install Now** → **Activate**
4. Open any page with **Elementor**
5. Search for **"Markdown Editor"** in the widget panel
6. Drag & drop onto your page

### Manual Installation
1. Download the latest `.zip` from [Releases](https://github.com/redboltit/elemark/releases)
2. Go to **Plugins → Add New → Upload Plugin**
3. Upload the zip file and activate

### Via Composer / WP-CLI
```bash
wp plugin install elemark --activate
```

---

## 🚀 Quick Start

Once installed, open Elementor editor:

1. Find **"RedLab Widgets"** category in the left panel
2. Drag **"Markdown Editor"** widget to your page
3. Start writing Markdown in the content area:

```markdown
# Hello World

This is **EleMark** — write markdown, get beautiful HTML.

## Features
- Easy to use
- Fully styled
- Live preview

> "The best markdown editor for Elementor."

```python
print("Hello from EleMark!")
```
```

4. See it render live in the editor!

---

## 🛠️ Requirements

| Requirement | Minimum Version |
|---|---|
| WordPress | 5.0+ |
| Elementor | 3.0+ |
| PHP | 7.4+ |
| Browser | Chrome 70+, Firefox 70+, Safari 13+ |

> ⚠️ **Elementor Pro is NOT required.** Works with free Elementor.

---

## 📁 Project Structure

```
elemark/
├── elemark.php                        ← Main plugin bootstrap
├── includes/
│   └── class-markdown-widget.php      ← Elementor widget class
├── assets/
│   ├── js/
│   │   ├── marked.min.js              ← Markdown parser
│   │   └── markdown-render.js         ← Custom render logic
│   └── css/
│       └── markdown-style.css         ← Default styling
├── readme.txt                         ← WordPress.org readme
└── README.md                          ← This file
```

---

## 🤝 Contributing

Contributions are welcome! Here's how:

1. Fork this repo
2. Create a feature branch: `git checkout -b feature/my-feature`
3. Commit your changes: `git commit -m "Add: my feature"`
4. Push to branch: `git push origin feature/my-feature`
5. Open a **Pull Request**

### Development Setup
```bash
# Clone the repo
git clone https://github.com/redboltit/elemark.git

# Copy to your local WordPress plugins folder
cp -r elemark/ /path/to/wordpress/wp-content/plugins/

# Activate via WP Admin or WP-CLI
wp plugin activate elemark
```

---

## 🐛 Bug Reports & Feature Requests

- 🐛 **Bug?** → [Open an Issue](https://github.com/redboltit/elemark/issues)
- 💡 **Feature Request?** → [Start a Discussion](https://github.com/redboltit/elemark/discussions)
- 📧 **Email:** support@redboltit.com

---

## 📜 Changelog

### v1.0.0 — Initial Release
- Full CommonMark markdown support
- Live preview in Elementor editor
- Style tab controls (typography, colors, spacing)
- Dark themed code blocks
- HTML inside markdown support
- Responsive design

---

## 📄 License

EleMark is open-source software licensed under the **GPL v2 or later**.

```
Copyright (C) 2025 RedBolt IT
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License.
```

---

## 👨‍💻 Author

**RedBolt IT**
- 🌐 Website: [redboltit.com](https://redboltit.com)
- 🐙 GitHub: [@redboltit](https://github.com/redboltit)
- 📧 Email: hello@redboltit.com

---

<p align="center">
  Made with ❤️ by <a href="https://redboltit.com">RedBolt IT</a>
  <br>
  <strong>EleMark Red — Pro version coming soon 🔴</strong>
</p>
