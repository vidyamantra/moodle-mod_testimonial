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

/**
 * The mod_testimonial course module viewed event.
 *
 * @package mod_testimonial
 * @copyright 2014 Krishna Pratap Singh <krishna@vidyamantra.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(__FILE__).'/locallib.php');
require_once ($CFG->dirroot.'/course/moodleform_mod.php');

$id = optional_param('cmid', 0, PARAM_INT);
if ($id) {
    $cm         = get_coursemodule_from_id('testimonial', $id, 0, false, MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $testimonial  = $DB->get_record('testimonial', array('id' => $cm->instance), '*', MUST_EXIST);
} 
$context = context_module::instance($cm->id);

 // Conditions to show the intro can change to look for own settings or whatever
 $question= $testimonial->intro;

 $videotitle= optional_param('vtitle', null, PARAM_RAW);
 if(strcmp($videotitle, '[object HTMLInputElement]')==0){
    $videotitle=get_string('untitled', 'testimonial');;
 }
 
 $result = $DB->get_records_sql("SELECT * FROM {testimonial_videos} WHERE user_id=$USER->id AND testimonial_id=$testimonial->id");
 
 $replycount=0;
 foreach ($result as $value) {
  if(isvideofile($value->name)=='mbew'){
     $replycount++;
  }
} 
 $rowscount=$DB->count_records('testimonial_videos', array('testimonial_id'=>$testimonial->id));
  
 $completion = new completion_info($course);
 $completion->set_module_viewed($cm);
   
foreach(array('video', 'audio') as $type) {
    if (isset($_FILES["${type}-blob"])) {
    
       $uploaded_file_path = $_FILES["${type}-blob"]["tmp_name"];  // temp path to the actual file
        $filename = $_POST["${type}-filename"];                // the original (human readable) filename
        
          //insert record into videos table except url
           $record = new stdClass();
           $record->testimonial_id=$testimonial->id;
           $record->user_id = $USER->id;
           $record->name = $filename;
           $record->videotitle = $videotitle;
           $record->question = $question;
           $record->datetime = time();
         
         $lastinsertid = $DB->insert_record('testimonial_videos', $record, false);

         $sql='SELECT id FROM {testimonial_videos} WHERE name = ? AND testimonial_id = ?';    
         $mediaid = $DB->get_field_sql($sql, array($filename,$testimonial->id));

         //store data into moodle database
        $fileinfo = array(
            'contextid' => $context->id,
            'component' => 'mod_testimonial',    // mod_[your-mod-name]
            'filearea' => 'testimonial_docs',   // arbitrary string
            'itemid' => $mediaid,           // use a unique id in the context of the filearea and you should be safe
            'filepath' => '/',              // virtual path
            'filename' => "$filename");       // virtual filename

        $file_storage = get_file_storage();
        //check file exist or not
        if ($file_storage->file_exists($fileinfo['contextid'],
                             $fileinfo['component'],
                             $fileinfo['filearea'],
                             $fileinfo['itemid'],
                             $fileinfo['filepath'],
                             $fileinfo['filename'])) return false; // (this code is actually in a function)

        $file = $file_storage->create_file_from_pathname($fileinfo, $uploaded_file_path);

         $url=get_testimonial_doc_url((int)$mediaid);
       // $url1="$url";

         //update the testimonial_videos table with url
         $update = new stdclass;
            $update->id = $mediaid;
            $update->url = "$url";
            $update->replycount = $replycount;
            $update->rowscount = $rowscount;
            
         $lastupdate=$DB->update_record('testimonial_videos', $update);
     }
 }
    $eventdata1 = array();
    $eventdata1['context'] = $context;
    $eventdata1['objectid'] = $mediaid;
    $eventdata1['userid'] = $USER->id;
    $eventdata1['courseid'] = $course->id;

    $event = \mod_testimonial\event\video_submitted::create($eventdata1);
    $event->add_record_snapshot('course', $course);
    $event->add_record_snapshot('course_modules', $cm);
    $event->trigger();  

      if($completion->is_enabled($cm) && $testimonial->completionrecord) {
         $completion->update_state($cm,COMPLETION_COMPLETE);
     }
?>