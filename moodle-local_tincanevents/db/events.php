<?php

$observers = array(
	array (
	    'eventname'     => '\core\event\course_completed',
	    'callback' => 'report_tincan_observer::tincan_course_completed',
	)
);
?>