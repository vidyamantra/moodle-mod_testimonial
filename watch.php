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
require_once ($CFG->dirroot.'/course/moodleform_mod.php');
$PAGE->requires->css('/mod/testimonial/style.css');

global $DB,$USER,$PAGE;
$cmid = optional_param('cmid', 0, PARAM_INT);
if ($cmid) {
    $cm         = get_coursemodule_from_id('testimonial', $cmid, 0, false, MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $testimonial  = $DB->get_record('testimonial', array('id' => $cm->instance), '*', MUST_EXIST);
} 

 require_login($course, true, $cm);
$context = context_module::instance($cm->id);

$PAGE->set_url('/mod/testimonial/view.php', array('id' => $cm->id));
$PAGE->set_title(format_string($testimonial->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);

    $completion = new completion_info($course);
    if($completion->is_enabled($cm) && $testimonial->completionwatch) {
         $completion->update_state($cm,COMPLETION_COMPLETE);
     }
     
$id2='';$url='';$url2='';$testimonialid='';
 $avid = optional_param('id', 0, PARAM_INT);
    
    if(isset($avid)){
   $testimonialid=$testimonial->id;
       //fetching video from database
       $query = $DB->get_records_sql('SELECT * FROM {testimonial_videos} WHERE id = ? AND testimonial_id = ?', array($avid,$testimonial->id));

       foreach ($query as $value) { 
                $name=$value->name;
                $revitem= strrev($name);
                $str = $revitem;

               $char=substr( $str, 0, 1 );
                if($char=='m') {
                   $cropeditem= substr($revitem,4);
                   $audioname=  trim(strrev($cropeditem).'wav');
                   $url=$value->url;

                 }
               else{
                   $cropeditem= substr($revitem,3);
                   $audioname=  trim(strrev($cropeditem).'webm');
                   $url2=$value->url;
               } 

                $title=$value->videotitle;

              if(!($DB->record_exists('testimonial_watching', array('user_id' =>$USER->id, 'testimonial_id'=>$testimonialid, 'video_id'=>$avid)))){  
                 $record1 = new stdClass();
                 $record1->user_id = $USER->id;
                 $record1->testimonial_id = $testimonialid;
                 $record1->video_id=$avid;
                 $lastinsertid1 = $DB->insert_record('testimonial_watching', $record1, false);
              }
           }

          //getting id of second a/v file with unique name
        $query2 = $DB->get_records_sql('SELECT * FROM {testimonial_videos} WHERE name = ? AND testimonial_id = ?', array($audioname,$testimonial->id));
            
            foreach ($query2 as $value2) { 
              if($char=='m') {
                $id2=$value2->id;
                $url2=$value2->url;
              }
              else{
                $id2=$value2->id;
                $url=$value2->url;
              }
              //check record exist or not
               if(!($DB->record_exists('testimonial_watching', array('user_id' =>$USER->id, 'testimonial_id'=>$testimonialid, 'video_id'=>$id2)))){
                 $record2 = new stdClass();
                 $record2->user_id = $USER->id;
                 $record2->testimonial_id = $testimonialid;
                 $record2->video_id=$id2;
                 $lastinsertid2 = $DB->insert_record('testimonial_watching', $record2, false);
               }
            }
        //add some required .js and .css file     
        echo '<script src="js/need.js"> </script>';
        echo '<link href="style.css" type="text/css" rel="stylesheet"></link>';
        echo html_writer::start_tag('div', array('class'=>'youwatching','align'=>'center'));
           
            $table = new html_table();
                 $table->align=array();
                 $table->rowclasses = array();
                 $table->size=array();

                  $table->size[] = '100px';
                  $table->align[] = 'left';

                  $table->size[] = '680px';
                  $table->align[] = 'left';

                  $table->size[] = '150px';
                  $table->align[] = 'left';
             
                  $watchtable=array();

                  $watchtable[]='';
                  $watchtable[]=get_string('youwatching','testimonial').$title;
                  $watchtable[]='';

                  $table->data[]=$watchtable;
                  $watchtable=array();
                     
                     $startdiv=html_writer::start_tag('div', array('id' => 'video-container'));
                     $audiobuff=html_writer::start_tag('audio', array('src'=> $url2 , 'id' => 'audio','class'=>'audiowatch'));echo html_writer::end_tag('audio');
                     $videobuff=html_writer::start_tag('video', array('src'=> $url, 'id' => 'video','class'=>'videowatch')).html_writer::end_tag('video');
                     $enddiv= html_writer::end_tag('div');
                     
                    
                     $watchtable[]=''; 
                     $watchtable[]=$startdiv.$videobuff.$enddiv;
                     $watchtable[]=$audiobuff;
                         
                     $table->data[]=$watchtable;
                     $watchtable=array();
                     
                     echo html_writer::start_tag('div', array('id' => 'video-controls'));
                          $playbutt= html_writer::empty_tag('input', array('type' => 'button','value' => 'Play','id'=>'play-pause', 'class'=>'watch2'));
                          $playrange= html_writer::empty_tag('input', array('type' => 'range', 'id'=>'seek-bar', 'class'=>'watchbar'));
                          $mutebutt= html_writer::empty_tag('input', array('type' => 'button', 'value' => get_string('mutewatch','testimonial'),'id'=>'mute', 'class'=>'watch2'));
                          $muterange= html_writer::empty_tag('input', array('type' => 'range', 'id'=>'volume-bar', 'class'=>'watchbarmute', 'min'=>'0', 'max'=>'1','step'=>'0.1', 'value'=>'1'));
                     echo html_writer::end_tag('div');
                     
                     $watchtable[]=''; 
                     $watchtable[]=$playbutt.' '.$playrange.' '.$mutebutt.' '.$muterange;
                     $watchtable[]='';
                     $table->data[]=$watchtable;
                      
                     $watchtable=array();
                     
                     $startdivbutt=html_writer::start_tag('div', array('id' => 'controlbutton'));
                       $replay = html_writer::link("javascript:history.go(0)",
                                     html_writer::empty_tag('img', array('src'=>$OUTPUT->pix_url('a/refresh'),'class'=>'icon'),
                                     array('class'=>'watch','id' => 'replaywatch')));
                        
                       $closewindow = html_writer::link("javascript:window.close()",
                                     html_writer::empty_tag('img', array('src'=>$OUTPUT->pix_url('t/dockclose'),'class'=>'icon'),
                                     array('class'=>'watch','id' => 'close')));
                     $enddivbutt= html_writer::end_tag('div');
                     
                      $watchtable[]='';
                      $watchtable[]=$startdivbutt.' Refresh '.$replay.'&nbsp&nbsp  Exit'.$closewindow.' '.$enddivbutt;
                      $watchtable[]='';
                           
                     $table->data[]=$watchtable;
                echo html_writer::table($table); 
             echo html_writer::end_tag('div');
           
                         
                     
            $eventdata = array();
            $eventdata['context'] = $context;
            $eventdata['objectid'] = $avid;
            $eventdata['userid'] = $USER->id;
            $eventdata['courseid'] = $course->id;

            $event = \mod_testimonial\event\video_revealed::create($eventdata);
            $event->add_record_snapshot('course', $course);
            $event->add_record_snapshot('course_modules', $cm);
            $event->trigger();  
       }
    
    else{
      echo 'error';     
  }
     


   