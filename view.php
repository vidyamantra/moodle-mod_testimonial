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
//$PAGE->requires->css('/mod/testimonial/style.css');

$id = optional_param('id', 0, PARAM_INT); // course_module ID, or
$n  = optional_param('n', 0, PARAM_INT);  // testimonial instance ID - it should be named as the first character of the module

if ($id) {
    $cm         = get_coursemodule_from_id('testimonial', $id, 0, false, MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $testimonial  = $DB->get_record('testimonial', array('id' => $cm->instance), '*', MUST_EXIST);
} elseif ($n) {
    $testimonial  = $DB->get_record('testimonial', array('id' => $n), '*', MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $testimonial->course), '*', MUST_EXIST);
    $cm         = get_coursemodule_from_instance('testimonial', $testimonial->id, $course->id, false, MUST_EXIST);
} else {
    error('You must specify a course_module ID or an instance ID');
}

require_login($course, true, $cm);
$context = context_module::instance($cm->id);

 $eventdata = array();
    $eventdata['objectid'] = $testimonial->id;
    $eventdata['context'] = $context;

    $event = \mod_testimonial\event\course_module_viewed::create($eventdata);
    $event->add_record_snapshot('course_modules', $cm);
    $event->add_record_snapshot('course', $course);
    $event->trigger();
 
/// Print the page header
$PAGE->set_url('/mod/testimonial/view.php', array('id' => $cm->id));
$PAGE->set_title(format_string($testimonial->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);

// other things you may want to set - remove if not needed
//$PAGE->set_cacheable(false);
//$PAGE->set_focuscontrol('some-html-id');
//$PAGE->add_body_class('testimonial-'.$somevar);

$completion = new completion_info($course);
$completion->set_module_viewed($cm);

// Output starts here                         
echo $OUTPUT->header();
//Heading display
$heading=$OUTPUT->heading(format_string($testimonial->name), 3, null);
echo $heading;

//Question/description asked by teacher/admin
$question= $OUTPUT->box(format_module_intro('testimonial', $testimonial, $cm->id), 'generalbox mod_introbox', 'testimonialintro'); 
echo $question;

//student time duration for deletion
$studenttime = $testimonial->studenttime;
//teacher can delete
$teacherdelete = $testimonial->teacherdelete;
$totalpages='';

$PAGE->requires->js('/mod/testimonial/js/record.js');

//get all submit values into variables
$postdatabase= optional_param('database', null, PARAM_RAW);
$postdelete= optional_param('delete', null, PARAM_RAW);
$getvidname= optional_param('vidname', null, PARAM_TEXT);
$getrecord= optional_param('action', null, PARAM_TEXT);

//if(isset($postdatabase)){
//    $_SESSION['flip']=0;
//}
//if(isset($postdelete)){
//    $_SESSION['flip']=0;
//}
//if(isset($postback)){
//    $_SESSION['flip']=1;
//}

  //calling function for updating serial numbers of each record into table
  updateserialnum();
  
//display the testimonial live recording page
if(!isset($postdatabse) && isset($getrecord)){

  if(checkBrowser()!='chrome'){
      echo html_writer::start_tag('div', array('class'=>'alert alert-error'));
        echo get_string('usechromerecord','testimonial');
      echo html_writer::end_tag('div');
   }
    
   //create new table
   $table=createtable();
    $table->size[] = '250px';

       $OUTPUT->heading(get_string('recheading', 'testimonial'), 4, null);
         $recordingtable=array();

          $recordingtable[]= get_string('videotitle','testimonial');
          //text box for testimonial title
          $recordingtable[]= html_writer::empty_tag('input', array('type' => 'text','name'=>'videotitle','id'=>'videotitle', 'class'=>'titlebutton', 'onchange'=>'saveVideoTitle(this.value)'));        
          $recordingtable[]='';

          $table->data[]=$recordingtable;
          $recordingtable=array();
          
          $recordingtable[]=get_string('testirecording','testimonial');
          //record button
          if(has_capability('mod/testimonial:record', $context)){
             $recordingbutton= html_writer::empty_tag('input', array('type' => 'submit','name'=>'record', 'value' => get_string('record','testimonial'),'id'=>'record', 'class'=>'recordbutton'));
          }
          else{
             $recordingbutton='';
          }
             //stop button
            $recordingbutton= $recordingbutton.html_writer::empty_tag('input', array('type' => 'button','name'=>'stop', 'value' => get_string('stop','testimonial'),'id'=>'stop', 'class'=>'stopbutton','disabled'=>'disabled' ));

           //recent testimonial deletion button
          if (has_capability('mod/testimonial:deleterecent', $context)) {
             $recordingbutton= $recordingbutton.html_writer::empty_tag('input', array('type' => 'button','name'=>'delete', 'value' => get_string('deletefiles','testimonial'),'id'=>'delete', 'class'=>'deletefilesbutton','disabled'=>'disabled' ));
          }
          else{
            $recordingbutton= $recordingbutton.''; 
         }

          $recordingtable[]= $recordingbutton; $recordingtable[]='';
          
          $table->data[]= $recordingtable;
          $recordingtable=array();
          //testimonial recording container
          $recbox= html_writer::start_tag('video', array('id' => 'preview','class'=>'videopreview','controls'=>'controls'));
          $recbox= $recbox.html_writer::end_tag('video');
          
          $recordingtable[]=''; $recordingtable[]=$recbox; $recordingtable[]='';

          $table->data[]=$recordingtable;
          $recordingtable=array();
          
          //uploading processing bar
          $recordingtable[]=get_string('uploading','testimonial');
          $recordingtable[]= html_writer::start_tag('div', array('id' => 'container','class'=>'uploadingbar')).html_writer::end_tag('div'); $recordingtable[]='';

          $table->data[]=$recordingtable;
         echo html_writer::table($table);
         //button for previous testimonial store page
         echo html_writer::tag('form',html_writer::empty_tag('input', array('type' => 'submit','name'=>'database', 'value' => get_string('store','testimonial'),'id'=>'store', 'class'=>'databasesbutton')), array('method' => 'post', 'action' => "view.php?id={$cm->id}"));
           
      echo html_writer::end_tag('form');
      echo '<script>window.uniqueId ='.$id.'; </script>';
      $PAGE->requires->js('/mod/testimonial/js/record2.js');		
      
        //calling function for updating serial numbers of each record into table
      updateserialnum();
 }

//display main page or testimonial records page, firstly 
if(!empty($getvidname) || (isset($id) && !isset($getrecord))){
    
     echo $OUTPUT->heading(get_string('subheading', 'testimonial'), 4, null);
        $page = optional_param('page', 0, PARAM_INT); 
  
      //code block for deletion either multiple or single deletion
     if(isset($postdelete) || !empty($getvidname)) {   
         $postvideoarr= optional_param_array('videoarr', null, PARAM_RAW);
          if(isset($postvideoarr)){
           $names=$postvideoarr;
          }

          else if(isset($getvidname)){
           $getvidarr=array();
           $getvidarr[0]=$getvidname;
           $names=$getvidarr;
          }

          if(!empty($postvideoarr) || !empty($names)){
           //calling funtion for deleting testimonials
            testimonialdeletion($names);
            
              if (has_capability('mod/testimonial:isadmin', $context) || has_capability('mod/testimonial:isteacher', $context)) {
                 echo html_writer::start_tag('div', array('class'=>'alert alert-success')); echo get_string('deleteprint', 'testimonial');echo html_writer::end_tag('div');
              }
          }
          else{
            echo html_writer::start_tag('div', array('class'=>'alert alert-error')); echo get_string('selectfile', 'testimonial');echo html_writer::end_tag('div');
         }
     }
     //calling function for updating serial numbers of each record into table
        updateserialnum();
        
      //select no. of records per page    
       $pagestart= ($page*10)+1;
       $endpage= $pagestart+10-1;

     //query for admin  
      if (has_capability('mod/testimonial:isadmin', $context) || has_capability('mod/testimonial:isteacher', $context)) {
            $queryall= $DB->get_records_sql("SELECT * FROM {testimonial_videos} WHERE testimonial_id=$testimonial->id");
        if(isset($page) && $page>0){
            $query= $DB->get_records_sql("SELECT * FROM {testimonial_videos} WHERE rowscount>=$pagestart AND rowscount<=$endpage AND testimonial_id=$testimonial->id");
         }
        else{
            $query= $DB->get_records_sql("SELECT * FROM {testimonial_videos}  WHERE rowscount<=$endpage AND testimonial_id=$testimonial->id");
        }
      }
    //for student  
     else{
        $query= $DB->get_records_sql("SELECT * FROM {testimonial_videos} WHERE user_id=$USER->id AND rowscount>=$pagestart AND rowscount<=$endpage AND testimonial_id=$testimonial->id");
        $queryall= $DB->get_records_sql("SELECT * FROM {testimonial_videos} WHERE user_id=$USER->id AND testimonial_id=$testimonial->id");
      }
      
      
   if(!$query || !$queryall){
           if (has_capability('mod/testimonial:record', $context) && !(has_capability('mod/testimonial:isadmin', $context))) {
              echo html_writer::start_tag('form', array('method' => 'post', 'action' => "view.php?id={$cm->id}&action=record"));
              
              echo html_writer::start_tag('div', array('class'=>'alert alert-success')); echo get_string('printrecordtestimoniallikeque', 'testimonial'); echo html_writer::end_tag('div');
              echo html_writer::empty_tag('input', array('type' => 'submit','name'=>'back', 'value' => get_string('backbutton2','testimonial'),'id'=>'backbutton'));
              
              echo html_writer::end_tag('form');
           }
           else if ((has_capability('mod/testimonial:isadmin', $context) || has_capability('mod/testimonial:isteacher', $context)) ) {
              echo html_writer::start_tag('div', array('class'=>'alert alert-error')); echo get_string('existprint', 'testimonial'); echo html_writer::end_tag('div');
            }
    }
            
  else { 
            //table for student stats
            $table=createtable();
            $table->size[] = '180px';
            $table->size[] = '800px';
          
             $stattable=array();
             $row='';
             
             $stattable[]=get_string('totaltestimonials', 'testimonial');
          
             $counter=0;
             foreach ($queryall as $value) {
                 $testimonialcount=$counter++;
              }
             $totaltestimonial=round($testimonialcount/2);
             $stattable[]= $totaltestimonial;
            
          if ((has_capability('mod/testimonial:isadmin', $context) || has_capability('mod/testimonial:isteacher', $context))) {
             $table->data[] =$stattable;
             $stattable=array();
             $stattable[]=get_string('totalstudents', 'testimonial');
             
             //calling function for student statistics
             $stattable[]=studentstats($totaltestimonial);
           }
         
          $table->data[] =$stattable;
             
           if (has_capability('mod/testimonial:isstudent', $context) && !(has_capability('mod/testimonial:isadmin', $context))) { 
              $stattable=array();
              $stattable[]=get_string('studentimetext', 'testimonial');
              //time in minutes or seconds
              if($studenttime>99){
                 $studenttimeshow=round($studenttime/60).' minutes';
              }
              else{
                 $studenttimeshow=$studenttime.' seconds';
              }
              $stattable[]=$studenttimeshow;
              $table->data[] =$stattable;        
           }
         
             $stattable=array();
             
             $stattable[]=get_string('testimonialstore', 'testimonial');
             if (has_capability('mod/testimonial:isstudent', $context) && !(has_capability('mod/testimonial:isadmin', $context))) {
                 //button for new testimonial recording
                $stattable[]= html_writer::tag('form',html_writer::empty_tag('input', 
                array('type' => 'submit','name'=>'back', 'value' => get_string('backbutton2','testimonial'),'id'=>'backbutton2')),
                array('method' => 'post', 'action' => "view.php?id={$cm->id}&action=record"));
              }
             else{
                 $stattable[]='';
             }
            $table->data[] =$stattable;
        echo html_writer::table($table);
        
       //delete/select heading display till time limit 
        $showdelete=false; $showselect=false;
        foreach ($query as $value) { 
            $datetime = $value->datetime;

            $timelimit= $datetime+$studenttime;
            $expire = date($timelimit);
            $current = date(time());
            
           if(($expire > $current)){  
              if(has_capability('mod/testimonial:isstudent', $context) && $studenttime && !(has_capability('mod/testimonial:isadmin', $context))){  
               $showdelete=TRUE;
              }
            }
           else{
              $showselect=TRUE;
           }
        }
     
    echo html_writer::start_tag('form', array('method' => 'post', 'action' => '','id'=>'frm1'));
    echo html_writer::start_tag('div', array('id'=>'storetable'));
            
     //display testimonial database in table format       
     $table=createtable();

           $table->head[] = 'S No.'; $table->size[] = '90px'; $table->align[] = 'center';

           if (has_capability('mod/testimonial:isadmin', $context) || has_capability('mod/testimonial:isteacher', $context)) {
                 $table->head[] = 'Student Name';$table->size[] = '250px';//$table->align[] = 'left';
           }
           $table->head[] = 'Title of Testimonial';$table->size[] = '300px';//$table->align[] = 'left';
           $table->head[] = 'Date/Time';$table->size[] = '400px';//$table->align[] = 'left';
            
           if(has_capability('mod/testimonial:isadmin', $context) || (has_capability('mod/testimonial:isteacher', $context) && $teacherdelete && $showselect)){ 
              $table->head[] = 'Select'; $table->align[] = 'center';
           }
           
           if(has_capability('mod/testimonial:isstudent', $context) && $studenttime && !(has_capability('mod/testimonial:isadmin', $context))){ 
             if($showdelete){  
              $table->head[] = 'Delete';$table->align[] = 'center';
             }
           }
     
      //retrieve all values from query   
      foreach ($query as $value) { 
          $dataarr=array();
            
           // $vid = $value->id;
           // $testimonialid = $value->testimonial_id;
           // $userid = $value->user_id;
           // $name =$value->name ;
           // $videotitle = $value->videotitle;
           // $datetime = $value->datetime;
           // $replycount = $value->replycount;
           // $rowscount = $value->rowscount;
            $videoids=$value->id.'/'.$value->name;
               
                $revitem= strrev($value->name);
                $str = $revitem;
                $char=substr( $str, 0, 4 );
                
                if($char=='mbew') {
                    $dataarr[]=$value->rowscount;
                }
                
                $sql='SELECT firstname,lastname FROM {user} WHERE id = ?';  
                $username = $DB->get_record_sql($sql, array($value->user_id));
               // $username = $DB->get_field_sql($sql, array($userid));
                
                if($char=='mbew') {
                    if (has_capability('mod/testimonial:isadmin', $context) || has_capability('mod/testimonial:isteacher', $context)) {  
                        $dataarr[]=$username->firstname.' '.$username->lastname;
                    }
                   //here open the popup window for testimonial recording then you can watch  
                   $cropeditem= substr($revitem,4);
                   $videofile=  trim(strrev($cropeditem).'webm');
                   $audiofile=  trim(strrev($cropeditem).'wav');
                   $dataarr[]="<a  href=\"javascript:create_window('watch.php?vf=$videofile&af=$audiofile&cmid=$id')\">$value->videotitle</a><br />";
                
        
                  // $datetimeformat=date("Y-m-d h:i:s", $datetime);
                   $dataarr[]=userdate($value->datetime);
                }
               
            
                $timelimit= $value->datetime+$studenttime;
                $expire = date($timelimit);
                $current = date(time());
               
                if(has_capability('mod/testimonial:isstudent', $context) && $char=='mbew' && $studenttime && !(has_capability('mod/testimonial:isadmin', $context))){ 
                    if(($expire > $current)){  
                        //individual deletion for student only
                        $delurl = new moodle_url("view.php?id={$cm->id}&page=$page&vidname=$videoids");
                        $lastcolumn =  html_writer::link($delurl,
                                                                html_writer::empty_tag('img', array('src'=>$OUTPUT->pix_url('t/delete'),'class'=>'iconsmall'),
                                                                array('class'=>'titlebutton','id' => $videoids, 'onclick'=>'getvideoid(this.id)', 'name'=> 'singledelurl')));
                        $dataarr[] = $lastcolumn;
                    }     
                }   
                
                 if(has_capability('mod/testimonial:isadmin', $context)){
                    if($char=='mbew'){
                        //individual check box for each record
                        $dataarr[]="<input type=checkbox name=videoarr[] value='$videoids' />";
                    }
                 }
                 else if(has_capability('mod/testimonial:isteacher', $context) && $teacherdelete){
                     if($char=='mbew' && !($expire > $current)){
                        //individual check box for each record
                        $dataarr[]="<input type=checkbox name=videoarr[] value='$videoids' />";
                    }
                 }
                  
                 if($char=='mbew') {
                     $table->data[] =$dataarr; 
                 }
                 
        }
             
        if(has_capability('mod/testimonial:isadmin', $context) || (has_capability('mod/testimonial:isteacher', $context) && $teacherdelete && $showselect) ){
            $dataarr=array();
            $dataarr[]='';$dataarr[]='';$dataarr[]='';$dataarr[]=get_string('selectall', 'testimonial');;
            $dataarr[]= html_writer::empty_tag('input', array('type' => 'checkbox','name'=>'checkall','onclick'=>'checkedAll(frm1)','id'=>'checkall'));//checkbox for select all
            $table->data[] =$dataarr;
         }
     
      //close or write table
     echo html_writer::table($table);
     
       //multiple delete button
       if (has_capability('mod/testimonial:deletemultiple', $context)) {
            if(has_capability('mod/testimonial:isadmin', $context) || (has_capability('mod/testimonial:isteacher', $context) && $teacherdelete && $showselect)){
              echo html_writer::empty_tag('input', array('type' => 'submit','name'=>'delete', 'value' => get_string('deletemultiple','testimonial'),'id'=>'deletemul', 'class'=>'deletemulbutton' ));
            }
        } 
         //pagination bar, dynamically arranged pages and each of have 10 records  
        $totalpages=floor(($totaltestimonial-1)/10)+1;
      // echo $totalpages;
       echo $OUTPUT->paging_bar($totalpages*10, $page, 10, "view.php?id={$cm->id}&page=$page");
       echo html_writer::end_tag('div');
          
     //close form       
   echo html_writer::end_tag('form');  
        
   }
}
// Finish the page
echo $OUTPUT->footer();
