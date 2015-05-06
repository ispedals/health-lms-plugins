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

global $CFG;
require_once($CFG->dirroot . '/mod/quiz/lib.php');

class report_tincanevents_testcase extends advanced_testcase {

	protected $course;
	protected $student;

	//adapted from mod/quiz/tests/lib_test.php:mod_quiz_lib_testcase::test_quiz_get_completion_state() @e527aff4
	protected function setUp() {
		global $CFG, $DB;
        $this->resetAfterTest(true);

        // Enable completion before creating modules, otherwise the completion data is not written in DB.
        $CFG->enablecompletion = true;

        // Create a course and student.
        $this->course = $this->getDataGenerator()->create_course(array('enablecompletion' => true));
        $this->student = $this->getDataGenerator()->create_user();
        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $this->assertNotEmpty($studentrole);

        // Enrol students.
        $this->assertTrue($this->getDataGenerator()->enrol_user($this->student->id, $this->course->id, $studentrole->id));
	}

	public function test_pretest_posttest() {
		global $CFG, $DB;
		$student = $this->student;
		$course = $this->course;

		$this->create_and_take_quiz($course, $student, 'Infection Control Pretest');
		$this->create_and_take_quiz($course, $student, 'Infection Control Posttest');

        // Mark course as complete.
		$ccompletion = new completion_completion(array('course' =>  $course->id, 'userid' => $student->id));
        $ccompletion->mark_complete();

        $result = $DB->get_record_sql("SELECT pretest, posttest from phpu_report_tincan_grades where userid = ?", array($student->id));
		$this->assertEquals($result->pretest, 100, 'retrieved pretest');
		$this->assertEquals($result->posttest, 100, 'retrieved posttest');
	}

	public function test_pretest_no_posttest() {
		global $CFG, $DB;
		$student = $this->student;
		$course = $this->course;

		$this->create_and_take_quiz($course, $student, 'Infection Control Pretest');

        // Mark course as complete.
		$ccompletion = new completion_completion(array('course' =>  $course->id, 'userid' => $student->id));
        $ccompletion->mark_complete();

        $result = $DB->get_record_sql("SELECT pretest, posttest from phpu_report_tincan_grades where userid = ?", array($student->id));
		$this->assertEquals($result->pretest, 100, 'retrieved pretest');
		$this->assertNull($result->posttest, 'retrieved nonexisting posttest');
	}

	public function test_pretest_fake_posttest() {
		global $CFG, $DB;
		$student = $this->student;
		$course = $this->course;

		$this->create_and_take_quiz($course, $student, 'Infection Control Pretest');
		$this->create_and_take_quiz($course, $student, 'Posttest Infection Control');

        // Mark course as complete.
		$ccompletion = new completion_completion(array('course' =>  $course->id, 'userid' => $student->id));
        $ccompletion->mark_complete();

        $result = $DB->get_record_sql("SELECT pretest, posttest from phpu_report_tincan_grades where userid = ?", array($student->id));
		$this->assertEquals($result->pretest, 100, 'retrieved pretest');
		$this->assertNull($result->posttest, 'did not detect quiz name with posttest in the title as a posttest');
	}

	public function test_no_pretest_posttest() {
		global $CFG, $DB;
		$student = $this->student;
		$course = $this->course;

		$this->create_and_take_quiz($course, $student, 'Infection Control Posttest');

        // Mark course as complete.
		$ccompletion = new completion_completion(array('course' =>  $course->id, 'userid' => $student->id));
        $ccompletion->mark_complete();

        $result = $DB->get_record_sql("SELECT pretest, posttest from phpu_report_tincan_grades where userid = ?", array($student->id));
		$this->assertNull($result->pretest, 'retrieved nonexisting pretest');
		$this->assertEquals($result->posttest, 100, 'retrieved posttest');
	}

	public function test_fake_pretest_posttest() {
		global $CFG, $DB;
		$student = $this->student;
		$course = $this->course;

		$this->create_and_take_quiz($course, $student, 'Infection Pretest Control');
		$this->create_and_take_quiz($course, $student, 'Infection Control Posttest');

        // Mark course as complete.
		$ccompletion = new completion_completion(array('course' =>  $course->id, 'userid' => $student->id));
        $ccompletion->mark_complete();

        $result = $DB->get_record_sql("SELECT pretest, posttest from phpu_report_tincan_grades where userid = ?", array($student->id));
		$this->assertNull($result->pretest, 'did not detect quiz name with pretest in the title as a pretest');
		$this->assertEquals($result->posttest, 100);
	}

	public function test_case_caps() {
		global $CFG, $DB;
		$student = $this->student;
		$course = $this->course;

		$this->create_and_take_quiz($course, $student, 'Infection Control PRETEST');
		$this->create_and_take_quiz($course, $student, 'Infection Control POSTTEST');

        // Mark course as complete.
		$ccompletion = new completion_completion(array('course' =>  $course->id, 'userid' => $student->id));
        $ccompletion->mark_complete();

        $result = $DB->get_record_sql("SELECT pretest, posttest from phpu_report_tincan_grades where userid = ?", array($student->id));
		$this->assertEquals($result->pretest, 100, 'retrieved pretest when title was in all caps');
		$this->assertEquals($result->posttest, 100, 'retrieved posttest when title was in all caps');
	}

	public function test_case_nocaps() {
		global $CFG, $DB;
		$student = $this->student;
		$course = $this->course;

		$this->create_and_take_quiz($course, $student, 'Infection Control pretest');
		$this->create_and_take_quiz($course, $student, 'Infection Control posttest');

        // Mark course as complete.
		$ccompletion = new completion_completion(array('course' =>  $course->id, 'userid' => $student->id));
        $ccompletion->mark_complete();

        $result = $DB->get_record_sql("SELECT pretest, posttest from phpu_report_tincan_grades where userid = ?", array($student->id));
		$this->assertEquals($result->pretest, 100, 'retrieved pretest when title was in all lowercase');
		$this->assertEquals($result->posttest, 100, 'retrieved posttest when title was in all lowercase');
	}

	public function test_case_barename() {
		global $CFG, $DB;
		$student = $this->student;
		$course = $this->course;

		$this->create_and_take_quiz($course, $student, 'pretest');
		$this->create_and_take_quiz($course, $student, 'posttest');

        // Mark course as complete.
		$ccompletion = new completion_completion(array('course' =>  $course->id, 'userid' => $student->id));
        $ccompletion->mark_complete();

        $result = $DB->get_record_sql("SELECT pretest, posttest from phpu_report_tincan_grades where userid = ?", array($student->id));
		$this->assertEquals($result->pretest, 100, 'retrieved pretest when title was just pretest');
		$this->assertEquals($result->posttest, 100, 'retrieved posttest when title was just posttest');
	}

	public function test_no_pretest_no_posttest() {
		global $CFG, $DB;
		$student = $this->student;
		$course = $this->course;

        // Mark course as complete.
		$ccompletion = new completion_completion(array('course' =>  $course->id, 'userid' => $student->id));
        $ccompletion->mark_complete();

        $result = $DB->get_record_sql("SELECT pretest, posttest from phpu_report_tincan_grades where userid = ?", array($student->id));
		$this->assertNull($result->pretest, 'did not care if pretest did not exist');
		$this->assertNull($result->posttest, 'did not care if posttest did not exist');
	}

	public function test_no_pretest_multiple_posttest() {
		global $CFG, $DB;
		$student = $this->student;
		$course = $this->course;

		$this->create_and_take_quiz($course, $student, 'posttest');
		$this->create_and_take_quiz($course, $student, '2 posttest');

        // Mark course as complete.
		$ccompletion = new completion_completion(array('course' =>  $course->id, 'userid' => $student->id));
        $ccompletion->mark_complete();

        $result = $DB->get_record_sql("SELECT pretest, posttest from phpu_report_tincan_grades where userid = ?", array($student->id));
		$this->assertNull($result->pretest, 'did not care if pretest did not exist');
		$this->assertEquals($result->posttest, 100, 'handled multiple posttests');
	}

	//adapted from mod/quiz/tests/lib_test.php:mod_quiz_lib_testcase::test_quiz_get_completion_state() @e527aff4
	protected function create_and_take_quiz($course, $student, $quizname){
		// Make a quiz with the outcome on.
        $quizgenerator = $this->getDataGenerator()->get_plugin_generator('mod_quiz');
        $data = array('course' => $course->id,
                      'grade' => 100.0,
                      'questionsperpage' => 0,
                      'sumgrades' => 1,
					  'name' => $quizname);
        $quiz = $quizgenerator->create_instance($data);
        $cm = get_coursemodule_from_id('quiz', $quiz->cmid);

        // Create a couple of questions.
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $cat = $questiongenerator->create_question_category();
        $question = $questiongenerator->create_question('numerical', null, array('category' => $cat->id));
        quiz_add_quiz_question($question->id, $quiz);

        $quizobj = quiz::create($quiz->id, $student->id);
        // Set grade to pass.
        $item = grade_item::fetch(array('courseid' => $course->id, 'itemtype' => 'mod',
                                        'itemmodule' => 'quiz', 'iteminstance' => $quiz->id, 'outcomeid' => null));
        $item->gradepass = 80;
        $item->update();

        // Start the passing attempt.
        $quba = question_engine::make_questions_usage_by_activity('mod_quiz', $quizobj->get_context());
        $quba->set_preferred_behaviour($quizobj->get_quiz()->preferredbehaviour);
        $timenow = time();
        $attempt = quiz_create_attempt($quizobj, 1, false, $timenow, false, $student->id);
        quiz_start_new_attempt($quizobj, $quba, $attempt, 1, $timenow);
        quiz_attempt_save_started($quizobj, $quba, $attempt);

        // Process some responses from the student.
        $attemptobj = quiz_attempt::create($attempt->id);
        $tosubmit = array(1 => array('answer' => '3.14'));
        $attemptobj->process_submitted_actions($timenow, false, $tosubmit);

        // Finish the attempt.
        $attemptobj = quiz_attempt::create($attempt->id);
        $this->assertTrue($attemptobj->has_response_to_at_least_one_graded_question());
        $attemptobj->process_finish($timenow, false);
	}
}