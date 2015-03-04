#report_tincan

This is a Moodle plugin based on https://github.com/leo-platforms/moodle-quiz-tincan that
attempts to observe and record data associated with quiz attempt and completion events.

Currently the data are logged in a pseudo-xAPI JSON message format at `/mod/quiz/quizlog.log`.

#Todo
* Quiz question and answer data logging is currently broken
* Change from xAPI messages to logging in a DB
* Add a reporting UI
* Add a dashboard block UI
* Think about how user deletion will be handled
* Think about how access will be restricted to user information
* Who owns copyright?
* Other thinks I haven't thought of that will cause problems later
