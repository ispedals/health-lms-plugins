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

class report_tincan_observer {

    public static function tincan_course_completed($event){
        global $DB;
        $courseid = $event->courseid;
        $userid = $event->relateduserid;
        $pregrade = $DB->get_record_sql("SELECT ROUND(finalgrade / rawgrademax * 100 ,2) AS percentage FROM {grade_grades} as gg JOIN {grade_items} AS gi  ON gi.id = gg.itemid WHERE gi.courseid = ? AND gg.userid = ? AND gi.itemmodule=? AND gi.itemname LIKE '%pretest' LIMIT 1", array($courseid, $userid, 'quiz'));
        $postgrade = $DB->get_record_sql("SELECT ROUND(finalgrade / rawgrademax * 100 ,2) AS percentage, gg.timemodified FROM {grade_grades} as gg JOIN {grade_items} AS gi  ON gi.id = gg.itemid WHERE gi.courseid = ? AND gg.userid = ? AND gi.itemmodule=? AND gi.itemname LIKE '%posttest' LIMIT 1", array($courseid, $userid, 'quiz'));
        $record = new stdClass();
        $record->courseid = $courseid;
        $record->userid = $userid;
        $record->pretest = is_object($pregrade) ? $pregrade->percentage : null;
        $record->posttest = is_object($postgrade) ? $postgrade->percentage : null;
        $record->updated = $event->timecreated;
        $DB->insert_record('report_tincan_grades', $record, false);
    }

}

