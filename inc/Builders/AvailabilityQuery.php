<?php

namespace LaunchpadBudAssets\Builders;

class AvailabilityQuery {

	/**
	 * Post-types enabled.
	 *
	 * @var string[]
	 */
	protected $post_types = [];

	/**
	 * Shortcodes the query is enabled on.
	 *
	 * @var string[]
	 */
	protected $shortcodes = [];

	/**
	 * Blocks the query is enabled on.
	 *
	 * @var string[]
	 */
	protected $blocks = [];

	/**
	 * Templates the query is enabled on.
	 *
	 * @var string[]
	 */
	protected $templates = [];

	/**
	 * Taxonomies the query is enabled on.
	 *
	 * @var string[]
	 */
	protected $taxonomies = [];

	/**
	 * Enable the query on the post-type.
	 *
	 * @param string $post_type Post-type to enable the query on.
	 *
	 * @return $this
	 */
	public function with_post_type(string $post_type): self {
		$this->post_types []= $post_type;
		return $this;
	}

	/**
	 * Enable the query on the shortcode.
	 *
	 * @param string $shortcode Shortcode to enable the query on.
	 *
	 * @return $this
	 */
	public function with_shortcode(string $shortcode): self {
		$this->shortcodes []= $shortcode;
		return $this;
	}

	/**
	 * Enable the query on the block.
	 *
	 * @param string $block Block to enable the query on.
	 *
	 * @return $this
	 */
	public function with_block(string $block): self {
		$this->blocks []= $block;
		return $this;
	}

	/**
	 * Enable the query on the template.
	 *
	 * @param string $template Template to enable the query on.
	 *
	 * @return $this
	 */
	public function with_template(string $template): self {
		$this->templates []= $template;
		return $this;
	}

	/**
	 * Enable the query on the taxonomy.
	 *
	 * @param string $taxonomy Taxonomy to enable the query on.
	 *
	 * @return $this
	 */
	public function with_taxonomy(string $taxonomy): self {
		$this->taxonomies [] = $taxonomy;
		return $this;
	}

	/**
	 * Applies the query to return if it should be enabled.
	 *
	 * @return bool
	 */
	public function applies(): bool {
		global $post;

		foreach ($this->taxonomies as $taxonomy) {
			if ( is_tax( $taxonomy ) ) {
   				return true;
			}
		}

		foreach ($this->templates as $template) {
			if( is_page_template( $template ) ) {
				return true;
			}
		}

		foreach ($this->post_types as $post_type) {
			if(is_singular( $post_type )) {
				return true;
			}
		}

		foreach ($this->blocks as $block) {
			if($this->has_reusable_block($block)) {
				return true;
			}
		}

		if(! is_a( $post, 'WP_Post' ) ) {
			return false;
		}

		foreach ($this->shortcodes as $shortcode) {
			if( has_shortcode( $post->post_content, $shortcode) ) {
				return true;
			}
		}


		return false;
	}

	/**
	 * Check if the block is used.
	 *
	 * @param string $block_name Name from the block.
	 *
	 * @return bool
	 */
	protected function has_reusable_block( string $block_name ){
		$id = get_the_ID();

		if( $id ){
			if ( has_block( 'block', $id ) ){
				// Check reusable blocks
				$content = get_post_field( 'post_content', $id );
				$blocks = parse_blocks( $content );

				if ( ! is_array( $blocks ) || empty( $blocks ) ) {
					return false;
				}

				foreach ( $blocks as $block ) {
					if ( $block['blockName'] === 'core/block' && ! empty( $block['attrs']['ref'] ) ) {
						if( has_block( $block_name, $block['attrs']['ref'] ) ){
							return true;
						}
					}
				}
			}
		}

		return false;
	}
}