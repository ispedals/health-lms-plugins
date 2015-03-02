<?php

defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot.'/filter/viewerjs/lib.php');

/*
	adapted from filter_mediaplugin in filter/mediaplugin/filter.php from the Moodle core
	https://github.com/lucisgit/moodle-filter_jwplayer used for guidance
    
    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/

class filter_viewerjs extends moodle_text_filter {
	private $mediarenderer;


	// copied from filter_mediaplugin::filter
    // the reason we are not using inheritance is because
    // filter_mediaplugin::mediarenderer is private and we have different logic in how it is set
    public function filter($text, array $options = array()) {
		global $PAGE;

		if (!is_string($text) or empty($text)) {
			return $text;
		}

		if (stripos($text, '</a>') === false) {
			return $text;
		}

		if (!$this->mediarenderer) {
			$this->mediarenderer = $PAGE->get_renderer('filter_viewerjs');
			$embedmarkers = $this->mediarenderer->get_embeddable_markers();
		}

        // Looking for tags.
        $matches = preg_split('/(<[^>]*>)/i', $text, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
        
        if (!$matches) {
            return $text;
        }
        
        // Regex to find media extensions in an <a> tag.
        $re = '~<a\s[^>]*href="([^"]*(?:' .  $embedmarkers . ')[^"]*)"[^>]*>([^>]*)</a>~is';
        $newtext = '';
        $validtag = '';
        $sizeofmatches = count($matches);
        
        // We iterate through the given string to find valid <a> tags
        // and build them so that the callback function can check it for
        // embedded content. Then we rebuild the string.
        foreach ($matches as $idx => $tag) {
            if (preg_match('|</a>|', $tag) && !empty($validtag)) {
                $validtag .= $tag;
                // Given we now have a valid <a> tag to process it's time for
                // ReDoS protection. Stop processing if a word is too large.
                if (strlen($validtag) < 4096) {
                    $processed = preg_replace_callback($re, array($this, 'callback'), $validtag);
                }
                // Rebuilding the string with our new processed text.
                $newtext .= !empty($processed) ? $processed : $validtag;
                // Wipe it so we can catch any more instances to filter.
                $validtag = '';
                $processed = '';
            } else if (preg_match('/<a\s[^>]*/', $tag) && $sizeofmatches > 1) {
                // Looking for a starting <a> tag.
                $validtag = $tag;
            } else {
                // If we have a validtag add to that to process later,
                // else add straight onto our newtext string.
                if (!empty($validtag)) {
                    $validtag .= $tag;
                } else {
                    $newtext .= $tag;
                }
            }
        }
        // Return the same string except processed by the above.
        return $newtext;
	}

	private function callback(array $matches) {
        // Get name.
        $name = trim($matches[2]);
        if (empty($name) or strpos($name, 'http') === 0) {
            $name = ''; // Use default name.
        }

		// Split provided URL into alternatives.
		$urls = core_media::split_alternatives($matches[1], $width, $height);
		$result = $this->mediarenderer->embed_alternatives($urls, $name, $width, $height);

        // If something was embedded, return it, otherwise return original.
        if ($result !== '') {
            return $result;
        } else {
            return $matches[0];
        }
	}
}
?>