<?php

namespace Aepro\Modules\AcfFields\Skins;

use Aepro\Aepro;
use Aepro\Modules\AcfFields;
use Aepro\Classes\AcfMaster;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Aepro\Base\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Plugin as EPlugin;
use Elementor\Group_Control_Image_Size;

class Skin_Text extends Skin_Base {

	public function get_id() {
		return 'text';
	}

	public function get_title() {
		return __( 'Text', 'ae-pro' );
	}
	// phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
	protected function _register_controls_actions() {

		parent::_register_controls_actions();

		
		add_action( 'elementor/element/ae-acf/general/after_section_end', [ $this, 'register_style_controls' ] );
		add_action( 'elementor/element/ae-acf/general/after_section_end', [ $this, 'register_fallback' ] );
		add_action( 'elementor/element/ae-acf/text_general_style/after_section_end', [ $this, 'register_fallback_style' ] );
	}


	public function register_controls( Widget_Base $widget ) {

		$this->parent = $widget;

		parent::register_text_controls();
	}
	public function register_fallback() {

		if(!$this->load_skin_controls(['text', 'text-area', 'wysiwyg' ])){
			return true;
		}
		
		$this->register_fallback_controls();
	}
	public function register_fallback_style() {

		if(!$this->load_skin_controls(['text', 'text-area', 'wysiwyg' ])){
			return true;
		}

		$this->fallback_style_controls();
	}

	public function render() {

		$settings   = $this->parent->get_settings();
		$post       = Aepro::$_helper->get_demo_post_data();
		$field_args = [
			'field_type'   => $settings['field_type'],
			'is_sub_field' => $settings['is_sub_field'],
		];

		$accepted_parent_fields = [ 'repeater', 'group', 'flexible' ];
		//phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
		if ( in_array( $settings['is_sub_field'], $accepted_parent_fields ) ) {

			switch ( $settings['is_sub_field'] ) {

				case 'flexible':
					$field_args['field_name']                     = $settings['flex_sub_field'];
									$field_args['flexible_field'] = $settings['flexible_field'];
					break;

				case 'repeater':
					$field_args['field_name']                   = $settings['repeater_sub_field'];
									$field_args['parent_field'] = $settings['repeater_field'];
					break;

				case 'group':
					$field_args['field_name']                   = $settings['field_name'];
									$field_args['parent_field'] = $settings['parent_field'];
					break;
			}
		} else {
			$field_args['field_name'] = $settings['field_name'];
		}

		$title_raw = AcfMaster::instance()->get_field_value( $field_args );
		if ( is_array( $title_raw ) || is_object( $title_raw ) ) {
			return;
		}
		$placeholder  = $this->get_instance_value( 'placeholder' );
		$before_text  = $this->get_instance_value( 'prefix' );
		$after_text   = $this->get_instance_value( 'suffix' );
		$links_to     = $this->get_instance_value( 'links_to' );
		$link_new_tab = $this->get_instance_value( 'link_new_tab' );
		$link         = '';

		if ( EPlugin::$instance->editor->is_edit_mode() ) {
			//phpcs:ignore WordPress.PHP.StrictComparisons.LooseComparison
			if ( $this->get_instance_value( 'preview_fallback' ) == 'yes' ) {
				$this->render_fallback_content( $settings );
			}
		}

		if ( $title_raw === '' && $placeholder === '' ) {
			//phpcs:ignore WordPress.PHP.StrictComparisons.LooseComparison
			if ( $this->get_instance_value( 'enable_fallback' ) != 'yes' ) {
				return;
			} else {
				$this->render_fallback_content( $settings );
				return;
			}
		} elseif ( $title_raw === '' & $placeholder !== '' ) {
			$title = $placeholder;
		} else {
			//phpcs:ignore WordPress.PHP.StrictComparisons.LooseComparison
			if ( $this->get_instance_value( 'strip_text' ) == 'yes' ) {
				$strip_mode   = $this->get_instance_value( 'strip_mode' );
				$strip_size   = $this->get_instance_value( 'strip_size' );
				$strip_append = $this->get_instance_value( 'strip_append' );
				if ( $strip_mode === 'word' ) {
					$title_raw = wp_trim_words( $title_raw, $strip_size, $strip_append );
				} else {
					$title_raw = Aepro::$_helper->ae_trim_letters( $title_raw, 0, $strip_size, $strip_append );
				}
			}
			if($before_text != ''){
				$title_raw = '<span class="ae-prefix">' . $before_text . '</span>' . $title_raw;
			}
			if($after_text != ''){
				$title_raw = $title_raw . '<span class="ae-suffix">' . $after_text . '</span>';
			}

			$title = $title_raw;
			//$title = '<span class="ae-prefix">' . $before_text . '</span>' . $title_raw . '<span class="ae-suffix">' . $after_text . '</span>';
		}

		// Process Content
		$title = $this->process_content( $title );

		if ( $links_to !== '' ) {

			switch ( $links_to ) {

				case 'post':
					$link = get_permalink( $post->ID );
					break;

				case 'static':
					$link = $this->get_instance_value( 'link_url' );
					break;

				case 'custom_field':
					$link_cf                                      = $this->get_instance_value( 'link_cf' );
										$field_args['field_name'] = $link_cf;
										$link                     = AcfMaster::instance()->get_field_value( $field_args );

					break;

			}
		}

		$this->parent->add_render_attribute( 'wrapper-class', 'class', 'ae-acf-wrapper' );
		$this->parent->add_render_attribute( 'title-class', 'class', 'ae-acf-content-wrapper' );

		$html_tag = $this->get_instance_value( 'html_tag' );

		if ( $link !== '' ) {

			$this->parent->add_render_attribute( 'anchor', 'title', $title_raw );
			$this->parent->add_render_attribute( 'anchor', 'href', $link );
			//phpcs:ignore WordPress.PHP.StrictComparisons.LooseComparison
			if ( $link_new_tab == 'yes' ) {
				$this->parent->add_render_attribute( 'anchor', 'target', '_blank' );
			}

			$title_html = '<a ' . $this->parent->get_render_attribute_string( 'anchor' ) . '>' . $title . '</a>';
		} else {

			$title_html = $title;
		}

		$html = sprintf( '<%1$s itemprop="name" %2$s>%3$s</%1$s>', $html_tag, $this->parent->get_render_attribute_string( 'title-class' ), $title_html );
		if ( $title === '' ) {
			$this->parent->add_render_attribute( 'wrapper-class', 'class', 'ae-hide' );
		}
		?>
		<div <?php echo $this->parent->get_render_attribute_string( 'wrapper-class' ); ?>>
		<?php
		echo $html;
		?>
		</div>
		<?php
	}

	public function process_content( $content ) {
		/** This filter is documented in wp-includes/widgets/class-wp-widget-text.php */
		$content = apply_filters( 'widget_text', $content, $this->parent->get_settings() );

		$content = shortcode_unautop( $content );
		$content = do_shortcode( $content );
		$content = wptexturize( $content );

		if ( $GLOBALS['wp_embed'] instanceof \WP_Embed ) {
			$content = $GLOBALS['wp_embed']->autoembed( $content );
		}

		return $content;
	}

	public function register_style_controls() {

		if(!$this->load_skin_controls(['text', 'text-area', 'wysiwyg' ])){
			return true;
		}
		
		$this->start_controls_section(
			'general_style',
			[
				'label' => __( 'General', 'ae-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'typography',
				'global'   => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
				'selector' => '{{WRAPPER}} .ae-acf-content-wrapper, {{WRAPPER}} .ae-acf-content-wrapper a',
			]
		);

			$this->start_controls_tabs( 'style' );

				$this->start_controls_tab(
					'normal_style',
					[
						'label' => __( 'Normal', 'ae-pro' ),
					]
				);

				$this->add_control(
					'color',
					[
						'label'     => __( 'Color', 'ae-pro' ),
						'type'      => Controls_Manager::COLOR,
						'global'    => [
							'default' => Global_Colors::COLOR_TEXT,
						],
						'selectors' => [
							'{{WRAPPER}} .ae-acf-content-wrapper, {{WRAPPER}} .ae-acf-content-wrapper a' => 'color:{{VALUE}}',
						],
					]
				);

				$this->add_control(
					'bg_color',
					[
						'label'     => __( 'Background Color', 'ae-pro' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ae-acf-content-wrapper' => 'background:{{VALUE}}',
						],
					]
				);

				$this->add_group_control(
					Group_Control_Border::get_type(),
					[
						'name'     => 'border',
						'label'    => __( 'Border', 'ae-pro' ),
						'selector' => '{{WRAPPER}} .ae-acf-content-wrapper',
					]
				);

				$this->add_control(
					'border_radius',
					[
						'label'      => __( 'Border Radius', 'ae-pro' ),
						'type'       => Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%' ],
						'selectors'  => [
							'{{WRAPPER}} .ae-acf-content-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);

				$this->add_group_control(
					Group_Control_Box_Shadow::get_type(),
					[
						'name'     => 'box_shadow',
						'label'    => __( 'Shadow', 'ae-pro' ),
						'selector' => '{{WRAPPER}} .ae-acf-content-wrapper',
					]
				);

				$this->end_controls_tab();  // Normal Tab End

				$this->start_controls_tab(
					'hover_style',
					[
						'label' => __( 'Hover', 'ae-pro' ),
					]
				);

				$this->add_control(
					'color_hover',
					[
						'label'     => __( 'Color', 'ae-pro' ),
						'type'      => Controls_Manager::COLOR,
						'global'    => [
							'default' => Global_Colors::COLOR_TEXT,
						],
						'selectors' => [
							'{{WRAPPER}} .ae-acf-content-wrapper:hover, {{WRAPPER}} .ae-acf-content-wrapper:hover a' => 'color:{{VALUE}}',
						],
					]
				);

				$this->add_control(
					'bg_color_hover',
					[
						'label'     => __( 'Background Color', 'ae-pro' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ae-acf-content-wrapper:hover' => 'background:{{VALUE}}',
						],
					]
				);

				$this->add_control(
					'border_color_hover',
					[
						'label'     => __( 'Border Color', 'ae-pro' ),
						'type'      => Controls_Manager::COLOR,
						'global'    => [
							'default' => Global_Colors::COLOR_TEXT,
						],
						'selectors' => [
							'{{WRAPPER}} .ae-acf-content-wrapper:hover' => 'border-color:{{VALUE}}',
						],
					]
				);

				$this->add_responsive_control(
					'border_radius_hover',
					[
						'label'     => __( 'Border Radius', 'ae-pro' ),
						'type'      => Controls_Manager::DIMENSIONS,
						'selectors' => [
							'{{WRAPPER}} .ae-acf-content-wrapper:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],

					]
				);

				$this->add_group_control(
					Group_Control_Box_Shadow::get_type(),
					[
						'name'     => 'hover_box_shadow',
						'label'    => __( 'Shadow', 'ae-pro' ),
						'selector' => '{{WRAPPER}} .ae-acf-content-wrapper:hover',
					]
				);

				$this->end_controls_tab();

			$this->end_controls_tabs();

			$this->add_responsive_control(
				'padding',
				[
					'label'     => __( 'Padding', 'ae-pro' ),
					'type'      => Controls_Manager::DIMENSIONS,
					'separator' => 'before',
					'selectors' => [
						'{{WRAPPER}} .ae-acf-content-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],

				]
			);

			$this->add_responsive_control(
				'margin',
				[
					'label'     => __( 'Margin', 'ae-pro' ),
					'type'      => Controls_Manager::DIMENSIONS,
					'selectors' => [
						'{{WRAPPER}} .ae-acf-content-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],

				]
			);

		$this->end_controls_section();
	}


}
