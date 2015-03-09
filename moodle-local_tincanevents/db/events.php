<?php

$observers = array(
	array (
	    'eventname'     => '\mod_quiz\event\attempt_submitted',
	    'callback' => 'report_tincan_observer::tincan_quiz_attempt_submitted',
	),
	array (
	    'eventname'     => '\mod_quiz\event\attempt_started',
	    'callback' => 'report_tincan_observer::tincan_quiz_attempt_started',
	),
	array (
	    'eventname'     => '\core\event\course_completed',
	    'callback' => 'report_tincan_observer::tincan_course_completed',
	),
);
?>