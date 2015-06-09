<?php
defined('MOODLE_INTERNAL') || die();

function atto_youtube_strings_for_js() {
	global $PAGE;
	$strings = array(
		'createvideo',
		'cropvideo',
		'enterurl',
	);
	$PAGE->requires->strings_for_js($strings, 'atto_youtube');
}
