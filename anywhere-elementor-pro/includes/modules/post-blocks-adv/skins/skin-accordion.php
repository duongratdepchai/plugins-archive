<?php
namespace Aepro\Modules\PostBlocksAdv\Skins;

use Aepro\Aepro;
use Aepro\Frontend;
use Aepro\Modules\PostBlocksAdv\Classes\Query;
use Elementor\Core\Files\CSS\Post as Post_CSS;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Plugin;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Skin_Accordion extends Skin_Base {
	//phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
	protected function _register_controls_actions() {
		parent::_register_controls_actions(); // TODO: Change the autogenerated stub
		add_action( 'elementor/element/ae-post-blocks-adv/section_layout/before_section_end', [ $this, 'accordion_controls' ] );
		add_action( 'elementor/element/ae-post-blocks-adv/section_query/after_section_end', [ $this, 'register_style_controls' ] );
	}

	public function get_id() {
		return 'accordion';
	}

	public function get_title() {
		return __( 'Accordion', 'ae-pro' );
	}

	public function register_style_controls() {
		$this->accordion_style_controls();

		$this->start_controls_section(
			'section_toggle_button',
			[
				'label'     => __( 'Toggle Button', 'ae-pro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					$this->get_control_id( 'enable_toggle_button' ) => 'yes',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'           => 'button_typography',
				'label'          => __( 'Typography', 'ae-pro' ),
				'global'         => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
				'selector'       => '{{WRAPPER}} .ae-accordion-tb-wrapper .ae-accordion-toggle-button',
				'fields_options' => [
					'font_family' => [
						'default' => 'Poppins',
					],
					'font_size' => [
						'default' => [
							'unit' => 'px',
							'size' => 18,
						],
					],
					'line_height' => [
						'default' => [
							'unit' => 'px',
							'size' => 18,
						],
					],
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'toggle_button_border',
				'label'    => __( 'Border', 'ae-pro' ),
				'selector' => '{{WRAPPER}} .ae-accordion-tb-wrapper .ae-accordion-toggle-button',
			]
		);

		$this->add_control(
			'toggle_button_border_radius',
			[
				'label'      => __( 'Border Radius', 'ae-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .ae-accordion-tb-wrapper .ae-accordion-toggle-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow:hidden;',
				],
			]
		);

		$this->start_controls_tabs(
			'toggle_button_tabs'
		);
		$this->start_controls_tab(
			'toggle_button_normal',
			[
				'label' => __( 'Normal', 'ae-pro' ),
			]
		);

		$this->add_control(
			'button_color',
			[
				'label'     => __( 'Color Hover', 'ae-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ae-accordion-tb-wrapper .ae-accordion-toggle-button' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_background',
			[
				'label'     => __( 'Background Color', 'ae-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ae-accordion-tb-wrapper .ae-accordion-toggle-button' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'toggle_button_hover',
			[
				'label' => __( 'Hover', 'ae-pro' ),
			]
		);

		$this->add_control(
			'button_color_hover',
			[
				'label'     => 'Color',
				'type'      => Controls_Manager::COLOR,
				'global'    => [
					'default' => Global_Colors::COLOR_ACCENT,
				],
				'selectors' => [
					'{{WRAPPER}} .ae-accordion-tb-wrapper .ae-accordion-toggle-button:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_background_hover',
			[
				'label'     => 'Background Color',
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ae-accordion-tb-wrapper .ae-accordion-toggle-button:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_border_color_hover',
			[
				'label'     => 'Border Color',
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ae-accordion-tb-wrapper .ae-accordion-toggle-button:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'toggle_button_active',
			[
				'label' => __( 'Active', 'ae-pro' ),
			]
		);

		$this->add_control(
			'button_color_active',
			[
				'label'     => 'Color',
				'type'      => Controls_Manager::COLOR,
				'global'    => [
					'default' => Global_Colors::COLOR_ACCENT,
				],
				'selectors' => [
					'{{WRAPPER}} .ae-accordion-tb-wrapper .ae-accordion-toggle-button.active' => 'color: {{VALUE}} !important;',
				],
			]
		);

		$this->add_control(
			'button_background_active',
			[
				'label'     => 'Background Color',
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ae-accordion-tb-wrapper .ae-accordion-toggle-button.active' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_border_color_active',
			[
				'label'     => 'Border Color',
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ae-accordion-tb-wrapper .ae-accordion-toggle-button.active' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_responsive_control(
			'toggle_button_padding',
			[
				'label'      => __( 'Padding', 'ae-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'separator'  => 'before',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}}.elementor-widget-ae-post-blocks-adv .ae-accordion-tb-wrapper .ae-accordion-toggle-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'toggle_button_space',
			[
				'label'     => __( 'Space Between', 'ae-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [
					'size' => 1,
				],
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}}.elementor-widget-ae-post-blocks-adv .ae-accordion-tb-wrapper .ae-accordion-toggle-button.collapse' => 'margin-left:calc({{SIZE}}{{UNIT}}/2);',
					'{{WRAPPER}}.elementor-widget-ae-post-blocks-adv .ae-accordion-tb-wrapper .ae-accordion-toggle-button.expand' => 'margin-right:calc({{SIZE}}{{UNIT}}/2);',
				],
			]
		);

		$this->add_control(
			'toggle_box_heading',
			[
				'label'     => __( 'Toggle Box', 'ae-pro' ),
				'type'      => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'toggle_box_background',
			[
				'label'     => 'Background Color',
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}.elementor-widget-ae-post-blocks-adv .ae-accordion-tb-wrapper' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'toggle_button_align',
			[
				'label'       => __( 'Alignment', 'ae-pro' ),
				'type'        => Controls_Manager::CHOOSE,
				'options'     => [
					'left' => [
						'title' => __( 'Start', 'ae-pro' ),
						'icon'  => 'eicon-h-align-left',
					],
					'center' => [
						'title' => __( 'Middle', 'ae-pro' ),
						'icon'  => 'eicon-h-align-center',
					],
					'right' => [
						'title' => __( 'End', 'ae-pro' ),
						'icon'  => 'eicon-h-align-right',
					],
				],
				'default'     => is_rtl() ? 'right' : 'left',
				'toggle'      => false,
				'label_block' => false,
				'selectors'   => [
					'{{WRAPPER}}.elementor-widget-ae-post-blocks-adv .ae-accordion-tb-wrapper' => 'text-align: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'toggle_box',
				'label'    => __( 'Border', 'ae-pro' ),
				'selector' => '{{WRAPPER}}.elementor-widget-ae-post-blocks-adv .ae-accordion-tb-wrapper',
			]
		);

		$this->add_responsive_control(
			'toggle_box_padding',
			[
				'label'      => __( 'Padding', 'ae-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'separator'  => 'before',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}}.elementor-widget-ae-post-blocks-adv .ae-accordion-tb-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'toggle_box_space',
			[
				'label'     => __( 'Space', 'ae-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [
					'size' => 1,
				],
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}}.elementor-widget-ae-post-blocks-adv .ae-accordion-tb-wrapper' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->end_controls_section();
	}

	public function render() {
		// TODO: Implement render() method.
		$this->if_layout_is_blank();

		$settings = $this->parent->get_settings_for_display();
		// get posts
		$query = new Query( $settings );
		$posts = $query->get_posts();

		//Multi Lingual templates
		$settings['layout'] = apply_filters( 'wpml_object_id', $settings['layout'], 'ae_global_templates', true );

		// Checked for No Post Message.

		$layout = $settings['layout'];

		$tab_icon        = $this->get_instance_value( 'selected_icon' );
		$tab_active_icon = $this->get_instance_value( 'selected_active_icon' );
		$toggle_button   = $this->get_instance_value( 'enable_toggle_button' );

		$has_icon = ( ! empty( $tab_icon['value'] ) );

		$this->parent->add_render_attribute( 'outer-wrapper', 'class', 'ae-outer-wrapper' );

		$this->parent->add_render_attribute( 'outer-wrapper', 'class', 'ae-height-100' );
		// Collection Attributes
		$transition_speed = $this->get_instance_value( 'accordion_transition_speed' );
		$this->parent->add_render_attribute( 'collection', 'class', 'ae-post-collection' );

		//WooCommerce Sales Badge
		if ( isset( $settings['sale_badge_switcher'] ) && $settings['sale_badge_switcher'] === 'yes' ) {
			$this->parent->add_render_attribute( 'outer-wrapper', 'class', 'sale-badge-' . $settings['sale_badge_switcher'] );
		}

		$this->parent->add_render_attribute(
			'collection',
			[
				'class'                 => 'elementor-accordion ae-accordion',
				'role'                  => 'tablist',
				'data-transition-speed' => $transition_speed['size'],
			]
		);

		?>
		<?php
		if ( ( $posts->have_posts() ) || ! empty( $settings['no_posts_message'] ) ) {
			$this->parent->get_widget_title_html();
		}

		if ( ! $posts->have_posts() ) {
			echo $this->ae_no_post_message( $settings );
			return;
		}
		?>
		<div <?php echo $this->parent->get_render_attribute_string( 'outer-wrapper' ); ?> >

			<?php

			$seq = 0;

			$css_file = Post_CSS::create( $layout );

			$css_file->enqueue();

			global $post;
			// Securing Current post of Parent Query
			$prev_post = get_post();

			global $wp_query;
			$old_queried_object = $wp_query->queried_object;
			?>

			<div <?php echo $this->parent->get_render_attribute_string( 'collection' ); ?> >
			<?php
			if ( $toggle_button === 'yes' ) {
				?>
						<div class="ae-accordion-tb-wrapper">
							<button class="ae-accordion-toggle-button expand" data-role="expand"><?php echo $this->get_instance_value( 'expand_button_text' ); ?></button>
							<span> <?php echo $this->get_instance_value( 'toggle_button_separator' ); ?> </span>
							<button class="ae-accordion-toggle-button collapse" data-role="collapse"><?php echo $this->get_instance_value( 'collapse_button_text' ); ?></button>
						</div>
					<?php
			}
			?>
			<?php
				Frontend::$_in_repeater_block = true;

				$tab_count       = 0;
				$accordion_state = $this->get_instance_value( 'accordion_state' );
				$index           = wp_rand();
			$wid = $this->parent->get_id();
			while ( $posts->have_posts() ) {

				$posts->the_post();
				Frontend::$_ae_post_block = get_the_ID();
				$tab_no                   = $index + 1;
				$tab_count++;
				$tab_title = '';

				$title_class   = 'ae-tab-title ae-post-blocks-adv-accordion';
				$content_class = 'elementor-clearfix ae-tab-content ae-post-blocks-adv-accordion';

				if ( $tab_count === 1 && $accordion_state === 'default' ) {
					$title_class   = $title_class . ' ae-active';
					$content_class = $content_class . ' ae-active';
				} elseif ( $accordion_state === 'all_open' ) {
					$title_class   = $title_class . ' ae-active';
					$content_class = $content_class . ' ae-active';
				} elseif ( $accordion_state === 'open_specific' ) {
					$specific_tab = $this->get_instance_value( 'specific_tab' );
					if ( $tab_count === $specific_tab ) {
						$title_class   = $title_class . ' ae-active';
						$content_class = $content_class . ' ae-active';
					}
				}

				$this->parent->set_render_attribute(
					'ae-post-blocks-adv-accordion-title',
					[
						'id'            => 'ae-tab-title-' . $tab_no . $tab_count,
						't_id'          => $tab_no . $tab_count,
						'class'         => $title_class,
						'data-tab'      => $tab_count,
						'role'          => 'tab',
						'aria-controls' => 'ae-tab-content-' . $tab_no . $tab_count,
					]
				);

				$this->parent->set_render_attribute(
					'ae-post-blocks-adv-accordion-content',
					[
						'id'              => 'ae-tab-content-' . $tab_no . $tab_count,
						't_id'            => $tab_no . $tab_count,
						'class'           => $content_class,
						'data-tab'        => $tab_count,
						'role'            => 'tabpanel',
						'aria-labelledby' => 'ae-tab-title-' . $tab_no . $tab_count,
					]
				);

				if ( $this->get_instance_value( 'enable_url_hashtag' ) === 'yes' ) {
					$data_hashtag = '';
					$hashtag_type = $this->get_instance_value( 'fragment_type' );
					switch ( $hashtag_type ) {
						case 'post_id':
									$data_hashtag = 'post-' . get_the_ID();
							break;
						case 'post_slug':
									$data_hashtag = get_post_field( 'post_name' );
							break;
						case 'custom_field':
							if ( $this->get_instance_value( 'fragment_custom_field' ) !== '' ) {
										$data_hashtag = get_post_meta( get_the_id(), $this->get_instance_value( 'fragment_custom_field' ), true );
							}
							break;
						default:
									$data_hashtag = 'tab-' . $tab_count;
					}

					if ( $data_hashtag !== '' ) {
						$this->parent->set_render_attribute(
							'ae-post-blocks-adv-accordion-title',
							[
								'data-hashtag' => $data_hashtag,
							]
						);
					}
				}

				?>
						<div class="ae-accordion-item">
							<<?php echo $settings['title_html_tag']; ?> <?php echo $this->parent->get_render_attribute_string( 'ae-post-blocks-adv-accordion-title' ); ?>>
					<?php if ( $has_icon ) : ?>
								<span class="ae-accordion-icon ae-accordion-icon-<?php echo esc_attr( $this->get_instance_value( 'icon_align' ) ); ?>" aria-hidden="true">
									<span class="ae-accordion-icon-closed"><?php Icons_Manager::render_icon( $tab_icon ); ?></span>
									<span class="ae-accordion-icon-opened"><?php Icons_Manager::render_icon( $tab_active_icon ); ?></span>
								</span>
							<?php endif; ?>
					<?php
					if ( $settings['tab_title'] === 'post_title' ) {
							$tab_title = get_the_title();
					} else {
						if ( \Aepro\Plugin::show_acf() && is_plugin_active( 'pods/init.php' ) ) {
							if($settings['relationship_type'] === 'pods'){
								$tab_title = get_post_meta( get_the_ID(), $settings['tab_title_custom_field'], true );
							}else {
								$tab_title = get_field( $settings['tab_title_custom_field'], get_the_ID() );
							}
						} elseif ( is_plugin_active( 'pods/init.php' ) ) {
							$tab_title = get_post_meta( get_the_ID(), $settings['tab_title_custom_field'], true );
						}else{
							$tab_title = get_field( $settings['tab_title_custom_field'], get_the_ID() );
						}
					}
					?>
							<a href="#"><?php echo $tab_title; ?></a>
							</<?php echo $settings['title_html_tag']; ?>>
							<div <?php echo $this->parent->get_render_attribute_string( 'ae-post-blocks-adv-accordion-content' ); ?>>
						<?php
						$layout                   = $this->get_layout( $seq, $settings );
						$wp_query->queried_object = $post;
						$this->render_item( $layout, $wid );
						?>
							</div>
						</div>

				<?php
			}
				$wp_query->queried_object = $old_queried_object;
				Frontend::$_ae_post_block = 0;
				//phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
				$post = $prev_post;
				setup_postdata( $post );
				Frontend::$_in_repeater_block = false;
			?>
			</div>
		</div> <!-- end .ae-outer-wrapper -->
		<?php
	}

	public function accordion_style_controls() {

		$this->start_controls_section(
			'section_accordion_style',
			[
				'label' => __( 'Accordion', 'ae-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'           => 'accordion_border',
				'label'          => __( 'Border', 'ae-pro' ),
				'selector'       => '{{WRAPPER}} .ae-accordion-item .ae-tab-title',
				'fields_options' => [
					'border' => [
						'default' => 'solid',
					],
					'width'  => [
						'default' => [
							'top'    => 1,
							'right'  => 1,
							'bottom' => 1,
							'left'   => 1,
						],
					],
					'color'  => [
						'default' => '#D4D4D4',
					],
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'accordion_title_style',
			[
				'label' => __( 'Title', 'ae-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'title_background',
			[
				'label'     => __( 'Background Color', 'ae-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ae-accordion .ae-tab-title' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'title_background_active',
			[
				'label'     => __( 'Background Color Active', 'ae-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ae-accordion .ae-tab-title.ae-active' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'title_color',
			[
				'label'     => __( 'Color', 'ae-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ae-accordion .ae-tab-title' => 'color: {{VALUE}};',
				],
				'global'    => [
					'default' => Global_Colors::COLOR_PRIMARY,
				],
			]
		);

		$this->add_control(
			'tab_active_color',
			[
				'label'     => __( 'Active Color', 'ae-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ae-accordion .ae-tab-title.ae-active' => 'color: {{VALUE}};',
				],
				'global'    => [
					'default' => Global_Colors::COLOR_ACCENT,
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'selector' => '{{WRAPPER}} .ae-accordion .ae-tab-title',
				'global'   => [
					'default' => Global_Typography::TYPOGRAPHY_PRIMARY,
				],
			]
		);

		$this->add_responsive_control(
			'title_padding',
			[
				'label'      => __( 'Padding', 'ae-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .ae-accordion .ae-tab-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'title_align',
			[
				'label'       => __( 'Alignment', 'ae-pro' ),
				'type'        => Controls_Manager::CHOOSE,
				'options'     => [
					'left' => [
						'title' => __( 'Start', 'ae-pro' ),
						'icon'  => 'eicon-h-align-left',
					],
					'center' => [
						'title' => __( 'Middle', 'ae-pro' ),
						'icon'  => 'eicon-h-align-center',
					],
					'right' => [
						'title' => __( 'End', 'ae-pro' ),
						'icon'  => 'eicon-h-align-right',
					],
				],
				'default'     => is_rtl() ? 'right' : 'left',
				'toggle'      => false,
				'label_block' => false,
				'selectors'   => [
					'{{WRAPPER}} .ae-tab-title' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'accordion_icon_style',
			[
				'label' => __( 'Icon', 'ae-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'icon_align',
			[
				'label'       => __( 'Alignment', 'ae-pro' ),
				'type'        => Controls_Manager::CHOOSE,
				'options'     => [
					'left' => [
						'title' => __( 'Start', 'ae-pro' ),
						'icon'  => 'eicon-h-align-left',
					],
					'right' => [
						'title' => __( 'End', 'ae-pro' ),
						'icon'  => 'eicon-h-align-right',
					],
				],
				'default'     => is_rtl() ? 'right' : 'left',
				'toggle'      => false,
				'label_block' => false,
			]
		);

		$this->add_control(
			'icon_color',
			[
				'label'     => __( 'Color', 'ae-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ae-accordion .ae-tab-title .ae-accordion-icon i:before' => 'color: {{VALUE}};',
					'{{WRAPPER}} .ae-accordion .ae-tab-title .ae-accordion-icon svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'icon_active_color',
			[
				'label'     => __( 'Active Color', 'ae-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ae-accordion .ae-tab-title.ae-active .ae-accordion-icon i:before' => 'color: {{VALUE}};',
					'{{WRAPPER}} .ae-accordion .ae-tab-title.ae-active .ae-accordion-icon svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'icon_space',
			[
				'label'     => __( 'Spacing', 'ae-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .ae-accordion .ae-accordion-icon.ae-accordion-icon-left' => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ae-accordion .ae-accordion-icon.ae-accordion-icon-right' => 'margin-left: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'accordion_content_style',
			[
				'label' => __( 'Content', 'ae-pro' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'content_background_color',
			[
				'label'     => __( 'Background', 'ae-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ae-accordion .ae-tab-content' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'           => 'content_border',
				'label'          => __( 'Border', 'ae-pro' ),
				'selector'       => '{{WRAPPER}} .ae-tab-content',
				'fields_options' => [
					'border' => [
						'default' => 'solid',
					],
					'width'  => [
						'default' => [
							'top'    => 0,
							'right'  => 0,
							'bottom' => 0,
							'left'   => 0,
						],
					],
					'color'  => [
						'default' => '#D4D4D4',
					],
				],
			]
		);

		$this->add_responsive_control(
			'content_padding',
			[
				'label'      => __( 'Padding', 'ae-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'separator'  => 'before',
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .ae-accordion .ae-tab-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();
	}

	public function accordion_controls( Widget_Base $widget ) {

		$this->parent = $widget;

		$this->add_control(
			'selected_icon',
			[
				'label'            => __( 'Icon', 'ae-pro' ),
				'type'             => Controls_Manager::ICONS,
				'separator'        => 'before',
				'fa4compatibility' => 'icon',
				'default'          => [
					'value'   => 'fas fa-plus',
					'library' => 'fa-solid',
				],
			]
		);

		$this->add_control(
			'selected_active_icon',
			[
				'label'            => __( 'Active Icon', 'ae-pro' ),
				'type'             => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon_active',
				'default'          => [
					'value'   => 'fas fa-minus',
					'library' => 'fa-solid',
				],
			]
		);

		$this->add_control(
			'accordion_transition_speed',
			[
				'label'   => __( 'Transition Speed', 'ae-pro' ),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'unit' => 'px',
					'size' => 300,
				],
				'range'   => [
					'px' => [
						'min'  => 300,
						'max'  => 1000,
						'step' => 100,
					],
				],

			]
		);

		$this->add_control(
			'accordion_state',
			[
				'label'              => __( 'State on Load', 'ae-pro' ),
				'type'               => Controls_Manager::SELECT,
				'options'            => [
					'default'       => __( 'Default', 'ae-pro' ),
					'all_open'      => __( 'All Open', 'ae-pro' ),
					'all_closed'    => __( 'All Close', 'ae-pro' ),
					'open_specific' => __( 'Open Specific', 'ae-pro' ),
				],
				'default'            => 'default',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'specific_tab',
			[
				'label'     => __( 'Specific Tab', 'ae-pro' ),
				'type'      => Controls_Manager::NUMBER,
				'default'   => '2',
				'min'       => 1,
				'max'       => 100,
				'condition' => [
					$this->get_control_id( 'accordion_state' ) => [ 'open_specific' ],
				],
			]
		);

		$this->add_control(
			'enable_url_hashtag',
			[
				'label'        => __( 'Enable Hashtag', 'ae-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '',
				'label_on'     => __( 'Yes', 'ae-pro' ),
				'label_off'    => __( 'No', 'ae-pro' ),
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'fragment_type',
			[
				'label'     => __( 'Fragment', 'ae-pro' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => [
					'default'      => __( 'Default', 'ae-pro' ),
					'post_id'      => __( 'Post ID', 'ae-pro' ),
					'post_slug'    => __( 'Post Slug', 'ae-pro' ),
					'custom_field' => __( 'Custom Field', 'ae-pro' ),
				],
				'default'   => 'default',
				'condition' => [
					$this->get_control_id( 'enable_url_hashtag' ) => 'yes',
				],
			]
		);

		$this->add_control(
			'fragment_custom_field',
			[
				'label'       => __( 'Fragment Custom Field', 'ae-pro' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => __( 'Custom Field', 'ae-pro' ),
				'condition'   => [
					$this->get_control_id( 'fragment_type' ) => 'custom_field',
					$this->get_control_id( 'enable_url_hashtag' ) => 'yes',
				],
			]
		);

		$this->add_control(
			'enable_toggle_button',
			[
				'label'        => __( 'Enable Toggle Button', 'ae-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '',
				'label_on'     => __( 'Yes', 'ae-pro' ),
				'label_off'    => __( 'No', 'ae-pro' ),
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'expand_button_text',
			[
				'label'     => __( 'Expend Text', 'ae-pro' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Expand All', 'ae-pro' ),
				'condition' => [
					$this->get_control_id( 'enable_toggle_button' ) => 'yes',
				],
			]
		);
		$this->add_control(
			'collapse_button_text',
			[
				'label'     => __( 'Collapse Text', 'ae-pro' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( 'Collapse All', 'ae-pro' ),
				'condition' => [
					$this->get_control_id( 'enable_toggle_button' ) => 'yes',
				],
			]
		);

		$this->add_control(
			'toggle_button_separator',
			[
				'label'     => __( 'Separator', 'ae-pro' ),
				'type'      => Controls_Manager::TEXT,
				'default'   => __( ' | ', 'ae-pro' ),
				'condition' => [
					$this->get_control_id( 'enable_toggle_button' ) => 'yes',
				],
			]
		);
	}
}
