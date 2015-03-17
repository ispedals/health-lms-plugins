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

/**
 * Library of interface functions and constants for module tincan
 *
 * All the core Moodle functions, neeeded to allow the module to work
 * integrated in Moodle should be placed here.
 * All the tincan specific functions, needed to implement all the module
 * logic, should go to locallib.php. This will help to save some memory when
 * Moodle is performing actions across all modules.
 *
 * @package report_tincan
 * @copyright  LEO
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/quiz/locallib.php');

class report_tincan_observer {

	public static function tincan_course_completed($event){
		global $DB;
        $pregrade = $DB->get_record_sql('SELECT ROUND(finalgrade / rawgrademax * 100 ,2) AS Percentage FROM {grade_grades} AS gg JOIN {grade_items} AS gi ON gi.id = gg.itemid WHERE gi.courseid = ? AND gg.userid = ? AND gi.itemmodule = "?" AND gi.itemname like "%?%"', array($event->courseid, $event->relateduserid, 'quiz', 'pre'));
        $postgrade = $DB->get_record_sql('SELECT ROUND(finalgrade / rawgrademax * 100 ,2) AS Percentage FROM {grade_grades} AS gg JOIN {grade_items} AS gi ON gi.id = gg.itemid WHERE gi.courseid = ? AND gg.userid = ? AND gi.itemmodule = "?" AND gi.itemname like "%?%"', array($event->courseid, $event->relateduserid, 'quiz', 'post'));
        tincanrpt_save_statement(array('userid' => $event->relateduserid, 'courseid' => $event->courseid, 'pretest' => $pregrade, 'postest'=> $postgrade, 'updated' => date(DATE_ATOM));
	}

	public static function tincanrpt_save_statement($data) {
		file_put_contents('quizlog.log',self::tincanrpt_myJson_encode($data), FILE_APPEND | LOCK_EX);
	}

}
