<?php
class block_tincan extends block_list {
    public function init() {
        $this->title = get_string('transcript', 'block_tincan');
    }
    
  public function get_content() {
    global $USER;
    if ($this->content !== null) {
      return $this->content;
    }
 
    $this->content         = new stdClass;
    $this->content->items  = array();
    $this->content->icons  = array();

    $this->content->items[] = html_writer::tag('a', get_string('my_grades', 'block_tincan'), array('href' => '/blocks/configurable_reports/viewreport.php?id=13'));
    $this->content->icons[] = html_writer::empty_tag('img', array('src' => 'about:blank, 'class' => 'icon'));
 
    $context = context_user::instance($USER->id);
    
    if(has_capability('report/configreports:managereports', $context, $USER->id){
      $this->content->items[] = html_writer::tag('a', get_string('learner_grades', 'block_tincan'), array('href' => '/blocks/configurable_reports/viewreport.php?id=12'));
      $this->content->icons[] = html_writer::empty_tag('img', array('src' => 'about:blank, 'class' => 'icon'));
    }
 
    return $this->content;
  }
  
  public function applicable_formats() {
    return array('site' => true);
  }
}
