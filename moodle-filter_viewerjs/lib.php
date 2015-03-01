<?php

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/medialib.php');

class filter_viewerjs_media extends core_media_player {

	public function embed($urls, $name, $width, $height, $options) {
		global $CFG;
		//don't expect alternative urls
		if(count($urls) !== 1){
			return ''; //TODO empty string means error, but do better error handling
		}

		$file_url = new moodle_url($urls[0]);
		$viewerjs_player_url = new moodle_url('/lib/viewerjs');
		//we assume the lib/viewerjs directory will be two directories away from the intital public directory
		$viewerjs_player_url->set_anchor('../..' . $file_url->out_as_local_url());

		//TODO don't hardcode dimensions
		$output = html_writer::tag('iframe', '', array('src' => $viewerjs_player_url->out(), 'width' => 300, 'height' => 300));

		return $output;
	}

	 public function get_supported_extensions() {
		return array('pdf');
	}

	 public function get_rank() {
		return 1; //TODO 0 means that this player does not really render video, but unsure the implication of what will happen
	}

	public function is_enabled() {
		return true;
	}
}

?>