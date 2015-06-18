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
        $coursename = $course->fullname;

        $records = array();

        //Get all the grade items from the course, which we will use to get the associated grade

        $grade_items = grade_item::fetch_all(array('courseid' => $courseid));

        foreach ($grade_items as $grade_item) {
            // outcomes appear as their own assignment in a course even if they are associated with another activity
            if($grade_item->is_outcome_item()){
                continue;
            }
            $grades = grade_get_grades($courseid, $grade_item->itemtype, $grade_item->itemmodule, $grade_item->iteminstance, $userid);
            //grades contain two objects, grades and outcomes
            foreach($grades->items as $item){
                //ignore items the use a text grade or no grade
                if($grade_item->gradetype == 0 || $grade_item->gradetype == 3){
                    break;
                }
                $record = new stdClass();
                $record->userid = $userid;
                $record->courseid = $courseid;
                $record->itemid = $item->id;
                $record->name = $name;
                $record->course = $coursename;
                $record->type = $grade_item->get_name();
                $record->score = $item->grades[$userid]->str_grade;
                $records[]=$record;
            }
            foreach($grades->outcomes as $grade){
                $record = new stdClass();
                $record->userid = $userid;
                $record->courseid = $courseid;
                $record->itemid = $grade->id;
                $record->name = $name;
                $record->course = $coursename;
                $record->type = $grade->name;
                $record->score = $grade->grades[$userid]->str_grade;
                $records[]=$record;
            }
        }
        $DB->insert_records('report_tincan_grades', $records);
    }

}

