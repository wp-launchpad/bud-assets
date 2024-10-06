<?php

namespace LaunchpadBudAssets\Builders;

class AvailabilityQuery {

	/**
	 * Post-types enabled.
	 *
	 * @var string[]
	 */
	protected $post_types = [];

	protected $shortcodes = [];

	protected $blocks = [];

	protected $templates = [];

	protected $taxonomy = [];

	public function with_post_type(string $post_type): self {
		$this->post_types []= $post_type;
		return $this;
	}

	public function with_shortcode(string $shortcode): self {
		$this->shortcodes []= $shortcode;
		return $this;
	}

	public function with_block(string $block): self {
		$this->blocks []= $block;
		return $this;
	}

	public function with_template(string $template): self {
		$this->templates []= $template;
		return $this;
	}

	public function with_taxonomy(string $taxonomy): self {
		$this->taxonomy []= $taxonomy;
		return $this;
	}

	public function applies(): bool {
		global $post;

		foreach ($this->taxonomy as $taxonomy) {
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

	protected function has_reusable_block( $block_name, $id = false ){
		$id = (!$id) ? get_the_ID() : $id;
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