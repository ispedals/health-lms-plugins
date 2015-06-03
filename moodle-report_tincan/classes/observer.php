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

require_once($CFG->libdir . '/grade/grade_item.php');
require_once($CFG->libdir . '/gradelib.php');

class report_tincan_observer {

    public static function tincan_course_completed($event){
        global $DB;
        
        $courseid = $event->courseid;
        $userid = $event->relateduserid;
        $dbuser = $DB->get_record('user', array('id' => $userid));
        $name = $dbuser->firstname . ' ' . $dbuser->lastname;
        $course = get_course($courseid);
        $coursename = $course->name;
        
        $records = array();
        
        $grade_items = grade_items::fetch_all(array('courseid' => $courseid));
        
        foreach ($grade_items as $grade_item) {
            $grades = grade_get_grades($courseid, $grade_item->itemtype, $grade_item->itemmodule, $grade_item->iteminstance, $userid);
            foreach($grades->items as $item){
                $record = new stdClass();
                $record->userid = $userid;
                $record->courseid = $courseid;
                $record->itemid = $item->id;
                $record->name = $name;
                $record->course = $coursename;
                $record->type = $item->grade[$userid]->name;
                $record->score = $item->grade[$userid]->str_grade;
                $record->updated = $item->grade[$userid]->dategraded;
                $records[]=$record;
            }
            foreach($grades->outcomes as $grade){
                $record = new stdClass();
                $record->userid = $userid;
                $record->courseid = $courseid;
                $record->itemid = $item->id;
                $record->name = $name;
                $record->course = $course;
                $record->type = $item->grade[$userid]->name;
                $record->score = $item->grade[$userid]->str_grade;
                $record->updated = $item->grade[$userid]->usermodified;
                $records[]=$record;
            }
        }
        $DB->insert_records('report_tincan_grades', $records);
    }

}

