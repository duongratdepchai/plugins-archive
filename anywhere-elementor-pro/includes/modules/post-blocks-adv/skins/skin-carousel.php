<?php
namespace Aepro\Modules\PostBlocksAdv\Skins;

use Aepro\Aepro;
use Aepro\Frontend;
use Aepro\Modules\PostBlocksAdv\Classes\Query;
use Elementor\Core\Files\CSS\Post as Post_CSS;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Icons_Manager;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Aepro\Helper;
use Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Skin_Carousel extends Skin_Base {
	//phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
	protected function _register_controls_actions() {
		parent::_register_controls_actions(); // TODO: Change the autogenerated stub
		add_action( 'elementor/element/ae-post-blocks-adv/section_query/after_section_end', [ $this, 'register_controls' ] );
		add_action( 'elementor/element/ae-post-blocks-adv/layout_style/after_section_end', [ $this, 'register_style_controls' ] );
	}

	public function get_id() {
		return 'carousel';
	}

	public function get_title() {
		return __( 'Carousel', 'ae-pro' );
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

		$swiper_data = $this->get_swiper_data();

		$this->parent->add_render_attribute( 'outer-wrapper', 'class', 'ae-swiper-outer-wrapper' );
		$this->parent->add_render_attribute( 'outer-wrapper', 'class', 'ae-carousel-yes' );
		if ( $this->get_instance_value( 'arrows_layout' ) === 'inside' ) {
			$this->parent->add_render_attribute( 'outer-wrapper', 'class', 'ae-hpos-' . $this->get_instance_value( 'arrow_horizontal_position' ) );
			$this->parent->add_render_attribute( 'outer-wrapper', 'class', 'ae-vpos-' . $this->get_instance_value( 'arrow_vertical_position' ) );
		}
		$this->parent->add_render_attribute( 'outer-wrapper', 'data-swiper-settings', wp_json_encode( $swiper_data ) );

		// Collection Attributes
		$this->parent->add_render_attribute( 'collection', 'class', 'ae-post-collection' );
		$this->parent->add_render_attribute( 'collection', 'class', 'ae-swiper-container swiper-container' );

		//Swiper List Wrapper Attributes
		$this->parent->add_render_attribute( 'post-list-wrapper', 'class', 'ae-post-widget-wrapper ae-swiper-wrapper swiper-wrapper' );

		//WooCommerce Sales Badge
		if ( isset( $settings['sale_badge_switcher'] ) && $settings['sale_badge_switcher'] === 'yes' ) {
			$this->parent->add_render_attribute( 'outer-wrapper', 'class', 'sale-badge-' . $settings['sale_badge_switcher'] );
		}
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
				<div <?php echo $this->parent->get_render_attribute_string( 'post-list-wrapper' ); ?> >
					<?php
					$wid = $this->parent->get_id();
					while ( $posts->have_posts() ) {

						$seq++;

						$posts->the_post();
						Frontend::$_ae_post_block = get_the_ID();
						$layout                   = $this->get_layout( $seq, $settings );
						$wp_query->queried_object = $post;
						$this->render_item( $layout, $wid );
					}
					$wp_query->queried_object = $old_queried_object;
					Frontend::$_ae_post_block = 0;
					//phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
					$post = $prev_post;
					setup_postdata( $post );
					?>
				</div>

				<?php

				$this->get_swiper_pagination();
				/** Arrows Inside **/
				if ( $this->get_instance_value( 'navigation_button' ) === 'yes' && $this->get_instance_value( 'arrows_layout' ) === 'inside' ) {
					$this->get_swiper_arrows();
				}

				$this->get_swiper_scrolbar();
				?>

			</div> <!-- end collection -->
			<?php
			if ( $this->get_instance_value( 'navigation_button' ) === 'yes' && $this->get_instance_value( 'arrows_layout' ) === 'outside' ) {
				/** Arrows Outside **/
				$this->get_swiper_arrows();
			}
			?>
		</div> <!-- end .ae-outer-wrapper -->
		<?php
	}

	public function register_controls( Widget_Base $widget ) {
		$this->parent = $widget;
		$this->carousel_controls();
	}

	public function register_style_controls() {
		$this->carousel_style_section();
	}

	public function carousel_controls() {

		$this->start_controls_section(
			'carousel_control',
			[
				'label'     => __( 'Carousel', 'ae-pro' ),
				'condition' => [
					'_skin' => 'carousel',
				],
			]
		);

		$this->add_control(
			'image_carousel',
			[
				'label'     => __( 'Carousel', 'ae-pro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		// Todo:: different effects management
		$this->add_control(
			'effect',
			[
				'label'   => __( 'Effects', 'ae-pro' ),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'fade'      => __( 'Fade', 'ae-pro' ),
					'slide'     => __( 'Slide', 'ae-pro' ),
					'coverflow' => __( 'Coverflow', 'ae-pro' ),
					'flip'      => __( 'Flip', 'ae-pro' ),
				],
				'default' => 'slide',
			]
		);

		$this->add_responsive_control(
			'slide_per_view',
			[
				'label'              => __( 'Slides Per View', 'ae-pro' ),
				'type'               => Controls_Manager::NUMBER,
				'min'                => 1,
				'max'                => 100,
				'default'            => 3,
				'tablet_default'     => 2,
				'mobile_default'     => 1,
				'condition'          => [
					$this->get_control_id( 'effect' ) => [ 'slide', 'coverflow' ],
				],
				'frontend_available' => true,
			]
		);

		$this->add_responsive_control(
			'slides_per_group',
			[
				'label'              => __( 'Slides Per Group', 'ae-pro' ),
				'type'               => Controls_Manager::NUMBER,
				'min'                => 1,
				'max'                => 100,
				'default'            => 1,
				'tablet_default'     => 1,
				'mobile_default'     => 1,
				'condition'          => [
					$this->get_control_id( 'effect' ) => [ 'slide', 'coverflow' ],
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'carousel_settings_heading',
			[
				'label'     => __( 'Setting', 'ae-pro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'speed',
			[
				'label'       => __( 'Speed', 'ae-pro' ),
				'type'        => Controls_Manager::SLIDER,
				'default'     => [
					'size' => 5000,
				],
				'description' => __( 'Duration of transition between slides (in ms)', 'ae-pro' ),
				'range'       => [
					'px' => [
						'min'  => 300,
						'max'  => 10000,
						'step' => 300,
					],
				],

			]
		);

		$this->add_control(
			'autoplay',
			[
				'label'        => __( 'Autoplay', 'ae-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '',
				'label_on'     => __( 'On', 'ae-pro' ),
				'label_off'    => __( 'Off', 'ae-pro' ),
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'duration',
			[
				'label'       => __( 'Duration', 'ae-pro' ),
				'type'        => Controls_Manager::SLIDER,
				'default'     => [
					'size' => 900,
				],
				'description' => __( 'Delay between transitions (in ms)', 'ae-pro' ),
				'range'       => [
					'px' => [
						'min'  => 300,
						'max'  => 10000,
						'step' => 300,
					],
				],
				'condition'   => [
					$this->get_control_id( 'autoplay' ) => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'space',
			[
				'label'              => __( 'Space Between Slides', 'ae-pro' ),
				'type'               => Controls_Manager::SLIDER,
				'default'            => [
					'size' => 15,
				],
				'tablet_default'     => [
					'size' => 10,
				],
				'mobile_default'     => [
					'size' => 5,
				],
				'range'              => [
					'px' => [
						'min'  => 0,
						'max'  => 50,
						'step' => 5,
					],
				],
				'condition'          => [
					$this->get_control_id( 'effect' ) => [ 'slide', 'coverflow' ],
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'loop',
			[
				'label'        => __( 'Loop', 'ae-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'label_on'     => __( 'Yes', 'ae-pro' ),
				'label_off'    => __( 'No', 'ae-pro' ),
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'auto_height',
			[
				'label'        => __( 'Auto Height', 'ae-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '',
				'label_on'     => __( 'Yes', 'ae-pro' ),
				'label_off'    => __( 'No', 'ae-pro' ),
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'pause_on_hover',
			[
				'label'        => __( 'Pause on Hover', 'ae-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => '',
				'label_on'     => __( 'Yes', 'ae-pro' ),
				'label_off'    => __( 'No', 'ae-pro' ),
				'return_value' => 'yes',
				'condition'    => [
					'_skin' => 'carousel',
				],
			]
		);

		$this->add_control(
			'pagination_heading',
			[
				'label'     => __( 'Pagination', 'ae-pro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'ptype',
			[
				'label'   => __( ' Pagination Type', 'ae-pro' ),
				'type'    => Controls_Manager::SELECT,
				'options' =>
					[
						''            => __( 'None', 'ae-pro' ),
						'bullets'     => __( 'Bullets', 'ae-pro' ),
						'fraction'    => __( 'Fraction', 'ae-pro' ),
						'progressbar' => __( 'Progress', 'ae-pro' ),
					],
				'default' => 'bullets',
			]
		);

		$this->add_control(
			'clickable',
			[
				'label'     => __( 'Clickable', 'ae-pro' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'label_on'  => __( 'Yes', 'ae-pro' ),
				'label_off' => __( 'No', 'ae-pro' ),
				'condition' => [
					'ptype' => 'bullets',
				],
			]
		);

		$this->add_control(
			'keyboard',
			[
				'label'        => __( 'Keyboard Control', 'ae-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'label_on'     => __( 'Yes', 'ae-pro' ),
				'label_off'    => __( 'No', 'ae-pro' ),
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'scrollbar',
			[
				'label'        => __( 'Scroll bar', 'ae-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'label_on'     => __( 'Yes', 'ae-pro' ),
				'label_off'    => __( 'No', 'ae-pro' ),
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'navigation_arrow_heading',
			[
				'label'     => __( 'Prev/Next Navigaton', 'ae-pro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',

			]
		);

		$this->add_control(
			'navigation_button',
			[
				'label'        => __( 'Enable', 'ae-pro' ),
				'type'         => Controls_Manager::SWITCHER,
				'default'      => 'yes',
				'label_on'     => __( 'Yes', 'ae-pro' ),
				'label_off'    => __( 'No', 'ae-pro' ),
				'return_value' => 'yes',
			]
		);

		$this->add_control(
			'arrows_layout',
			[
				'label'     => __( 'Position', 'ae-pro' ),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'inside',
				'options'   => [
					'inside'  => __( 'Inside', 'ae-pro' ),
					'outside' => __( 'Outside', 'ae-pro' ),
				],
				'condition' => [
					$this->get_control_id( 'navigation_button' ) => 'yes',
				],

			]
		);

		$this->add_control(
			'arrow_icon_left',
			[
				'label'            => __( 'Icon Prev', 'ae-pro' ),
				'type'             => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'default'          => [
					'value'   => 'fa fa-angle-left',
					'library' => 'fa-solid',
				],
				'condition'        => [
					$this->get_control_id( 'navigation_button' ) => 'yes',
				],
			]
		);

		$this->add_control(
			'arrow_icon_right',
			[
				'label'            => __( 'Icon Next', 'ae-pro' ),
				'type'             => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'default'          => [
					'value'   => 'fa fa-angle-right',
					'library' => 'fa-solid',
				],
				'condition'        => [
					$this->get_control_id( 'navigation_button' ) => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'arrow_horizontal_position',
			[
				'label'       => __( 'Horizontal Position', 'ae-pro' ),
				'type'        => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options'     => [
					'left' => [
						'title' => __( 'Left', 'ae-pro' ),
						'icon'  => 'eicon-h-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'ae-pro' ),
						'icon'  => 'eicon-h-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'ae-pro' ),
						'icon'  => 'eicon-h-align-right',
					],
				],
				'default'     => 'center',
				'condition'   => [
					$this->get_control_id( 'navigation_button' ) => 'yes',
					$this->get_control_id( 'arrows_layout' ) => 'inside',
				],
			]
		);

		$this->add_responsive_control(
			'arrow_vertical_position',
			[
				'label'       => __( 'Vertical Position', 'ae-pro' ),
				'type'        => Controls_Manager::CHOOSE,
				'label_block' => false,
				'options'     => [
					'top' => [
						'title' => __( 'Top', 'ae-pro' ),
						'icon'  => 'eicon-v-align-top',
					],
					'middle' => [
						'title' => __( 'Middle', 'ae-pro' ),
						'icon'  => 'eicon-v-align-middle',
					],
					'bottom' => [
						'title' => __( 'Bottom', 'ae-pro' ),
						'icon'  => 'eicon-v-align-bottom',
					],
				],
				'default'     => 'center',
				'condition'   => [
					$this->get_control_id( 'navigation_button' ) => 'yes',
					$this->get_control_id( 'arrows_layout' ) => 'inside',

				],
			]
		);

		$this->end_controls_section();
	}

	public function carousel_style_section() {
		$this->start_controls_section(
			'carousel_style',
			[
				'label'     => __( 'Carousel', 'ae-pro' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'_skin' => 'carousel',
				],
			]
		);

		$this->add_control(
			'heading_style_arrow',
			[
				'label'     => __( 'Prev/Next Navigation', 'ae-pro' ),
				'type'      => Controls_Manager::HEADING,
				'condition' =>
					[
						$this->get_control_id( 'navigation_button' ) => 'yes',
					],
			]
		);
		$this->start_controls_tabs( 'tabs_arrow_styles' );

		$this->start_controls_tab(
			'tab_arrow_normal',
			[
				'label' => __( 'Normal', 'ae-pro' ),
			]
		);

		$this->add_control(
			'arrow_color',
			[
				'label'     => __( 'Color', 'ae-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ae-swiper-button-prev i' => 'color:{{VAlUE}};',
					'{{WRAPPER}} .ae-swiper-button-next i' => 'color:{{VAlUE}};',
					'{{WRAPPER}} .ae-swiper-button-prev svg' => 'fill:{{VAlUE}};',
					'{{WRAPPER}} .ae-swiper-button-next svg' => 'fill:{{VAlUE}};',
				],
				'default'   => '#444',
				'condition' =>
					[
						$this->get_control_id( 'navigation_button' ) => 'yes',
					],
			]
		);

		$this->add_control(
			'arrow_bg_color',
			[
				'label'     => __( ' Background Color', 'ae-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ae-swiper-button-prev' => 'background-color:{{VAlUE}};',
					'{{WRAPPER}} .ae-swiper-button-next' => 'background-color:{{VAlUE}};',
				],
				'condition' =>
					[
						$this->get_control_id( 'navigation_button' ) => 'yes',
					],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'arrow_border',
				'label'     => __( 'Border', 'ae-pro' ),
				'selector'  => '{{WRAPPER}} .ae-swiper-container .ae-swiper-button-prev, {{WRAPPER}} .ae-swiper-container .ae-swiper-button-next, {{WRAPPER}} .ae-swiper-button-prev, {{WRAPPER}} .ae-swiper-button-next',
				'condition' =>
					[
						$this->get_control_id( 'navigation_button' ) => 'yes',
					],
			]
		);

		$this->add_control(
			'arrow_border_radius',
			[
				'label'      => __( 'Border Radius', 'ae-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .ae-swiper-container .ae-swiper-button-prev, {{WRAPPER}} .ae-swiper-button-prev' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow:hidden;',
					'{{WRAPPER}} .ae-swiper-container .ae-swiper-button-next, {{WRAPPER}} .ae-swiper-button-next' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow:hidden;',
				],
				'condition'  =>
					[
						$this->get_control_id( 'navigation_button' ) => 'yes',
					],
			]
		);
		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_arrow_hover',
			[
				'label' => __( 'Hover', 'ae-pro' ),
			]
		);
		$this->add_control(
			'arrow_color_hover',
			[
				'label'     => __( 'Color', 'ae-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ae-swiper-button-prev:hover i' => 'color:{{VAlUE}};',
					'{{WRAPPER}} .ae-swiper-button-next:hover i' => 'color:{{VAlUE}};',
					'{{WRAPPER}} .ae-swiper-button-prev:hover svg' => 'fill:{{VAlUE}};',
					'{{WRAPPER}} .ae-swiper-button-next:hover svg' => 'fill:{{VAlUE}};',
				],
				'condition' =>
					[
						$this->get_control_id( 'navigation_button' ) => 'yes',
					],
			]
		);

		$this->add_control(
			'arrow_bg_color_hover',
			[
				'label'     => __( ' Background Color', 'ae-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ae-swiper-button-prev:hover' => 'background-color:{{VAlUE}};',
					'{{WRAPPER}} .ae-swiper-button-next:hover' => 'background-color:{{VAlUE}};',
				],
				'condition' =>
					[
						$this->get_control_id( 'navigation_button' ) => 'yes',
					],
			]
		);

		$this->add_control(
			'arrow_border_color_hover',
			[
				'label'     => __( ' Border Color', 'ae-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ae-swiper-button-prev:hover' => 'border-color:{{VAlUE}};',
					'{{WRAPPER}} .ae-swiper-button-next:hover' => 'border-color:{{VAlUE}};',
				],
				'condition' =>
					[
						$this->get_control_id( 'navigation_button' ) => 'yes',
					],
			]
		);

		$this->add_control(
			'arrow_border_radius_hover',
			[
				'label'      => __( 'Border Radius', 'ae-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .ae-swiper-container .ae-swiper-button-prev:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow:hidden;',
					'{{WRAPPER}} .ae-swiper-container .ae-swiper-button-next:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow:hidden;',
				],
				'condition'  =>
					[
						$this->get_control_id( 'navigation_button' ) => 'yes',
					],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'arrow_size',
			[
				'label'     => __( 'Arrow Size', 'ae-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   =>
					[
						'size' => 50,
					],
				'range'     =>
					[
						'min'  => 20,
						'max'  => 100,
						'step' => 1,
					],
				'selectors' => [
					'{{WRAPPER}} .ae-swiper-button-prev i' => 'font-size:{{SIZE}}px;',
					'{{WRAPPER}} .ae-swiper-button-next i' => 'font-size:{{SIZE}}px;',
					'{{WRAPPER}} .ae-swiper-button-prev svg' => 'width : {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .ae-swiper-button-next svg' => 'width : {{SIZE}}{{UNIT}};',
				],
				'condition' =>
					[
						$this->get_control_id( 'navigation_button' ) => 'yes',
					],
			]
		);

		$this->add_responsive_control(
			'horizontal_arrow_offset',
			[
				'label'          => __( 'Horizontal Offset', 'ae-pro' ),
				'type'           => Controls_Manager::SLIDER,
				'size_units'     => [ '%', 'px' ],
				'default'        => [
					'unit' => '%',
				],
				'tablet_default' => [
					'unit' => '%',
				],
				'mobile_default' => [
					'unit' => '%',
				],
				'range'          =>
					[
						'min'  => 1,
						'max'  => 1000,
						'step' => 1,
					],
				'selectors'      => [
					'{{WRAPPER}} .ae-hpos-left .ae-swiper-button-wrapper' => 'left: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .ae-hpos-right .ae-swiper-button-wrapper' => 'right: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .ae-hpos-center .ae-swiper-button-prev' => 'left: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .ae-hpos-center .ae-swiper-button-next' => 'right: {{SIZE}}{{UNIT}}',

				],
				'condition'      => [
					$this->get_control_id( 'navigation_button' ) => 'yes',
					$this->get_control_id( 'arrows_layout' ) => 'inside',
				],
			]
		);
		$this->add_responsive_control(
			'vertical_arrow_offset',
			[
				'label'          => __( 'Vertical Offset', 'ae-pro' ),
				'type'           => Controls_Manager::SLIDER,
				'size_units'     => [ '%', 'px' ],
				'default'        => [
					'unit' => '%',
				],
				'tablet_default' => [
					'unit' => '%',
				],
				'mobile_default' => [
					'unit' => '%',
				],
				'range'          =>
					[
						'min'  => 1,
						'max'  => 1000,
						'step' => 1,
					],
				'selectors'      => [
					'{{WRAPPER}} .ae-vpos-top .ae-swiper-button-wrapper' => 'top: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .ae-vpos-bottom .ae-swiper-button-wrapper' => 'bottom: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .ae-vpos-middle .ae-swiper-button-prev' => 'top: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .ae-vpos-middle .ae-swiper-button-next' => 'top: {{SIZE}}{{UNIT}}',

				],
				'condition'      => [
					$this->get_control_id( 'navigation_button' ) => 'yes',
					$this->get_control_id( 'arrows_layout' ) => 'inside',
				],
			]
		);

		$this->add_responsive_control(
			'arrow_gap',
			[
				'label'          => __( 'Arrow Gap', 'ae-pro' ),
				'type'           => Controls_Manager::SLIDER,
				'size_units'     => [ '%', 'px' ],
				'default'        => [
					'unit' => 'px',
					'size' => '25',
				],
				'tablet_default' => [
					'unit' => '%',
				],
				'mobile_default' => [
					'unit' => '%',
				],
				'range'          =>
					[
						'min'  => 1,
						'max'  => 1000,
						'step' => 1,
					],
				'selectors'      => [
					'{{WRAPPER}} .ae-swiper-container'     => 'margin-left: {{SIZE}}{{UNIT}}; margin-right: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .ae-swiper-outer-wrapper' => 'position: relative',
					'{{WRAPPER}} .ae-swiper-button-prev'   => 'left: 0',
					'{{WRAPPER}} .ae-swiper-button-next'   => 'right: 0',

				],
				'condition'      => [
					$this->get_control_id( 'navigation_button' ) => 'yes',
					$this->get_control_id( 'arrows_layout' ) => 'outside',
				],
			]
		);

		$this->add_responsive_control(
			'arrow_padding',
			[
				'label'      => __( 'Padding', 'ae-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .ae-swiper-button-prev' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .ae-swiper-button-next' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'heading_style_dots',
			[
				'label'     => __( 'Dots', 'ae-pro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' =>
					[
						$this->get_control_id( 'ptype' ) => 'bullets',
					],
			]
		);

		$this->add_control(
			'dots_size',
			[
				'label'     => __( 'Dots Size', 'ae-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   =>
					[
						'size' => 5,
					],
				'range'     =>
					[
						'min'  => 1,
						'max'  => 10,
						'step' => 1,
					],
				'selectors' => [
					'{{WRAPPER}} .swiper-pagination-bullet' => 'width:{{SIZE}}px; height:{{SIZE}}px;',
				],
				'condition' =>
					[
						$this->get_control_id( 'ptype' ) => 'bullets',
					],
			]
		);

		$this->add_responsive_control(
			'dot_top_offset',
			[
				'label'     => __( 'Top Offset', 'ae-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'default'   => [
					'unit' => 'px',
					'size' => 0,
				],
				'selectors' => [
					'{{WRAPPER}} .ae-post-widget-wrapper' => 'margin-bottom:{{SIZE}}{{UNIT}};',
				],
				'condition' => [
					$this->get_control_id( 'ptype' ) => 'bullets',
				],
			]
		);

		$this->add_control(
			'dots_color',
			[
				'label'     => __( 'Active Dot Color', 'ae-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .swiper-pagination-bullet-active' => 'background-color:{{VAlUE}} !important;',
				],
				'condition' =>
					[
						$this->get_control_id( 'ptype' ) => 'bullets',
					],
			]
		);

		$this->add_control(
			'inactive_dots_color',
			[
				'label'     => __( 'Inactive Dot Color', 'ae-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .swiper-pagination-bullet' => 'background-color:{{VAlUE}};',
				],
				'condition' =>
					[
						$this->get_control_id( 'ptype' ) => 'bullets',
					],
			]
		);

		$this->add_responsive_control(
			'pagination_bullet_margin',
			[
				'label'      => __( 'Margin', 'ae-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .ae-swiper-pagination' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'  =>
					[
						$this->get_control_id( 'ptype' ) => 'bullets',
					],
			]
		);

		$this->add_control(
			'heading_style_fraction',
			[
				'label'     => __( 'Fraction', 'ae-pro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' =>
					[
						$this->get_control_id( 'ptype' ) => 'fraction',
					],
			]
		);

		$this->add_control(
			'fraction_bg_color',
			[
				'label'     => __( 'Background Color', 'ae-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .swiper-pagination-current, {{WRAPPER}} .swiper-pagination-total' => 'background-color:{{VAlUE}};',
				],
				'condition' =>
					[
						$this->get_control_id( 'ptype' ) => 'fraction',
					],
			]
		);

		$this->add_control(
			'fraction_color',
			[
				'label'     => __( 'Color', 'ae-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .swiper-pagination-fraction' => 'color:{{VAlUE}};',
				],
				'condition' =>
					[
						$this->get_control_id( 'ptype' ) => 'fraction',
					],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'pagination_typography',
				'label'     => __( 'Typography', 'ae-pro' ),
				'selector'  => '{{WRAPPER}} .swiper-pagination-fraction',
				'condition' =>
					[
						$this->get_control_id( 'ptype' ) => 'fraction',
					],
			]
		);

		$this->add_responsive_control(
			'fraction_padding',
			[
				'label'      => __( 'Padding', 'ae-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .swiper-pagination-fraction .swiper-pagination-current, {{WRAPPER}} .swiper-pagination-fraction .swiper-pagination-total' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'  =>
					[
						$this->get_control_id( 'ptype' ) => 'fraction',
					],
			]
		);

		$this->add_control(
			'heading_style_scroll',
			[
				'label'     => __( 'Scrollbar', 'ae-pro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' =>
					[
						$this->get_control_id( 'scrollbar' ) => 'yes',
					],
			]
		);
		$this->add_control(
			'scroll_size',
			[
				'label'     => __( 'Scrollbar Size', 'ae-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   =>
					[
						'size' => 5,
					],
				'range'     =>
					[
						'min'  => 1,
						'max'  => 10,
						'step' => 1,
					],
				'selectors' => [
					'{{WRAPPER}} .swiper-container-vertical .ae-swiper-scrollbar' => 'width:{{SIZE}}px;',
					'{{WRAPPER}} .swiper-container-horizontal .ae-swiper-scrollbar' => 'height:{{SIZE}}px;',
				],
				'condition' =>
					[
						$this->get_control_id( 'scrollbar' ) => 'yes',
					],
			]
		);

		$this->add_control(
			'scrollbar_color',
			[
				'label'     => __( 'Scrollbar Drag Color', 'ae-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .swiper-scrollbar-drag' => 'background-color:{{VAlUE}};',
				],
				'condition' =>
					[
						$this->get_control_id( 'scrollbar' ) => 'yes',
					],
			]
		);

		$this->add_control(
			'scroll_color',
			[
				'label'     => __( 'Scrollbar Color', 'ae-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .ae-swiper-scrollbar' => 'background-color:{{VAlUE}};',
				],
				'condition' =>
					[
						$this->get_control_id( 'scrollbar' ) => 'yes',
					],
			]
		);

		$this->add_control(
			'heading_style_progress',
			[
				'label'     => __( 'Progress Bar', 'ae-pro' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' =>
					[
						$this->get_control_id( 'ptype' ) => 'progressbar',
					],
			]
		);
		$this->add_control(
			'progressbar_color',
			[
				'label'     => __( 'Prgress Bar Color', 'ae-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .swiper-pagination-progressbar' => 'background-color:{{VAlUE}};',
				],
				'condition' =>
					[
						$this->get_control_id( 'ptype' ) => 'progressbar',
					],
			]
		);

		$this->add_control(
			'progress_color',
			[
				'label'     => __( 'Prgress Color', 'ae-pro' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .swiper-pagination-progressbar-fill' => 'background-color:{{VAlUE}};',
				],
				'condition' =>
					[
						$this->get_control_id( 'ptype' ) => 'progressbar',
					],
			]
		);

		$this->add_control(
			'progressbar_size',
			[
				'label'     => __( 'Prgress Bar Size', 'ae-pro' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   =>
					[
						'size' => 5,
					],
				'range'     =>
					[
						'min'  => 1,
						'max'  => 10,
						'step' => 1,
					],
				'selectors' => [
					'{{WRAPPER}} .swiper-container-horizontal .swiper-pagination-progressbar' => 'height:{{SIZE}}px;',
					'{{WRAPPER}} .swiper-container-vertical .swiper-pagination-progressbar' => 'width:{{SIZE}}px;',
				],
				'condition' =>
					[
						$this->get_control_id( 'ptype' ) => 'progressbar',
					],
			]
		);

		$this->add_responsive_control(
			'pagination_progress_margin',
			[
				'label'      => __( 'Margin', 'ae-pro' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .ae-swiper-pagination' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'  =>
					[
						$this->get_control_id( 'ptype' ) => 'progressbar',
					],
			]
		);

		$this->end_controls_section();
	}

	public function get_swiper_data() {

		if ( $this->get_instance_value( 'speed' )['size'] ) {
			$swiper_data['speed'] = $this->get_instance_value( 'speed' )['size'];
		} else {
			$swiper_data['speed'] = 1000;
		}
		$swiper_data['direction'] = 'horizontal';

		if ( $this->get_instance_value( 'autoplay' ) === 'yes' ) {
			$swiper_data['autoplay']['delay'] = $this->get_instance_value( 'duration' )['size'];
		} else {
			$swiper_data['autoplay'] = false;
		}

		if ( $this->get_instance_value( 'pause_on_hover' ) === 'yes' ) {
			$swiper_data['pause_on_hover'] = $this->get_instance_value( 'pause_on_hover' );
		}

		$swiper_data['effect'] = $this->get_instance_value( 'effect' );

		$swiper_data['loop']       = $this->get_instance_value( 'loop' );
		$height                    = $this->get_instance_value( 'auto_height' );
		$swiper_data['autoHeight'] = ( $height === 'yes' ) ? true : false;
		$ele_breakpoints           = Plugin::$instance->breakpoints->get_active_breakpoints();
		$active_devices            = Plugin::$instance->breakpoints->get_active_devices_list();
		$active_breakpoints        = array_keys( $ele_breakpoints );
		$break_value               = [];
		foreach ( $active_devices as $active_device ) {
			$min_breakpoint                = Plugin::$instance->breakpoints->get_device_min_breakpoint( $active_device );
			$break_value[ $active_device ] = $min_breakpoint;
		}

		if ( $this->get_instance_value( 'effect' ) === 'fade' || $this->get_instance_value( 'effect' ) === 'flip' ) {
			foreach ( $active_devices as $break_key => $active_device ) {
				if ( $active_device === 'desktop' ) {
					$active_device = 'default';
				}
				$swiper_data['spaceBetween'][ $active_device ] = 0;
			}
			foreach ( $active_devices as $break_key => $active_device ) {
				if ( $active_device === 'desktop' ) {
					$active_device = 'default';
				}
				$swiper_data['slidesPerView'][ $active_device ] = 1;
			}
			foreach ( $active_devices as $break_key => $active_device ) {
				if ( $active_device === 'desktop' ) {
					$active_device = 'default';
				}
				$swiper_data['slidesPerGroup'][ $active_device ] = 1;
			}
		} else {

			foreach ( $active_devices as $break_key => $active_device ) {
				//phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
				if ( in_array( $active_device, [ 'mobile', 'tablet', 'desktop' ] ) ) {
					switch ( $active_device ) {
						case 'mobile':
							$swiper_data['spaceBetween'][ $active_device ] = intval( $this->get_instance_value( 'space_' . $active_device )['size'] !== '' ? $this->get_instance_value( 'space_' . $active_device )['size'] : 5 );
							break;
						case 'tablet':
							$swiper_data['spaceBetween'][ $active_device ] = intval( $this->get_instance_value( 'space_' . $active_device )['size'] !== '' ? $this->get_instance_value( 'space_' . $active_device )['size'] : 10 );
							break;
						case 'desktop':
							$swiper_data['spaceBetween']['default'] = intval( $this->get_instance_value( 'space' )['size'] !== '' ? $this->get_instance_value( 'space' )['size'] : 15 );
							break;
					}
				} else {
					$swiper_data['spaceBetween'][ $active_device ] = intval( $this->get_instance_value( 'space_' . $active_device )['size'] !== '' ? $this->get_instance_value( 'space_' . $active_device )['size'] : 15 );
				}
			}
			// SlidesPerView
			foreach ( $active_devices as $break_key => $active_device ) {
				//phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
				if ( in_array( $active_device, [ 'mobile', 'tablet', 'desktop' ] ) ) {
					switch ( $active_device ) {
						case 'mobile':
							$swiper_data['slidesPerView'][ $active_device ] = intval( $this->get_instance_value( 'slide_per_view_' . $active_device ) !== '' ? $this->get_instance_value( 'slide_per_view_' . $active_device ) : 1 );
							break;
						case 'tablet':
							$swiper_data['slidesPerView'][ $active_device ] = intval( $this->get_instance_value( 'slide_per_view_' . $active_device ) !== '' ? $this->get_instance_value( 'slide_per_view_' . $active_device ) : 2 );
							break;
						case 'desktop':
							$swiper_data['slidesPerView']['default'] = intval( $this->get_instance_value( 'slide_per_view' ) !== '' ? $this->get_instance_value( 'slide_per_view' ) : 3 );
							break;
					}
				} else {
					$swiper_data['slidesPerView'][ $active_device ] = intval( $this->get_instance_value( 'slide_per_view_' . $active_device ) !== '' ? $this->get_instance_value( 'slide_per_view_' . $active_device ) : 2 );
				}
			}

			// SlidesPerGroup
			foreach ( $active_devices as $break_key => $active_device ) {
				//phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
				if ( in_array( $active_device, [ 'mobile', 'tablet', 'desktop' ] ) ) {
					switch ( $active_device ) {
						case 'mobile':
							$swiper_data['slidesPerGroup'][ $active_device ] = $this->get_instance_value( 'slides_per_group_' . $active_device ) !== '' ? $this->get_instance_value( 'slides_per_group_' . $active_device ) : 1;
							break;
						case 'tablet':
							$swiper_data['slidesPerGroup'][ $active_device ] = $this->get_instance_value( 'slides_per_group_' . $active_device ) !== '' ? $this->get_instance_value( 'slides_per_group_' . $active_device ) : 1;
							break;
						case 'desktop':
							$swiper_data['slidesPerGroup']['default'] = $this->get_instance_value( 'slides_per_group' ) !== '' ? $this->get_instance_value( 'slides_per_group' ) : 1;
							break;
					}
				} else {
					$swiper_data['slidesPerGroup'][ $active_device ] = $this->get_instance_value( 'slides_per_group_' . $active_device ) !== '' ? $this->get_instance_value( 'slides_per_group_' . $active_device ) : 1;
				}
			}
		}

		if ( $this->get_instance_value( 'ptype' ) !== '' ) {
			$swiper_data['ptype'] = $this->get_instance_value( 'ptype' );
		}
		$swiper_data['breakpoints_value'] = $break_value;
		$clickable                        = $this->get_instance_value( 'clickable' );
		$swiper_data['clickable']         = isset( $clickable ) ? $clickable : false;
		$swiper_data['navigation']        = $this->get_instance_value( 'navigation_button' );
		$swiper_data['scrollbar']         = $this->get_instance_value( 'scrollbar' );

		return $swiper_data;
	}

	public function get_swiper_pagination() {
		if ( $this->get_instance_value( 'ptype' ) !== '' ) {
			?>
			<div class = "ae-swiper-pagination swiper-pagination"></div>
			<?php
		}
	}

	public function get_swiper_scrolbar() {
		if ( $this->get_instance_value( 'scrollbar' ) === 'yes' ) {
			?>
			<div class = "ae-swiper-scrollbar swiper-scrollbar"></div>
			<?php
		}
	}

	public function get_swiper_arrows() {

		if ( $this->get_instance_value( 'arrow_horizontal_position' ) !== 'center' && $this->get_instance_value( 'arrows_layout' ) === 'inside' ) {
			?>
			<div class="ae-swiper-button-wrapper">
			<?php
		}
		?>
		<div class = "ae-swiper-button-prev swiper-button-prev">
			<?php
			if ( is_rtl() ) {
				Icons_Manager::render_icon( $this->get_instance_value( 'arrow_icon_right' ), [ 'aria-hidden' => 'true' ] );
			} else {
				Icons_Manager::render_icon( $this->get_instance_value( 'arrow_icon_left' ), [ 'aria-hidden' => 'true' ] );
			}
			?>
		</div>
		<div class = "ae-swiper-button-next swiper-button-next">
			<?php
			if ( is_rtl() ) {
				Icons_Manager::render_icon( $this->get_instance_value( 'arrow_icon_left' ), [ 'aria-hidden' => 'true' ] );
			} else {
				Icons_Manager::render_icon( $this->get_instance_value( 'arrow_icon_right' ), [ 'aria-hidden' => 'true' ] );
			}
			?>
		</div>
		<?php
		if ( $this->get_instance_value( 'arrow_horizontal_position' ) !== 'center' && $this->get_instance_value( 'arrows_layout' ) === 'inside' ) {
			;
			?>
			</div>
			<?php
		}
	}
}
