( function () {
	'use strict';

	var markedReady = false;

	/**
	 * One-time marked configuration.
	 * Guard with a flag so stacking marked.use() calls on re-renders is avoided.
	 */
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

	/**
	 * Add target="_blank" / rel to external links via DOM traversal.
	 *
	 * Avoids using marked's Renderer subclass, whose internal this.parser
	 * dependency (injected only during an active parse cycle) would throw
	 * a TypeError when called on a standalone new marked.Renderer() instance.
	 */
	function processLinks( el ) {
		el.querySelectorAll( 'a[href]' ).forEach( function ( a ) {
			var href = a.getAttribute( 'href' ) || '';
			if (
				/^https?:\/\//i.test( href ) &&
				href.indexOf( window.location.hostname ) === -1
			) {
				a.setAttribute( 'target', '_blank' );
				a.setAttribute( 'rel', 'noopener noreferrer' );
			}
		} );
	}

	/** Render a single .redlab-markdown-output element. */
	function renderElement( el ) {
		var raw = el.getAttribute( 'data-markdown' );
		if ( ! raw ) {
			return;
		}

		// Skip re-render if content hasn't changed.
		if ( el.dataset.lastMd === raw ) {
			return;
		}

		var markdown = '';
		try {
			// The attribute stores a JSON-encoded string (so it survives HTML escaping).
			markdown = JSON.parse( raw );
		} catch ( e ) {
			markdown = raw; // Fallback: treat as plain text.
		}

		if ( ! markdown ) {
			return;
		}

		try {
			el.innerHTML  = marked.parse( markdown );
			el.dataset.lastMd = raw;
			processLinks( el );
		} catch ( err ) {
			console.error( 'RedLab Markdown Widget — render error:', err );
			el.innerHTML =
				'<p style="color:#c0392b;font-family:monospace;padding:8px;">'
				+ '<strong>Markdown render error.</strong> '
				+ 'See browser console for details.'
				+ '</p>';
		}
	}

	/** Render every widget found inside `root` (default: whole document). */
	function renderAll( root ) {
		if ( ! configureMarked() ) {
			console.warn( 'RedLab Markdown Widget: marked.js is not loaded.' );
			return;
		}
		var container = ( root instanceof Element ) ? root : document;
		container.querySelectorAll( '.redlab-markdown-output' ).forEach( renderElement );
	}

	// -------------------------------------------------------------------------
	// Register Elementor widget-ready hook.
	// We must handle the race: our footer script might run before OR after
	// elementorFrontend fires its 'init' DOM custom event.
	// -------------------------------------------------------------------------
	function registerElementorHook() {
		if (
			window.elementorFrontend &&
			window.elementorFrontend.hooks
		) {
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

	// Try immediately (script loaded after Elementor init).
	if ( ! registerElementorHook() ) {
		// Not ready yet — listen for Elementor's custom DOM event.
		window.addEventListener( 'elementor/frontend/init', registerElementorHook );
	}

	// -------------------------------------------------------------------------
	// Bootstrap — standard frontend page load.
	// -------------------------------------------------------------------------
	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', function () {
			renderAll();
		} );
	} else {
		renderAll();
	}

	// Public API for external callers.
	window.redlabMarkdownRender = renderAll;
} )();
