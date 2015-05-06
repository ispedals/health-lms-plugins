<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot.'/filter/viewerjs/lib.php');

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
		}
		$embedmarkers = $this->mediarenderer->get_embeddable_markers();

        // Looking for tags.
        $matches = preg_split('/(<[^>]*>)/i', $text, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);

        if (!$matches) {
            return $text;
        }

        // Regex to find media extensions in an <a> tag.
		// TODO try to only match local urls, as they are the only ones that will work
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
		try { // guard against runtime errors
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
		catch (Exception $e) {
			error_log('filterjs encountered an exception: ' . $e->getMessage(), 0);
			return $matches[0];
		}
	}
}
?>