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
$PAGE->requires->css('/mod/testimonial/style.css');

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

$heading = $OUTPUT->heading(format_string($testimonial->name), 3, null);
echo $heading;

if ($testimonial->intro) { // Conditions to show the intro can change to look for own settings or whatever
 $question= $OUTPUT->box(format_module_intro('testimonial', $testimonial, $cm->id), 'generalbox mod_introbox', 'testimonialintro'); 
}
else{
 $question = " Sorry no question/dscription "; 
}
echo $question;

$studenttime = $testimonial->studenttime;
$teacherdelete = $testimonial->teacherdelete;

$PAGE->requires->js('/mod/testimonial/js/record.js');

$_SESSION['flip']=0;

//get all submit values into variables
$postdatabase= optional_param('database', null, PARAM_RAW);
$postdelete= optional_param('delete', null, PARAM_RAW);
$postback= optional_param('back', null, PARAM_RAW);
$getvidname= optional_param('vidname', null, PARAM_RAW);

if(isset($postdatabase)){
    $_SESSION['flip']=0;
}
if(isset($postdelete)){
    $_SESSION['flip']=0;
}
if(isset($postback)){
    $_SESSION['flip']=1;
}

//check the user is admin or not
$admins = get_admins();
  $isadmin = false;
  foreach($admins as $admin) {
   if ($USER->id == $admin->id) {
       $isadmin = true;
        break;
      }
  }


//display the testimonial live recording page
if(((!isset($postdatabse)) && (!isset($postdelete)) && ((isset($_POST['back'])) || isset($id))) && $_SESSION['flip']==1){
//create new table
    
$table = new html_table();

     $table->align=array();
     $table->rowclasses = array();
     $table->size=array();

      $table->size[] = '150px';
      $table->align[] = 'left';

      $table->size[] = '300px';
      $table->align[] = 'left';

      $table->size[] = '100px';
      $table->align[] = 'left';

       $OUTPUT->heading(get_string('recheading', 'testimonial'), 4, null);
         $recpaneltable=array();

          $recpaneltable[]= get_string('videotitle','testimonial');
          //text box for testimonial title
          $recpaneltable[]= html_writer::empty_tag('input', array('type' => 'text','name'=>'videotitle','id'=>'videotitle', 'class'=>'titlebutton', 'onchange'=>'saveVideoTitle(this.value)'));        
          $recpaneltable[]='';

          $table->data[]=$recpaneltable;

          $recpaneltable=array();
          $recpaneltable[]=get_string('testirecording','testimonial');

          //record button
          if (has_capability('mod/testimonial:record', $context)) {
            $recordbutt= html_writer::empty_tag('input', array('type' => 'submit','name'=>'record', 'value' => get_string('record','testimonial'),'id'=>'record', 'class'=>'recordbutton'));
          }
          //stop button
            $stopbutt= html_writer::empty_tag('input', array('type' => 'button','name'=>'stop', 'value' => get_string('stop','testimonial'),'id'=>'stop', 'class'=>'stopbutton','disabled'=>'disabled' ));

           //recent testimonial deletion button
          if (has_capability('mod/testimonial:deleterecent', $context)) {
             $deleterecent= html_writer::empty_tag('input', array('type' => 'button','name'=>'delete', 'value' => get_string('deletefiles','testimonial'),'id'=>'delete', 'class'=>'deletefilesbutton','disabled'=>'disabled' ));
          }

          $recpaneltable[]= $recordbutt.$stopbutt.$deleterecent;
          $recpaneltable[]='';
          $table->data[]=$recpaneltable;

          $recpaneltable=array();
          //testimonial recording container
          $recbox= html_writer::start_tag('video', array('id' => 'preview','class'=>'videopreview','controls'=>'controls'));echo html_writer::end_tag('video');
          $recpaneltable[]='';
          $recpaneltable[]=$recbox;
          $recpaneltable[]='';

          $table->data[]=$recpaneltable;

          $recpaneltable=array();
          $recpaneltable[]=get_string('uploading','testimonial');
          //uploading processing bar
          $recpaneltable[]= html_writer::start_tag('div', array('id' => 'container','class'=>'uploadingbar')).html_writer::end_tag('div').get_string('refreshhint','testimonial');
          $recpaneltable[]='';

          $table->data[]=$recpaneltable;

          $recpaneltable=array();
          $recpaneltable[]='';
          if (has_capability('mod/testimonial:godatabase', $context)) {
               $url = new moodle_url('');
               //button for previous testimonial store page
               $backtostore= html_writer::tag('form',html_writer::empty_tag('input', array('type' => 'submit','name'=>'database', 'value' => get_string('store','testimonial'),'id'=>'store', 'class'=>'databasesbutton')), array('method' => 'post', 'action' => ''));
           }
          $recpaneltable[]= $backtostore;
          $recpaneltable[]='';

          $table->data[]=$recpaneltable;
            
          echo html_writer::table($table); 
        echo html_writer::end_tag('form');
      echo '<script>window.uniqueId ='.$id.'; </script>';
      $PAGE->requires->js('/mod/testimonial/js/record2.js');		
   
 }

//display main page or testimonial records page, firstly 
if(((isset($postdatabse)) || (isset($postdelete))  || (isset($getvidname))  || !isset($postback)) && ($_SESSION['flip']==0)){
    
     echo $OUTPUT->heading(get_string('subheading', 'testimonial'), 4, null);
        $page = optional_param('page', 0, PARAM_INT); 

        
      //code block for deletion either multiple or single deletion
     if(isset($postdelete) || isset($getvidname))  {   
          
              if(isset($_POST['videoarr'])){
               $names=$_POST['videoarr'];
              }
              
              else if(isset($getvidname)){
               $getvidarr=array();
               $getvidarr[0]=$getvidname;
               $names=$getvidarr;
              }
               
        if(isset($_POST['videoarr']) || isset($names)){
           foreach($names as $value){
               $idarr=array();
               if(isset($value)){
                   $idarr = (explode('/',$value,2));
                   $itemid=$idarr[0];
                   $itemname=$idarr[1];
                   $aitemid=$idarr[0]+1;

                   //getting file name without extension
                   $revitem= strrev($itemname);
                   $cropeditem= substr($revitem,4);
                   $aitemname=  trim(strrev($cropeditem).'wav');


                   $sql='SELECT videotitle FROM {testimonial_videos} WHERE id = ?';    
                   $vtitle = $DB->get_field_sql($sql, array((int)$itemid));

               //display deleted file
                echo html_writer::start_tag('div', array('class'=>'itemidprint'));
                    echo $vtitle.' |';
                echo html_writer::end_tag('div');  
                
                if(!($DB->record_exists('files', array('contextid' =>$context->id, 'itemid'=>$itemid)))){  
                       $DB->delete_records('testimonial_videos', array ('id'=> $itemid));
                       $DB->delete_records('testimonial_videos', array ('id'=> $aitemid));
                       echo html_writer::start_tag('div', array('class'=>'curruptprint'));
                       echo get_string('curruptprint', 'testimonial');
                       echo html_writer::end_tag('div');  
                }

                 else{
                     //deletion from moodle directory structure
                       fileDeletion($itemid,$itemname,$context->id);
                       fileDeletion($itemid,".",$context->id);
                       fileDeletion($aitemid,$aitemname,$context->id);
                       fileDeletion($aitemid,".",$context->id);
                       //delete data from videos table
                       $vid=$DB->delete_records('testimonial_videos', array ('id'=> $itemid));
                        $aid=$DB->delete_records('testimonial_videos', array ('id'=> $aitemid));
                    }
             
                }
           }

           echo html_writer::start_tag('div', array('class'=>'curruptprint'));
           echo get_string('deleteprint', 'testimonial');
           echo html_writer::end_tag('div');
           echo "<meta http-equiv='refresh' content='3; url=view.php?id={$cm->id}&page=$page'>";
    
        }
              
       else{
           echo get_string('selectfile', 'testimonial');
        }
       
    }
     //updating serial numbers of each record into table
        $queryall= $DB->get_records_sql("SELECT * FROM {testimonial_videos}");
        if(!$isadmin){
          $queryall= $DB->get_records_sql("SELECT * FROM {testimonial_videos} WHERE user_id=$USER->id ");
        }
            $sno=0;
            
          foreach ($queryall as $value) { 
             $vid = $value->id;
             $rowscount = $value->rowscount;
                if($vid%2!=0){
                    $sno++;
                }
             $update = new stdclass;
                  $update->id = $vid;
                  $update->rowscount = $sno;
             $lastupdate=$DB->update_record('testimonial_videos', $update);
          }
         
      //select no. of records per page    
       $pagestart= ($page*10)+1;
       $endpage= $pagestart+10-1;

     //query for admin  
      if($isadmin){
        if(isset($page) && $page>0){
            $query= $DB->get_records_sql("SELECT * FROM {testimonial_videos} WHERE rowscount>=$pagestart AND rowscount<=$endpage");
         }
        else{
            $query= $DB->get_records_sql("SELECT * FROM {testimonial_videos}  WHERE rowscount<=$endpage");
        }
      }
    //for student  
     else{
        $query= $DB->get_records_sql("SELECT * FROM {testimonial_videos} WHERE user_id=$USER->id AND rowscount>=$pagestart AND rowscount<=$endpage");
      }
      
      
   if(!$query || !$queryall){
            if(!$isadmin){
              echo html_writer::tag('form',html_writer::empty_tag('input', array('type' => 'submit','name'=>'back', 'value' => get_string('backbutton','testimonial'),'id'=>'backbutton')), array('method' => 'post', 'action' => "view.php?id={$cm->id}"));
            }
              echo html_writer::start_tag('div', array('class'=>'itemidprint'));
              echo get_string('existprint', 'testimonial');
               echo html_writer::end_tag('div');
    }
            
  else { 
            //table for student stats
             $table = new html_table();
         
             $table->align=array();
             $table->rowclasses = array();
             $table->size=array();
             $table->data = array();
            

             $table->size[] = '180px';
             $table->align[] = 'left';
            
             $table->size[] = '800px';
             $table->align[] = 'left';
          
             $stattable=array();
             $row='';
             
             $stattable[]=get_string('totaltestimonials', 'testimonial');
             foreach ($queryall as $value){
                 $totaltesti=  $value->rowscount;
             }
             $stattable[]= $totaltesti;
            
          if($isadmin){
             $table->data[] =$stattable;
              
             $stattable=array();
             $stattable[]=get_string('totalstudents', 'testimonial');
              
             $sql='SELECT DISTINCT user_id FROM {testimonial_videos} WHERE testimonial_id = ?';    
             $totalstudent = $DB->get_records_sql($sql, array($testimonial->id));
             
             
             foreach($totalstudent as $value){
                 $sql2='SELECT * FROM {testimonial_videos} WHERE user_id = ?';
                 $sql3='SELECT username FROM {user} WHERE id = ?';
                 
                 $totalreplyperstu = $DB->get_records_sql($sql2, array($value->user_id));
                 $usernameper = $DB->get_records_sql($sql3, array($value->user_id));
                 
                 //student names
                 foreach ($usernameper as $value) {
                     $lastcol1=$value->username;
                 }
                 //attempted testimonial
                 foreach ($totalreplyperstu as $value) {
                      $lastcol2=$value->rowscount;
                 }
                 //percentage ratio per student
                  $percentperstu= round(($lastcol2*100)/$totaltesti);
                  $row= $row.$lastcol1." | ".$lastcol2." | ".$percentperstu.'%'.' , ';
                  
               }
             $stattable[]=$row;
           }
         
            
          $table->data[] =$stattable;
             
           if(!$isadmin){ 
               
              $stattable=array();
              $stattable[]=get_string('studentimetext', 'testimonial');
              $stattable[]=$studenttime." hours";
              $table->data[] =$stattable;        
           }
         
             $stattable=array();
             $stattable[]=get_string('testimonialstore', 'testimonial');
             
             if(!$isadmin){
                 //button for new testimonial recording
                $stattable[]= html_writer::tag('form',html_writer::empty_tag('input', 
                array('type' => 'submit','name'=>'back', 'value' => get_string('backbutton2','testimonial'),'id'=>'backbutton2')),
                array('method' => 'post', 'action' => "view.php?id={$cm->id}"));
              }
             else{
                 $stattable[]='';
             }
            $table->data[] =$stattable;
            
        echo html_writer::table($table);
        
     
    echo html_writer::start_tag('form', array('method' => 'post', 'action' => '','id'=>'frm1'));
    echo html_writer::start_tag('div', array('id'=>'storetable'));
            
     //display testimonial database in table format       
     $table = new html_table();
       if(!$admin){  
          $table->attributes['class'] = 'datatable';
        }
             $table->head = array ();
             $table->align=array();
             $table->rowclasses = array();
             $table->size=array();
             $table->data = array();
            

            $h1="S No.";
            $table->head[] = $h1;
            $table->size[] = '70px';
            $table->align[] = 'center';

             if($isadmin){
                 $table->head[] = 'Student Name';
                 $table->size[] = '180px';
                 $table->align[] = 'left';
             }

             $table->head[] = 'Title of Testimonial';
             $table->size[] = '300px';
             $table->align[] = 'left';

            $table->head[] = 'Date/Time';
            $table->size[] = '200px';
            $table->align[] = 'left';
            
           if($isadmin && $teacherdelete){ 
              $table->head[] = 'Select';
              $table->align[] = 'center';
           }
           if(!$isadmin){ 
              $table->head[] = 'Remove';
              $table->align[] = 'center';
           }
     
        date_default_timezone_set("Asia/Calcutta");
           
      //retrieve all values from query   
      foreach ($query as $value) { 
          $dataarr=array();
            
            $vid = $value->id;
            $testimonialid = $value->testimonial_id;
            $userid = $value->user_id;
            $name = $value->name;
            $videotitle = $value->videotitle;
            $datetime = $value->datetime;
            $replycount = $value->replycount;
            $urll = $value->url;
            $rowscount = $value->rowscount;
            $videoids=$vid.'/'.$name;
               
                if($vid%2!=0 ){
                    $mouseover="style.backgroundColor='#f5f5f5'";
                    $mouseout="style.backgroundColor='#FFFFFF'";
                    //table hover effect
                    $table->rowclasses = "onMouseover=$mouseover onMouseout=$mouseout";
                    $dataarr[]=$rowscount;
                }
                
             
                $sql='SELECT username FROM {user} WHERE id = ?';    
                $username = $DB->get_field_sql($sql, array($userid));
                
                if($vid%2 !=0 ){
                    if($isadmin){   
                        $dataarr[]=$username;
                     }
                   //here open the popup window for testimonial recording then you can watch  
                   $dataarr[]="<a  href=\"javascript:create_window('watch.php?id=$vid&cmid=$id')\">$videotitle</a><br />";
                   $datetimeformat=date("Y-m-d H:i:s", $datetime);
                   $dataarr[]=$datetimeformat;
                }
               
            
                if($isadmin && $teacherdelete){
                    if($vid%2!=0){
                        //individual check box for each record
                        $dataarr[]="<input type=checkbox name=videoarr[] value='$videoids' />";
                    }
                }
               
                if(!$isadmin  && $vid%2!=0){ 
                      $timelimit= $datetime+$studenttime*60*60;
                      $expire = date("Y-m-d H:i:s", $timelimit);
                      $current = date("Y-m-d H:i:s");

                    if(($expire > $current)){  
                        //individual deletion for student only
                        $delurl = new moodle_url("view.php?id={$cm->id}&vidname=$videoids");
                        $lastcolumn =  html_writer::link($delurl,
                                                                html_writer::empty_tag('img', array('src'=>$OUTPUT->pix_url('t/delete'),'class'=>'iconsmall'),
                                                                array('class'=>'titlebutton','id' => $videoids, 'onclick'=>'getvideoid(this.id)', 'name'=> 'singledelurl')));
                        $dataarr[] = $lastcolumn;
                    }     
                }   
                  
                 if($vid%2!=0 ){
                     $table->data[] =$dataarr; 
                 }
                 
        }
             
      if($isadmin){
        $dataarr=array();
        $dataarr[]='';$dataarr[]='';$dataarr[]='';$dataarr[]=get_string('selectall', 'testimonial');;
        $dataarr[]= html_writer::empty_tag('input', array('type' => 'checkbox','name'=>'checkall','onclick'=>'checkedAll(frm1)','id'=>'checkall'));//checkbox for select all
        $table->data[] =$dataarr;
      }
      //close or write table
     echo html_writer::table($table);
     
       //multiple delete button
       if (has_capability('mod/testimonial:deletemultiple', $context)) {
            if($isadmin && $teacherdelete){
             echo html_writer::empty_tag('input', array('type' => 'submit','name'=>'delete', 'value' => get_string('deletemultiple','testimonial'),'id'=>'deletemul', 'class'=>'deletemulbutton' ));
           }
        } 
         //pagination bar, total 100 pages and each of have 10 records  
       echo $OUTPUT->paging_bar(100, $page, 10, "view.php?id={$cm->id}&page=$page");
       echo html_writer::end_tag('div');
          
      //button for new testimonial recording   
      if(!$isadmin){
          echo html_writer::tag('form',html_writer::empty_tag('input', 
               array('type' => 'submit','name'=>'back', 'value' => get_string('backbutton','testimonial'),'id'=>'backbutton')),
               array('method' => 'post', 'action' => "view.php?id={$cm->id}"));
      }
     //close form       
   echo html_writer::end_tag('form');  
        
   }
}
// Finish the page
echo $OUTPUT->footer();
