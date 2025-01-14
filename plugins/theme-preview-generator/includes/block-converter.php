<?php
/**
 * Block Converter.
 * Convert HTML to Gutenberg Blocks.
 *
 * @package Tyme\BlockConverter
 */

namespace Tyme;

use DOMDocument;
use DOMElement;

/* phpcs:disable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase */

/**
 * Convert HTML to Gutenberg Blocks
 *
 * Usage:
 * $converter = new \Tyme\BlockConverter();
 * $html = '<p>Some HTML content</p>';
 * $block_markup = $converter->convert_blocks( $html );
 */
class BlockConverter {

	/**
	 * Block Converter Options.
	 *
	 * @var array
	 */
	public $options = [
		'upload_media' => true,
		'force_https'  => true,
		'auto_p'       => true,
	];

	/**
	 * Raw content provided
	 *
	 * @var string
	 */
	private $raw_content = '';

	/**
	 * Dom document
	 *
	 * @var DOMDocument
	 */
	private $dom_content = '';

	/**
	 * Processed block content
	 *
	 * @var string
	 */
	private $block_content = '';

	/**
	 * A readable list of allowed tags and attributes.
	 * "class" and "id" are always allowed and do not need to be defined.
	 *
	 * @var array
	 */
	private $allowed_tags = [
		// Content Sectioning
		'h1'         => [],
		'h2'         => [],
		'h3'         => [],
		'h4'         => [],
		'h5'         => [],
		'h6'         => [],

		// Text Content
		'blockquote' => [],
		'dd'         => [],
		'dt'         => [],
		'dl'         => [],
		'figcaption' => [],
		'figure'     => [],
		'hr'         => [],
		'li'         => [],
		'ul'         => [
			'type',
		],
		'ol'         => [
			'reversed',
			'start',
			'type',
		],
		'p'          => [],

		// Inline text semantics
		'a'          => [
			'href',
			'target',
			'title',
			'rel',
		],
		'abbr'       => [
			'title',
		],
		'b'          => [],
		'br'         => [],
		'cite'       => [],
		'code'       => [],
		'em'         => [],
		'i'          => [],
		's'          => [],
		'strike'     => [],
		'small'      => [],
		'span'       => [],
		'strong'     => [],
		'sub'        => [],
		'sup'        => [],

		// Image and multimedia
		'audio'      => [
			'src',
			'controls',
			'autoplay',
		],
		'img'        => [
			'src',
			'alt',
			'srcset',
			'height',
			'width',
			'sizes',
		],
		'video'      => [
			'src',
			'controls',
			'autoplay',
			'poster',
		],

		// Embedded content
		'embed'      => [
			'src',
			'height',
			'width',
			'type',
		],
		'iframe'     => [
			'src',
			'height',
			'width',
		],
		'object'     => [],
		'picture'    => [],
		'portal'     => [
			'src',
		],
		'pre'        => [],
		'source'     => [
			'type',
			'src',
			'alt',
			'srcset',
			'height',
			'width',
			'sizes',
			'media',
		],

		// Table content
		'caption'    => [],
		'col'        => [
			'span',
		],
		'colgroup'   => [
			'span',
		],
		'table'      => [],
		'tbody'      => [],
		'td'         => [
			'colspan',
			'headers',
			'rowspan',
		],
		'tfoot'      => [],
		'th'         => [
			'abbr',
			'colspan',
			'headers',
			'rowspan',
		],
		'thead'      => [],
		'tr'         => [],
	];

	/**
	 * Dom Element Block Mapping
	 *
	 * @var array
	 */
	private $mapping = [
		'p'          => 'core/paragraph',
		'code'       => [ __CLASS__, 'code' ],
		'pre'        => [ __CLASS__, 'code' ],
		'ul'         => [ __CLASS__, 'list' ],
		'table'      => [ __CLASS__, 'table' ],
		'ol'         => [ __CLASS__, 'ordered_list' ],
		'h1'         => [ __CLASS__, 'heading' ],
		'h2'         => [ __CLASS__, 'heading' ],
		'h3'         => [ __CLASS__, 'heading' ],
		'h4'         => [ __CLASS__, 'heading' ],
		'h5'         => [ __CLASS__, 'heading' ],
		'h6'         => [ __CLASS__, 'heading' ],
		'blockquote' => [ __CLASS__, 'blockquote' ],
		'img'        => [ __CLASS__, 'image' ],
		'hr'         => [ __CLASS__, 'hr' ],
	];

	/**
	 * BlockConverter constructor.
	 *
	 * @param array $options Options.
	 */
	public function __construct( $options = [] ) {
		$this->options      = wp_parse_args( $options, $this->options );
		$this->mapping      = $this->get_mapping();
		$this->allowed_tags = $this->get_allowed_tags();
	}

	/**
	 * Convert HTML to Block Markup
	 *
	 * @param string $content Post Content to Convert.
	 */
	public function convert_blocks( string $content = '' ) {
		$this->load_content( $content );
		$this->convert_to_blocks();

		return $this->render();
	}

	/**
	 * BlockConverter load HTML.
	 *
	 * @param string $html post content
	 */
	public function load_content( string $html ) {
		if ( substr( $html, 0, 1 ) !== '<' ) {
			$html = '<html><body>' . $html . '</body></html>';
		}

		$this->block_content = '';
		$this->raw_content   = $html;
		$this->dom_content   = new DOMDocument();

		$encoded = mb_convert_encoding( stripslashes( $html ), 'HTML-ENTITIES', 'UTF-8' );
		if ( false !== $this->options['auto_p'] ) {
			$encoded = wpautop( $encoded );
		}

		// phpcs:ignore
		@$this->dom_content->loadHTML( $this->clean_content( $encoded ) );
	}

	/**
	 * Return block rendered content
	 *
	 * @return string
	 */
	public function render() {
		if ( empty( $this->block_content ) ) {
			return $this->raw_content;
		}

		// Remove paragraphs that are wrapping other blocks.
		$content = $this->block_content;
		$content = preg_replace( '/(<!-- wp:paragraph --><p>)(<!--)/i', '$2', $content );
		$content = preg_replace( '/(-->)(<\/p><!-- \/wp:paragraph -->)/i', '$1', $content );

		return $content;
	}

	/**
	 * Get block mapping
	 *
	 * @return array
	 */
	public function get_mapping() {
		/**
		 * Filter block converter DOM Mapping.
		 *
		 * @param array $mapping Mapping
		 * @return array
		 */
		return apply_filters( 'block_converter_mapping', $this->mapping );
	}

	/**
	 * Add custom DOM mapping.
	 * Allows for converting elements to custom blocks.
	 *
	 * @param string   $dom_element_tag Dom element tag
	 * @param callable $mapping         Block callback
	 */
	public function add_dom_mapping( $dom_element_tag, $mapping ) {
		$this->mapping[ $dom_element_tag ] = $mapping;
	}

	/**
	 * Remove DOM element mapping
	 *
	 * @param string $dom_element_tag Dom element tag
	 */
	public function remove_dom_mapping( $dom_element_tag ) {
		unset( $this->mapping[ $dom_element_tag ] );
	}

	/**
	 * Set converter option
	 *
	 * @param string $option option name
	 * @param mixed  $value  option value
	 */
	public function set_option( $option, $value ) {
		$this->options[ $option ] = $value;
	}

	/**
	 * Set converter options
	 *
	 * @param array $options options
	 */
	public function set_options( $options = [] ) {
		$this->options = wp_parse_args( $options, $this->options );
	}

	/**
	 * Get allowed tag and attributes.
	 *
	 * @return array
	 */
	public function get_allowed_tags() {
		/**
		 * Filter block converter allowed tags.
		 *
		 * @param array $allowed_tags Allowed tags
		 * @return array
		 */
		return apply_filters( 'block_converter_allowed_tags', $this->allowed_tags );
	}

	/**
	 * Set allowed tag and attributes.
	 *
	 * @param string $tag       Tag name
	 * @param array  $attributes Tag attributes
	 */
	public function add_allowed_tag( $tag, $attributes = [] ) {
		$this->allowed_tags[ $tag ] = $attributes;
	}

	/**
	 * Remove unwanted HTML attributes from an element.
	 *
	 * @param DOMElement $element The element to clean.
	 * @return DOMElement
	 */
	protected function clean_attributes( DOMElement $element ) {
		if ( ! $element->hasAttributes() ) {
			return $element;
		}

		// Default allowed attributes.
		$allowed_attributes = [
			'class',
			'id',
		];

		// Allow adding additional attributes at the tag level.
		if ( isset( $element->tagName ) && ! empty( $this->allowed_tags[ $element->tagName ] ) ) {
			$allowed_attributes = array_merge( $allowed_attributes, $this->allowed_tags[ $element->tagName ] );
		}

		for ( $i = $element->attributes->length - 1; $i >= 0; --$i ) {
			if ( ! in_array( $element->attributes->item( $i )->nodeName, $allowed_attributes, true ) ) {
				$element->removeAttributeNode( $element->attributes->item( $i ) );
			}
		}

		return $element;
	}

	/**
	 * Convert DOMDocument elements to blocks
	 */
	private function convert_to_blocks() {
		if ( 0 === $this->dom_content->getElementsByTagName( 'body' )->count() ) {
			return;
		}

		foreach ( $this->dom_content->getElementsByTagName( 'body' )->item( 0 )->childNodes as $element ) {
			$has_tag_mapping = isset( $element->tagName ) && isset( $this->mapping[ $element->tagName ] );

			if ( $has_tag_mapping && ! $this->is_callback( $this->mapping[ $element->tagName ] ) ) {
				$this->block_content .= $this->serialize_block(
					[
						'blockName' => $this->mapping[ $element->tagName ],
						'attrs'     => [],
						'innerHTML' => $this->get_html( $element ),
					]
				);
				$this->block_content .= "\n\n";
			} elseif ( $has_tag_mapping && $this->is_callback( $this->mapping[ $element->tagName ] ) ) {
				$this->block_content .= $this->serialize_block( call_user_func( $this->mapping[ $element->tagName ], $element ) );
				$this->block_content .= "\n\n";
			} elseif ( ! $has_tag_mapping && ! empty( preg_replace( '/[^a-z_\-0-9]/i', '', $element->textContent ) ) && false === strpos( $element->textContent, '[' ) ) {
				$this->block_content .= $this->serialize_block(
					[
						'blockName' => 'core/paragraph',
						'attrs'     => [],
						'innerHTML' => '<p>' . trim( $this->get_html( $element ) ) . '</p>',
					]
				);

				$this->block_content .= "\n\n";
			} elseif ( isset( $element->tagName ) ) {
				$this->block_content .= $this->serialize_block(
					[
						'blockName' => 'core/html',
						'attrs'     => [],
						'innerHTML' => $this->get_html( $element ),
					]
				);
				$this->block_content .= "\n\n";
			} elseif ( false !== strpos( $element->textContent, '[' ) ) {
				$this->block_content .= $element->textContent;
			}
		}
	}

	/**
	 * Clean content for post.
	 * We use our own allowed tags and attributes for any custom markup.
	 * The rest comes from the default wp_kses_post() tags.
	 *
	 * @param string $content The content.
	 * @return string
	 */
	protected function clean_content( $content ) {
		$allowed_tags = wp_kses_allowed_html( 'post' );
		$allowed_tags = wp_parse_args( $allowed_tags, $this->allowed_tags );
		return wp_kses( $content, $allowed_tags );
	}

	/**
	 * Heading block mapping
	 *
	 * @param DOMElement $element dom element
	 * @return array
	 */
	private function heading( DOMElement $element ) {
		return [
			'blockName' => 'core/heading',
			'attrs'     => [
				'level' => (int) preg_replace( '/\D/', '', $element->tagName ),
			],
			'innerHTML' => $this->get_html( $element ),
		];
	}

	/**
	 * Blockquote block mapping
	 *
	 * @param DOMElement $element dom element
	 * @return array
	 */
	private function blockquote( DOMElement $element ) {
		$element->setAttribute( 'class', 'wp-block-quote' );
		return [
			'blockName' => 'core/quote',
			'attrs'     => [],
			'innerHTML' => $this->get_html( $element ),
		];
	}

	/**
	 * List block mapping
	 *
	 * @param DOMElement $element dom element
	 * @return array
	 */
	private function list( DOMElement $element ) {
		$inner_blocks = '';
		foreach ( $element->getElementsByTagName( 'li' ) as $node ) {
			$inner_blocks .= $this->serialize_block( $this->list_item( $node ) );
		}

		return [
			'blockName' => 'core/list',
			'innerHTML' => '<ul>' . $inner_blocks . '</ul>',
		];
	}

	/**
	 * Ordered List block mapping
	 *
	 * @param DOMElement $element dom element
	 * @return array
	 */
	private function ordered_list( DOMElement $element ) {
		$inner_blocks = '';
		foreach ( $element->getElementsByTagName( 'li' ) as $node ) {
			$inner_blocks .= $this->serialize_block( $this->list_item( $node ) );
		}

		return [
			'blockName' => 'core/list',
			'attrs'     => [
				'ordered' => true,
			],
			'innerHTML' => '<ol>' . $inner_blocks . '</ol>',
		];
	}

	/**
	 * List Item block mapping
	 *
	 * @param DOMElement $element dom element
	 * @return array
	 */
	private function list_item( DOMElement $element ) {
		return [
			'blockName' => 'core/list-item',
			'attrs'     => [],
			'innerHTML' => $this->get_html( $element ),
		];
	}

	/**
	 * Table block mapping
	 *
	 * @param DOMElement $element dom element
	 * @return array
	 */
	private function table( DOMElement $element ) {
		return [
			'blockName' => 'core/table',
			'attrs'     => [],
			'innerHTML' => '<figure class="wp-block-table">' . $this->get_html( $element ) . '</figure>',
		];
	}

	/**
	 * HR block mapping
	 *
	 * @return array
	 */
	private function hr() {
		return [
			'blockName' => 'core/separator',
			'attrs'     => [],
			'innerHTML' => '<hr class="wp-block-separator"/>',
		];
	}

	/**
	 * Code block mapping
	 *
	 * @param DOMElement $element dom element
	 * @return array
	 */
	private function code( DOMElement $element ) {
		return [
			'blockName' => 'core/code',
			'attrs'     => [
				'content' => $this->get_html( $element ),
			],
			'innerHTML' => '<pre class="wp-block-code">' . $this->get_html( $element ) . '</pre>',
		];
	}

	/**
	 * Image block mapping
	 *
	 * @param DOMElement $element dom element
	 * @return array|string
	 */
	private function image( DOMElement $element ) {
		// Got to have a URL.
		$img_src = $element->getAttribute( 'src' );
		if ( empty( $img_src ) ) {
			return;
		}

		$img_title = $element->getAttribute( 'title' );
		$img_alt   = $this->get_image_alt( $element );
		if ( empty( $img_alt ) && ! empty( $img_title ) ) {
			$img_alt = $img_title;
		}

		// If we're uploading media, download the image into the media library.
		// This will return an existing attachment if the slug already exists.
		if ( false !== $this->options['upload_media'] ) {
			$image = $this->upload_image( $img_src );
		} else {
			$image = [
				'ID'  => 1,
				'url' => $img_src,
			];
		}

		// Failed to upload image? Bail.
		if ( empty( $image ) || is_wp_error( $image ) ) {
			return;
		}

		// Get the image URL after upload conditional & maybe force https
		$img_src = $image['url'];
		if ( false !== $this->options['force_https'] ) {
			$img_src = str_ireplace( 'http://', 'https://', $img_src );
		}

		// Create the image markup
		$img_tag = sprintf(
			'<figure class="wp-block-image"><img src="%1$s" alt="%2$s" class="%3$s" /></figure>',
			esc_url( $img_src ),
			trim( esc_attr( $img_alt ) ),
			'wp-image-' . $image['ID']
		);

		return [
			'blockName' => 'core/image',
			'attrs'     => [
				'id'              => $image['ID'],
				'url'             => $img_src,
				'alt'             => $img_alt,
				'title'           => $img_title,
				'sizeSlug'        => 'full',
				'linkDestination' => 'none',
			],
			'innerHTML' => $img_tag,
		];
	}

	/**
	 * Get HTML content
	 *
	 * @param DOMElement $element dom element
	 * @return string
	 */
	protected function get_html( DOMElement $element ) {
		if ( ! is_a( $element, '\DOMElement' ) ) {
			return $element;
		}

		$inner_html = $element->textContent;
		if ( 0 < $element->childNodes->count() ) {
			$inner_html = $element->ownerDocument->saveHTML( $this->clean_attributes( $element ) );
		}

		return $inner_html;
	}

	/**
	 * Downloads an image from the URL and imports to Media Library.
	 *
	 * @param string $img_src Image URL.
	 * @return array|WP_Error
	 */
	private function upload_image( $img_src ) {
		// Attempt to download the image
		$tmp = download_url( $img_src );
		if ( is_wp_error( $tmp ) ) {
			// If the file were still downloaded make sure its deleted.
			if ( file_exists( $tmp ) ) {
				unlink( $tmp ); // phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.file_ops_unlink
			}
			return $tmp;
		}

		// Prepare the file data for sideloading
		$json = [
			'name'     => basename( $img_src ),
			'type'     => mime_content_type( $tmp ),
			'tmp_name' => $tmp,
			'size'     => filesize( $tmp ),
		];

		// Upload the image
		$attachment_id = media_handle_sideload( $json );

		// Delete the temp file if still exists
		if ( file_exists( $tmp ) ) {
			unlink( $tmp ); // phpcs:ignore WordPressVIPMinimum.Functions.RestrictedFunctions.file_ops_unlink
		}

		// Return error if attachment upload
		if ( is_wp_error( $attachment_id ) ) {
			return $attachment_id;
		}

		$img          = [];
		$img['ID']    = absint( $attachment_id );
		$img['url']   = wp_get_attachment_url( $attachment_id );
		$img['sizes'] = wp_get_attachment_metadata( $attachment_id );

		return $img;
	}

	/**
	 * Get image alt text
	 *
	 * @param DOMElement $element dom element
	 * @param int        $id attachment id
	 * @return string
	 */
	private function get_image_alt( $element, int $id = 0 ) {
		// First try to get alt attribute from DOM node.
		$img_alt = $element->getAttribute( 'alt' );

		// If alt attribute is empty, try to get it from the attachment post meta (if post ID is available)
		if ( empty( $img_alt ) && 0 < intval( $id ) ) {
			$img_alt = get_post_meta( $id, '_wp_attachment_image_alt', true ) ?? '';
		}

		return $img_alt;
	}

	/**
	 * Validate callback
	 *
	 * @param array/function/string $callback callback
	 * @return bool
	 */
	private function is_callback( $callback ) {
		if ( is_callable( $callback ) ) {
			return true;
		}

		if ( is_callable( [ __CLASS__, $callback ] ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Converts a block to Gutenberg Comment Markup.
	 *
	 * Props: https://github.com/WordPress/gutenberg/blob/05b9e95a63ff1b278f9d6cfb1d63a84b54d1cbda/lib/class-experimental-wp-widget-blocks-manager.php#L265
	 *
	 * Block Params:
	 * - blockName - Name of Gutenberg Block
	 * - attrs     - Optional attributes of the Block
	 * - innerHTML - Optional body of the Block
	 *
	 * @param array $block Block data to convert
	 * @return string
	 */
	public function serialize_block( $block ) {
		if ( ! isset( $block['blockName'] ) ) {
			return false;
		}
		$name = $block['blockName'];
		if ( 0 === strpos( $name, 'core/' ) ) {
			$name = substr( $name, strlen( 'core/' ) );
		}
		if ( empty( $block['attrs'] ) ) {
			$opening_tag_suffix = '';
		} else {
			$opening_tag_suffix = ' ' . wp_json_encode( $block['attrs'] );
		}
		if ( empty( $block['innerHTML'] ) ) {
			return sprintf(
				'<!-- wp:%s%s /-->',
				$name,
				$opening_tag_suffix
			);
		} else {
			return sprintf(
				'<!-- wp:%1$s%2$s -->%3$s<!-- /wp:%1$s -->',
				$name,
				$opening_tag_suffix,
				$block['innerHTML']
			);
		}
	}
}