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

$PAGE->requires->js('/mod/testimonial/js/need.js');

$id = optional_param('cmid', 0, PARAM_INT); // course_module ID, or
$videofile  = optional_param('vf', null, PARAM_RAW); //video file
$audiofile  = optional_param('af', null, PARAM_RAW); //audio file
$url='';$url2='';

if ($id) {
    $cm         = get_coursemodule_from_id('testimonial', $id, 0, false, MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $testimonial  = $DB->get_record('testimonial', array('id' => $cm->instance), '*', MUST_EXIST);
}  
else {
    error('You must specify a course_module ID or an instance ID');
}
require_login($course, true, $cm);
$context = context_module::instance($cm->id);

/// Print the page header
$PAGE->set_pagelayout('popup');
$PAGE->set_url('/mod/testimonial/view.php', array('id' => $cm->id));
$PAGE->set_title(format_string($testimonial->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);

// Output starts here                         
echo $OUTPUT->header();

 if(isset($videofile)){
     
        //fetching video details from database
       $queryv = $DB->get_records_sql('SELECT * FROM {testimonial_videos} WHERE name = ? AND testimonial_id = ?', array($videofile,$testimonial->id));
       foreach ($queryv as $value) { 
                $vid=$value->id;
                $url=$value->url;
                $title=$value->videotitle;
        }
         //fetching audio details from database  
      // $audioexist= $DB->record_exists('testimonial_videos', array('testimonial_id' =>$testimonial->id, 'name'=>$audiofile));
      //     print_r($audioexist);
       
        if($audiofile!='combined'){
          $querya = $DB->get_records_sql('SELECT * FROM {testimonial_videos} WHERE name = ? AND testimonial_id = ?', array($audiofile,$testimonial->id));
          foreach ($querya as $value) { 
                $aid=$value->id;
                $url2=$value->url;
          }
        }
        
        echo html_writer::start_tag('div', array('class'=>'youwatching','align'=>'center'));
          $table=create_table();
              $table->size[] = '150px';
              $table->size[] = '550px';
              $table->size[] = '150px';

               $watchtable=array();
              //close window icon
              // $closewindow = html_writer::link("javascript:window.close()", html_writer::empty_tag('img', array('src'=>$OUTPUT->pix_url('t/close'),'class'=>'icon'),array('class'=>'watch','id' => 'close')));
                //testimonial title display
                $watchtable[]=''; $watchtable[]=get_string('youwatching','testimonial').html_writer::start_tag('span', array('class'=>'mainheading')).$title.html_writer::end_tag('span'); $watchtable[]='';

                $table->data[]=$watchtable;
                $watchtable=array();
                 //audio video display block
                 //check brower compatibility
                if($audiofile!='combined'){
                  $audiobuff=html_writer::start_tag('audio', array('src'=> $url2 , 'id' => 'audio','class'=>'audiowatch')).html_writer::end_tag('audio');//audio player
                }
                else{
                   $audiobuff=''; 
                }
                  $videobuff=html_writer::start_tag('div', array('id' => 'video-container')).html_writer::start_tag('video', array('src'=> $url, 'id' => 'video','class'=>'videowatch')).html_writer::end_tag('video').html_writer::end_tag('div');//video player
                 
                  $seeking= html_writer::empty_tag('input', array('type' => 'range', 'id'=>'seek-bar', 'class'=>'watchbar'));
                  $seeking= $seeking.' '.html_writer::empty_tag('input', array('type' => 'button','value' => 'Replay','id'=>'replayvid', 'class'=>'watch2'));//replay button

                 $watchtable[]=''; $watchtable[]=$videobuff.$seeking.$audiobuff; $watchtable[]='';

                 $table->data[]=$watchtable;
                 
                // $watchtable=array();
               //  $watchtable[]=''; $watchtable[]=$seeking; $watchtable[]='';
               //  $table->data[]=$watchtable;
                 
                 
                 $watchtable=array();
                 // play/pause,seek bar,replay,mute/unmute,volume bar controls
                  $controls= html_writer::empty_tag('input', array('type' => 'button','value' => 'Play','id'=>'play-pause', 'class'=>'watch2'));//play button
                  $controls= $controls.' '.html_writer::empty_tag('input', array('type' => 'button', 'value' => get_string('mutewatch','testimonial'),'id'=>'mute', 'class'=>'watch2'));//mute button
                  $controls= $controls.' '.html_writer::empty_tag('input', array('type' => 'range', 'id'=>'volume-bar', 'class'=>'watchbarmute', 'min'=>'0', 'max'=>'1','step'=>'0.1', 'value'=>'1'));//mute bar
                  
                  $controls= $controls.' '.html_writer::start_tag('span', array('id'=>'durationtime', 'class'=>'duration')).'00:00'.html_writer::end_tag('span');//play duration
                  $controls= $controls.''.html_writer::start_tag('span', array('id'=>'currenttime', 'class'=>'duration')).'00:00'.html_writer::end_tag('span');//play duration
                 // <span id="curtimetext">00:00</span> / <span id="durtimetext">00:00</span>
                  
                 $watchtable[]=''; $watchtable[]=$controls; $watchtable[]='';
                 $table->data[]=$watchtable;

                if(checkBrowser()!='chrome'){
                  $watchtable=array();
                  $ischrome= html_writer::start_tag('div', array('class'=>'alert alert-error'));
                  $ischrome= $ischrome.get_string('usechromewatch','testimonial');
                  $ischrome= $ischrome.html_writer::end_tag('div');

                  $watchtable[]=''; $watchtable[]=$ischrome; $watchtable[]='';
                  $table->data[]=$watchtable;
                }

            echo html_writer::table($table); 
         echo html_writer::end_tag('div');

        $eventdata = array();
        $eventdata['context'] = $context;
        $eventdata['objectid'] = $vid;
        $eventdata['userid'] = $USER->id;
        $eventdata['courseid'] = $course->id;

        $event = \mod_testimonial\event\video_revealed::create($eventdata);
        $event->add_record_snapshot('course', $course);
        $event->add_record_snapshot('course_modules', $cm);
        $event->trigger();
     }
    else{
      echo 'error! Testimonial not found';     
    }
// Finish the page
echo $OUTPUT->footer();
