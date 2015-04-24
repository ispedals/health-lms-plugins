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

require_once($CFG->libdir . '/medialib.php');

class filter_viewerjs_media extends core_media_player {

    public function embed($urls, $name, $width, $height, $options) {
        global $CFG;
        //don't expect alternative urls
        if(count($urls) !== 1){
            return '';
        }

        $file_url = new moodle_url($urls[0]);
        $viewerjs_player_url = new moodle_url('/lib/viewerjs');
        //we assume the lib/viewerjs directory will be two directories away from the initial public directory
        $viewerjs_player_url->set_anchor('../..' . $file_url->out_as_local_url());

        if(!$width){
            $width = 800;
        }
        if(!$height){
            $height = 600;
        }
        
        $output = html_writer::tag('iframe', '', array('src' => $viewerjs_player_url->out(), 'width' =>  $width, 'height' =>  $height, 'webkitallowfullscreen' => 'webkitallowfullscreen', 'mozallowfullscreen' => 'mozallowfullscreen', 'allowfullscreen' => 'allowfullscreen' ));

        return $output;
    }

     public function get_supported_extensions() {
        return array('pdf', 'ods', 'odp', 'odt');
    }

     public function get_rank() {
        return 1;
    }

    public function is_enabled() {
        return true;
    }
}

?>