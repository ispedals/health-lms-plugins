<?php
require('config.php');

global $DB;

$courseid = 2;
$userid = 4;

$event = \core\event\course_completed::create(array('context' => context_course::instance($courseid), 'objectid' => $courseid, 'relateduserid' =>$userid, 'courseid' => $courseid, 'other' => array('relateduserid' =>$userid)));
$event->trigger();

// to check if observer has been sucessfully registered
//require('lib/classes/event/manager.php');
//var_dump(core\event\manager::get_all_observers());
?>