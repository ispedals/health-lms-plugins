<?php
define('CLI_SCRIPT', true);
require('config.php');

require_once($CFG->libdir . '/grade/grade_item.php');
require_once($CFG->libdir . '/gradelib.php');

        global $DB, $CFG;
        $event = new stdClass();
        $event->courseid=24;
        $event->relateduserid = 31;
        $courseid = $event->courseid;
        $userid = $event->relateduserid;
        $dbuser = $DB->get_record('user', array('id' => $userid));
        $name = $dbuser->firstname . ' ' . $dbuser->lastname;
        $course = get_course($courseid);
        $coursename = $course->fullname;

        $records = array();

        $grade_items = grade_item::fetch_all(array('courseid' => $courseid));

        foreach ($grade_items as $grade_item) {
            if($grade_item->is_outcome_item()){
                continue;
            }
            $grades = grade_get_grades($courseid, $grade_item->itemtype, $grade_item->itemmodule, $grade_item->iteminstance, $userid);
            foreach($grades->items as $item){
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
                $record->updated = $event->timecreated;
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
                $record->updated = $event->timecreated;
                $records[]=$record;
            }
        }

        $DB->insert_records('report_tincan_grades', $records);