<?php

defined('MOODLE_INTERNAL') || die();

/*
	adapted from filter_jwplayer  in filter.php trom
	https://github.com/lucisgit/moodle-filter_jwplayer which in turn is adapted from
	filter/mediaplugin/filter.php from the Moodle core

	filter_jwplayer is released under GNU GPL v3
*/

class filter_viewerjs extends moodle_text_filter {


	public function filter($text, array $options = array()) {
		global $CFG, $PAGE;

		if (!is_string($text) or empty($text)) {
			return $text;
		}

		if (stripos($text, '</a>') === false) {
			return $text;
		}

		$newtext = preg_replace('/<a\s[^>]*href="([^"]*(?:[.]pdf)[^"]*)"[^>]*>([^>]*)<\/a>/is',"captured $1", $text);

		if (empty($newtext) or $newtext === $text) {
			return $text;
		}
		return $newtext;
	}
}
?>