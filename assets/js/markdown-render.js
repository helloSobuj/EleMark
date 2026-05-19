( function () {
	'use strict';

	// -------------------------------------------------------------------------
	// Constants
	// -------------------------------------------------------------------------

	var LANG_NAMES = {
		'javascript': 'JavaScript', 'js':         'JavaScript',
		'typescript': 'TypeScript', 'ts':         'TypeScript',
		'python':     'Python',     'py':         'Python',
		'php':        'PHP',
		'bash':       'Bash',       'shell':      'Shell',       'sh': 'Shell',
		'css':        'CSS',
		'html':       'HTML',       'markup':     'HTML',
		'json':       'JSON',
		'sql':        'SQL',
		'jsx':        'React JSX',
		'yaml':       'YAML',       'yml':        'YAML',
		'markdown':   'Markdown',   'md':         'Markdown',
		'go':         'Go',
		'rust':       'Rust',
		'java':       'Java',
		'cpp':        'C++',        'c':          'C',
	};

	var PRISM_THEME_URLS = {
		'tomorrow':  'https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism-tomorrow.min.css',
		'okaidia':   'https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism-okaidia.min.css',
		'solarized': 'https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism-solarizedlight.min.css',
		'ghcolors':  'https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism.min.css',
		'vsc-dark':  'https://cdnjs.cloudflare.com/ajax/libs/prism/1.29.0/themes/prism-vsc-dark-plus.min.css',
	};

	// -------------------------------------------------------------------------
	// marked.js — one-time configuration
	// -------------------------------------------------------------------------

	var markedReady = false;

	function configureMarked() {
		if ( typeof marked === 'undefined' ) {
			return false;
		}
		if ( markedReady ) {
			return true;
		}
		marked.use( { gfm: true, breaks: true } );
		markedReady = true;
		return true;
	}

	// -------------------------------------------------------------------------
	// FEATURE 7: Smooth anchor links — heading IDs
	// -------------------------------------------------------------------------

	function slugify( text ) {
		return text.toLowerCase()
			.replace( /[^\w\s-]/g, '' )
			.replace( /[\s_]+/g, '-' )
			.replace( /^-+|-+$/g, '' );
	}

	function processHeadings( el ) {
		var usedIds = {};

		el.querySelectorAll( 'h1,h2,h3,h4,h5,h6' ).forEach( function ( heading ) {
			// Build slug from text content (strip any existing anchor icons first).
			var text = heading.textContent.replace( /#\s*$/, '' ).trim();
			var base = slugify( text );
			if ( ! base ) {
				return;
			}

			// Handle duplicate IDs by appending -2, -3, etc.
			var id = base;
			if ( usedIds[ base ] ) {
				usedIds[ base ]++;
				id = base + '-' + usedIds[ base ];
			} else {
				usedIds[ base ] = 1;
			}
			heading.id = id;

			// Append clickable anchor icon.
			var anchor = document.createElement( 'a' );
			anchor.href        = '#' + id;
			anchor.className   = 'elemark-anchor-link';
			anchor.title       = 'Copy link to section';
			anchor.innerHTML   = ' <span class="elemark-anchor-icon">#</span>';
			anchor.setAttribute( 'aria-hidden', 'true' );

			anchor.addEventListener( 'click', function ( e ) {
				e.preventDefault();
				var url = window.location.href.split( '#' )[ 0 ] + '#' + id;
				if ( navigator.clipboard ) {
					navigator.clipboard.writeText( url ).catch( function () {} );
				}
				window.location.hash = id;
				var target = document.getElementById( id );
				if ( target ) {
					target.scrollIntoView( { behavior: 'smooth' } );
				}
			} );

			heading.appendChild( anchor );
		} );
	}

	// -------------------------------------------------------------------------
	// FEATURE 3: Language label on code blocks
	// -------------------------------------------------------------------------

	function addLanguageLabels( el ) {
		el.querySelectorAll( 'pre' ).forEach( function ( pre ) {
			var code = pre.querySelector( 'code' );
			if ( ! code ) {
				return;
			}

			// Extract language from class e.g. "language-python".
			var lang = null;
			code.classList.forEach( function ( cls ) {
				if ( cls.startsWith( 'language-' ) ) {
					lang = cls.replace( 'language-', '' );
				}
			} );

			if ( ! lang ) {
				return;
			}

			var displayName = LANG_NAMES[ lang ] || lang.toUpperCase();

			var label = document.createElement( 'span' );
			label.className   = 'elemark-lang-label';
			label.textContent = displayName;

			pre.appendChild( label );

			// When a language label is present, add top padding so label/button
			// don't overlap the code content.
			pre.style.paddingTop = '40px';
		} );
	}

	// -------------------------------------------------------------------------
	// FEATURE 2: Copy button on code blocks
	// -------------------------------------------------------------------------

	function addCopyButtons( el ) {
		el.querySelectorAll( 'pre' ).forEach( function ( pre ) {
			var code = pre.querySelector( 'code' );
			if ( ! code ) {
				return;
			}

			// position:relative on the <pre> so the button can be absolute.
			pre.style.position = 'relative';

			var btn = document.createElement( 'button' );
			btn.className   = 'elemark-copy-btn';
			btn.textContent = 'Copy';
			btn.setAttribute( 'aria-label', 'Copy code to clipboard' );
			btn.setAttribute( 'type', 'button' );

			btn.addEventListener( 'click', function () {
				var text = code.innerText || code.textContent || '';

				var doSuccess = function () {
					btn.textContent = '✓ Copied!';
					btn.classList.add( 'copied' );
					setTimeout( function () {
						btn.textContent = 'Copy';
						btn.classList.remove( 'copied' );
					}, 2000 );
				};

				var doFallback = function () {
					try {
						var ta = document.createElement( 'textarea' );
						ta.value = text;
						ta.style.cssText = 'position:fixed;top:-9999px;left:-9999px';
						document.body.appendChild( ta );
						ta.select();
						document.execCommand( 'copy' );
						document.body.removeChild( ta );
						doSuccess();
					} catch ( err ) {
						console.warn( 'EleMark: clipboard copy failed', err );
					}
				};

				if ( navigator.clipboard && navigator.clipboard.writeText ) {
					navigator.clipboard.writeText( text ).then( doSuccess, doFallback );
				} else {
					doFallback();
				}
			} );

			pre.appendChild( btn );
		} );
	}

	// -------------------------------------------------------------------------
	// FEATURE 4: Line numbers
	// -------------------------------------------------------------------------

	function applyLineNumbers( el ) {
		var enabled = el.getAttribute( 'data-line-numbers' ) === 'yes';
		if ( ! enabled ) {
			return;
		}
		// Prism's line-numbers plugin activates when <pre> has the class.
		el.querySelectorAll( 'pre' ).forEach( function ( pre ) {
			pre.classList.add( 'line-numbers' );
		} );
	}

	// -------------------------------------------------------------------------
	// FEATURE 1: Prism syntax highlighting
	// -------------------------------------------------------------------------

	function runPrism( el ) {
		if ( typeof Prism === 'undefined' ) {
			return;
		}
		// Re-highlight only within this element.
		el.querySelectorAll( 'pre code[class*="language-"]' ).forEach( function ( block ) {
			Prism.highlightElement( block );
		} );
	}

	// -------------------------------------------------------------------------
	// FEATURE 1 (editor): Dynamic Prism theme switching
	// Only runs inside the Elementor editor preview iframe.
	// -------------------------------------------------------------------------

	function applyHighlightTheme( theme ) {
		var url = PRISM_THEME_URLS[ theme ] || PRISM_THEME_URLS[ 'tomorrow' ];
		var existing = document.getElementById( 'elemark-prism-theme-dynamic' );
		if ( existing ) {
			if ( existing.getAttribute( 'href' ) !== url ) {
				existing.setAttribute( 'href', url );
			}
		} else {
			var link     = document.createElement( 'link' );
			link.rel     = 'stylesheet';
			link.id      = 'elemark-prism-theme-dynamic';
			link.href    = url;
			document.head.appendChild( link );
		}
	}

	// -------------------------------------------------------------------------
	// FEATURE 6: Reading time + word count
	// -------------------------------------------------------------------------

	function calculateReadingInfo( htmlContent, wordsPerMinute ) {
		var wpm  = wordsPerMinute > 0 ? wordsPerMinute : 200;
		var temp = document.createElement( 'div' );
		temp.innerHTML = htmlContent;
		var text      = temp.innerText || temp.textContent || '';
		var words     = text.trim().split( /\s+/ ).filter( function ( w ) { return w.length > 0; } );
		var wordCount = words.length;
		var minutes   = Math.max( 1, Math.ceil( wordCount / wpm ) );
		return { wordCount: wordCount, minutes: minutes };
	}

	function addReadingBadge( el, renderedHtml ) {
		if ( el.getAttribute( 'data-show-reading' ) !== 'yes' ) {
			return;
		}

		var speed    = parseInt( el.getAttribute( 'data-reading-speed' ), 10 ) || 200;
		var position = el.getAttribute( 'data-reading-position' ) || 'top';
		var info     = calculateReadingInfo( renderedHtml, speed );

		var badge          = document.createElement( 'div' );
		badge.className    = 'elemark-reading-badge';
		badge.innerHTML    =
			'<span>📚 ' + info.minutes + ' min read</span>' +
			'<span class="elemark-word-count">' + info.wordCount.toLocaleString() + ' words</span>';

		if ( position === 'bottom' ) {
			el.appendChild( badge );
		} else {
			el.insertBefore( badge, el.firstChild );
		}
	}

	// -------------------------------------------------------------------------
	// External links
	// -------------------------------------------------------------------------

	function processLinks( el ) {
		el.querySelectorAll( 'a[href]' ).forEach( function ( a ) {
			var href = a.getAttribute( 'href' ) || '';
			if ( /^https?:\/\//i.test( href ) && href.indexOf( window.location.hostname ) === -1 ) {
				a.setAttribute( 'target', '_blank' );
				a.setAttribute( 'rel', 'noopener noreferrer' );
			}
		} );
	}

	// -------------------------------------------------------------------------
	// Settings fingerprint — skip re-render when nothing changed
	// -------------------------------------------------------------------------

	function getFingerprint( el ) {
		return [
			el.getAttribute( 'data-markdown' )          || '',
			el.getAttribute( 'data-line-numbers' )      || 'no',
			el.getAttribute( 'data-show-reading' )      || 'no',
			el.getAttribute( 'data-reading-speed' )     || '200',
			el.getAttribute( 'data-reading-position' )  || 'top',
			el.getAttribute( 'data-highlight-theme' )   || 'tomorrow',
			el.className,
		].join( '|' );
	}

	// -------------------------------------------------------------------------
	// Core render function — executed in the spec-defined order
	// -------------------------------------------------------------------------

	function renderElement( el ) {
		var raw = el.getAttribute( 'data-markdown' );
		if ( ! raw ) {
			return;
		}

		var fingerprint = getFingerprint( el );
		if ( el.dataset.elmFingerprint === fingerprint ) {
			return; // Nothing changed — skip.
		}

		// 1. Parse markdown content from JSON-encoded data attribute.
		var markdown = '';
		try {
			markdown = JSON.parse( raw );
		} catch ( e ) {
			markdown = raw; // Fallback: treat as plain text.
		}

		if ( ! markdown ) {
			return;
		}

		var renderedHtml;
		try {
			// 2. Run marked.parse().
			renderedHtml = marked.parse( markdown );

			// 3. Set innerHTML.
			el.innerHTML = renderedHtml;
		} catch ( err ) {
			console.error( 'EleMark — render error:', err );
			el.innerHTML =
				'<p style="color:#c0392b;font-family:monospace;padding:8px;">'
				+ '<strong>Markdown render error.</strong> '
				+ 'See browser console for details.'
				+ '</p>';
			return;
		}

		// 4. Heading IDs + anchor links.
		processHeadings( el );

		// 5. Language labels.
		addLanguageLabels( el );

		// 6. Copy buttons.
		addCopyButtons( el );

		// 7. Apply line-numbers class before Prism so the plugin picks it up.
		applyLineNumbers( el );

		// 8. Prism syntax highlighting.
		runPrism( el );

		// 9. Reading time badge (uses the rendered HTML for word counting).
		addReadingBadge( el, renderedHtml );

		// 10. External link handling (after Prism so no double-pass needed).
		processLinks( el );

		// Dynamic theme switch — only in Elementor editor preview iframe.
		if ( window.elementorFrontend && typeof elementorFrontend.isEditMode === 'function' && elementorFrontend.isEditMode() ) {
			applyHighlightTheme( el.getAttribute( 'data-highlight-theme' ) || 'tomorrow' );
		}

		// Store fingerprint so repeat calls skip unchanged widgets.
		el.dataset.elmFingerprint = fingerprint;
	}

	// -------------------------------------------------------------------------
	// Render all widgets (or those inside a given root element)
	// -------------------------------------------------------------------------

	function renderAll( root ) {
		if ( ! configureMarked() ) {
			console.warn( 'EleMark: marked.js is not loaded.' );
			return;
		}
		var container = ( root instanceof Element ) ? root : document;
		container.querySelectorAll( '.redlab-markdown-output' ).forEach( renderElement );
	}

	// -------------------------------------------------------------------------
	// Elementor editor hook registration (handles init race condition)
	// -------------------------------------------------------------------------

	function registerElementorHook() {
		if ( window.elementorFrontend && window.elementorFrontend.hooks ) {
			window.elementorFrontend.hooks.addAction(
				'frontend/element_ready/redlab_markdown_editor.default',
				function ( $scope ) {
					renderAll( $scope[ 0 ] );
				}
			);
			return true;
		}
		return false;
	}

	if ( ! registerElementorHook() ) {
		window.addEventListener( 'elementor/frontend/init', registerElementorHook );
	}

	// -------------------------------------------------------------------------
	// Bootstrap — standard frontend page load
	// -------------------------------------------------------------------------

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', function () {
			renderAll();
		} );
	} else {
		renderAll();
	}

	// Public API.
	window.redlabMarkdownRender = renderAll;

} )();
