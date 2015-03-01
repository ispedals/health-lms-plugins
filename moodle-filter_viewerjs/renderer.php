<?php

defined('MOODLE_INTERNAL') || die();

class filter_viewerjs_renderer extends core_media_renderer {
	protected function get_players_raw() {
		return array( 'viewerjs' => new filter_viewerjs_media());
	}
}
?>