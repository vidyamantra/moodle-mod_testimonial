
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

$context = context_module::instance($id);
$files=array();
$postfiles=optional_param('delete-file', null, PARAM_TEXT);

if (isset($postfiles)) {
    //get an array of consecutive files for deletion
    $files=explode(',',$postfiles);
    
    foreach ($files as $value) {
      $file=$value;
      $sql='SELECT id FROM {testimonial_videos} WHERE name = ? AND testimonial_id = ?';    
      $fileid = $DB->get_field_sql($sql, array($file,$testimonial->id));
      
            if(!($DB->record_exists('files', array('contextid' =>$context->id, 'itemid'=>$fileid)))){  
                 $DB->delete_records('testimonial_videos', array ('id'=> $fileid));
             }
            else{
                //delete files from moodle database
                fileDeletion($fileid,$file);
                fileDeletion($fileid,".");
                //delete data from testimonial_videos table
                $vid=$DB->delete_records('testimonial_videos', array ('id'=> $fileid));
             }
     } 
    echo get_string('successdelrecent','testimonial'); 
  }
?>