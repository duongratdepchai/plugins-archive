<?php

namespace ElementPack\Modules\BbpressSingleView;

use ElementPack\Base\Element_Pack_Module_Base;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function get_name() {
		return 'bbpress-single-view';
	}

	public function get_widgets() {

		$widgets = [
			'Bbpress_Single_View',
		];

		return $widgets;
	}
}
