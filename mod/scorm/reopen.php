<?php

require_once('../../config.php');
require_once($CFG->dirroot.'/mod/scorm/locallib.php');

$scormid = required_param('scormid',PARAM_INT);
$scoid = required_param('scoid',PARAM_INT);
$attempt = required_param('attempt',PARAM_INT);
$userid = required_param('userid',PARAM_INT);

$cm = $DB->get_record('course_modules',array('instance'=>$scormid));
$contextmodule = context_module::instance($cm->id);
$course = $DB->get_record('course', array('id'=>$cm->course), '*', MUST_EXIST);

require_login($course,false,$cm);
require_capability('mod/scorm:viewreport', $contextmodule);

$PAGE->set_url('/mod/scorm/reopen.php',array('scormid'=>$scormid,'scoid'=>$scoid,'attempt'=>$attempt,'userid'=>$userid));
$PAGE->set_title('Reopen SCORM attempt');
$PAGE->set_heading('Reopen SCORM attempt');
$PAGE->set_pagelayout('standard');

echo $OUTPUT->header();

$elements = $DB->get_records('scorm_scoes_track',array('scormid'=>$scormid,'scoid'=>$scoid,'attempt'=>$attempt,'userid'=>$userid,'element'=>'cmi.completion_status'));

if($elements) {
	foreach($elements as $element) {
		$element->value = 'incomplete';
		$DB->update_record('scorm_scoes_track',$element);
		echo '<p>Attempt reopened.</p>';
	}
} else {
	echo '<p>No such attempt.</p>';
}

echo '<p><a href="'.$CFG->wwwroot.'/mod/scorm/report.php?id='.$cm->id.'">Back to report</a>.</p>';

echo $OUTPUT->footer();
