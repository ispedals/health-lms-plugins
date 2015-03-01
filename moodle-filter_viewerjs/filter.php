<?php

defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot.'/filter/viewerjs/lib.php');

/*
	adapted from filter_jwplayer  in filter.php trom
	https://github.com/lucisgit/moodle-filter_jwplayer which in turn is adapted from
	filter/mediaplugin/filter.php from the Moodle core

	filter_jwplayer is released under GNU GPL v3
*/

class filter_viewerjs extends moodle_text_filter {
	protected $renderer;
	protected $embedmarkers;


	public function filter($text, array $options = array()) {
		global $PAGE;

		if (!is_string($text) or empty($text)) {
			return $text;
		}

		if (stripos($text, '</a>') === false) {
			return $text;
		}

		if (!$this->renderer) {
			$this->renderer = $PAGE->get_renderer('filter_viewerjs');
			$this->embedmarkers = $this->renderer->get_embeddable_markers();
		}

		//matches href and tag contents
		//TODO see if we can use a real parser to protect against potential security problems
		$newtext = preg_replace_callback('/<a\s[^>]*href="([^"]*(?:' . $this->embedmarkers . ')[^"]*)"[^>]*>([^>]*)<\/a>/is', array($this, 'callback'), $text);

		if (empty($newtext) or $newtext === $text) {
			return $text;
		}
		return $newtext;
	}

	private function callback(array $matches) {
		// Get name.
		$name = trim($matches[2]);
		 if (empty($name) or strpos($name, 'http') === 0) {
			$name = '';
		}

		// Split provided URL into alternatives.
		$urls = core_media::split_alternatives($matches[1], $width, $height);
		$result = $this->renderer->embed_alternatives($urls, $name, $width, $height);

		// If something was embedded, return it, otherwise return original.
		if ($result !== '') {
			return $result;
		}
		else {
			return $matches[0];
		}
	}
}
?>