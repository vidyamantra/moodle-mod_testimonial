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
 * Prints a particular instance of feedcam
 *
 * You can have a rather longer description of the file as well,
 * if you like, and it can span multiple lines.
 *
 * @package    mod
 * @subpackage feedcam
 * @copyright  2011 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/// (Replace feedcam with the name of your module and remove this line)

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');
require_once(dirname(__FILE__).'/locallib.php');
require_once ($CFG->dirroot.'/course/moodleform_mod.php');
//require_once ($CFG->dirroot.'/mod/feedcam/style.css');
$PAGE->requires->css('/mod/feedcam/style.css');

$id = optional_param('id', 0, PARAM_INT); // course_module ID, or
$n  = optional_param('n', 0, PARAM_INT);  // feedcam instance ID - it should be named as the first character of the module


if ($id) {
    $cm         = get_coursemodule_from_id('feedcam', $id, 0, false, MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $feedcam  = $DB->get_record('feedcam', array('id' => $cm->instance), '*', MUST_EXIST);
} elseif ($n) {
    $feedcam  = $DB->get_record('feedcam', array('id' => $n), '*', MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $feedcam->course), '*', MUST_EXIST);
    $cm         = get_coursemodule_from_instance('feedcam', $feedcam->id, $course->id, false, MUST_EXIST);
} else {
    error('You must specify a course_module ID or an instance ID');
}

require_login($course, true, $cm);
$context = context_module::instance($cm->id);
//$context = get_context_instance(CONTEXT_MODULE, $cm->id);

//add_to_log($course->id, 'feedcam', 'view', "view.php?id={$cm->id}", $feedcam->name, $cm->id);

 $eventdata = array();
    $eventdata['objectid'] = $feedcam->id;
    $eventdata['context'] = $context;

    $event = \mod_feedcam\event\course_module_viewed::create($eventdata);
    $event->add_record_snapshot('course_modules', $cm);
    $event->add_record_snapshot('course', $course);
    $event->trigger();

    
 
/// Print the page header

$PAGE->set_url('/mod/feedcam/view.php', array('id' => $cm->id));
$PAGE->set_title(format_string($feedcam->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);

// other things you may want to set - remove if not needed
//$PAGE->set_cacheable(false);
//$PAGE->set_focuscontrol('some-html-id');
//$PAGE->add_body_class('feedcam-'.$somevar);

$completion = new completion_info($course);
$completion->set_module_viewed($cm);

// Output starts here                         
echo $OUTPUT->header();



$heading = $OUTPUT->heading(format_string($feedcam->name), 3, null);
echo $heading;
//echo html_writer::empty_tag('hr');

if ($feedcam->intro) { // Conditions to show the intro can change to look for own settings or whatever
    $question= $OUTPUT->box(format_module_intro('feedcam', $feedcam, $cm->id), 'generalbox mod_introbox', 'feedcamintro'); 
}
 else{
    $question = " Sorry no question/dscription "; 
 }
echo $question;





//print_r($feedcam);


$studenttime = $feedcam->studenttime;
$teacherdelete = $feedcam->teacherdelete;

//echo $question;
//echo $studenttime;
//echo $teacherdelete;
// Replace the following lines with you own code

$PAGE->requires->js('/mod/feedcam/js/record.js');
//require_capability($capability, $context);
//$_SESSION['id']=0;


        //$mform    =& $this->_form;
        
//$mform->addElement('filemanager', 'my_filemanager', 'Upload a file', null, array('maxbytes' => $CFG->maxbytes, 'maxfiles' => 1, 'accepted_types' => array('*.zip')));

//if ($draftitemid = file_get_submitted_draft_itemid('my_filemanager')) {
 //   file_save_draft_area_files($draftitemid, $context->id, 'mod_assignment', 'my_filemanager', 0, array('subdirs' => false, 'maxfiles' => 1));
//}

//file_encode_url($CFG->wwwroot . '/pluginfile.php', '/' . $this->context->id . '/mod_assignment/my_filemanager');

$_SESSION['flip']=0;

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


/*
if(((!isset($_POST['database'])) && (!isset($_POST['delete'])) && !isset($_POST['submit']) && !isset($_POST['back'])) && $_SESSION['flip']==0){
    
    
         //     $conn= mysqli_connect('localhost','root',"mummy","quizmantra");


               echo '<div class="page">';
                echo  "<fieldset><legend><font color='black'  size='4'><b>FEED CAM </b> </legend>";
                
                    
                            //   $DB->get_records_sql('SELECT subname,subid FROM {videos}');
                             //    $quer2=mysqli_query($conn,"SELECT subname,subid FROM 'subject' ");
                     
                                      echo "<form method=post name='home' action=''>";
                                            
                                               echo '<div style="border-bottom:1px solid #36AE79;height:170px;width:800px;"><br><font ><p style="text-align:justify">Lectures Tube is newly generated plugin for moodle. This provides the real user interface for recording and uploading audio-video lectures'
                                             . '. Lectures Tube is based on webRTC so, there is no need of external plugins and it gives very less load to server. Here we provide multiple options and privileges for different type of users. You can delete your recently '
                                                       . 'recorded lectures as well as multiple deletion feature for previously recorded lectures. This is one of the poweful plugin that can automatically upload the lectures on server side therefore local space does not required much.   </p></div>';
                                               
                                    
                                      
                                     echo "<br><input type=submit value=START name='submit' style='color: #36AE79;height:40px;width:100px;'> | <a href=testcategory.php>Reset</a>";

                                   echo "</form>";
                                           
            echo '</fieldset></div>';
  }

*/

$admins = get_admins();
      $isadmin = false;
      foreach($admins as $admin) {
       if ($USER->id == $admin->id) {
           $isadmin = true;
            break;
          }
     }



if(((!isset($postdatabse)) && (!isset($postdelete)) && ((isset($_POST['back'])) || isset($id))) && $_SESSION['flip']==1){
      
   
     //  echo '<section class="experiment">'  ;
             
           //html_writer::start_tag('fieldset',  array('class' => 'field'));
           //  html_writer::tag('legend', get_string('feedcamlegend', 'feedcam'), array('id' => 'feedcamlegend','class' => 'field'));
                     //  echo "<fieldset><legend><font size='4'><b>".get_string('heading', 'feedcam')."</b></font></legend>";
                        //   echo get_string('heading', 'feedcam');
                            //  echo '<div class="page">';
                                     
                            // echo '</div></br>';
                        //   $PAGE->requires->js('/mod/feedcam/js/need.js');
                        //   echo '<br/>';
                                   
                           
                        //   echo "<form method=post name='home' action=''>";
                            
                          //  echo '<script src="http://localhost/moodle27d/mod/feedcam/js/need.js"> </script>';
                     //  $PAGE->requires->js('/mod/feedcam/js/need.js');
    
            $table = new html_table();
            
                 $table->align=array();
                 $table->rowclasses = array();
                 $table->size=array();
                 //$table->data = array();

                          $table->size[] = '150px';
                          $table->align[] = 'left';

                          $table->size[] = '300px';
                          $table->align[] = 'left';
                          
                          $table->size[] = '100px';
                          $table->align[] = 'left';


                   //    $table->data[] =$dataarr; 
                   $OUTPUT->heading(get_string('recheading', 'feedcam'), 4, null);
                     $recpaneltable=array();
                //  echo html_writer::start_tag('div', array('id'=>'firstdiv','class' => 'page')); echo html_writer::end_tag('div').'<br/>';
                       
                      $recpaneltable[]= get_string('videotitle','feedcam');
                         //echo html_writer::empty_tag('input', array('type' => 'text','name'=>'videotitle','id'=>'videotitle', 'class'=>'titlebutton', 'onchange'=>'saveVideoTitle(this.value)'));
                       $recpaneltable[]= html_writer::empty_tag('input', array('type' => 'text','name'=>'videotitle','id'=>'videotitle', 'class'=>'titlebutton', 'onchange'=>'saveVideoTitle(this.value)'));        
                       $recpaneltable[]='';
                       
                      $table->data[]=$recpaneltable;
                      
                      
                      $recpaneltable=array();
                      $recpaneltable[]=get_string('testirecording','feedcam');
                      
                      if (has_capability('mod/feedcam:record', $context)) {
                       $recordbutt= html_writer::empty_tag('input', array('type' => 'submit','name'=>'record', 'value' => get_string('record','feedcam'),'id'=>'record', 'class'=>'recordbutton'));
                        }
                       $stopbutt= html_writer::empty_tag('input', array('type' => 'button','name'=>'stop', 'value' => get_string('stop','feedcam'),'id'=>'stop', 'class'=>'stopbutton','disabled'=>'disabled' ));
                      
                       if (has_capability('mod/feedcam:deleterecent', $context)) {
                             $deleterecent= html_writer::empty_tag('input', array('type' => 'button','name'=>'delete', 'value' => get_string('deletefiles','feedcam'),'id'=>'delete', 'class'=>'deletefilesbutton','disabled'=>'disabled' ));
                       }
                       
                      $recpaneltable[]= $recordbutt.$stopbutt.$deleterecent;
                      $recpaneltable[]='';
                              
                       $table->data[]=$recpaneltable;
                       
                       
                       $recpaneltable=array();
                       
                      // echo html_writer::start_tag('div', array('id' => 'video-container'));
                     //  $control='';
                      // echo "<script>document.getElementById('record').onclick = function() { $control='controls';}</script>";
                       // echo '';
                       
                         $recbox= html_writer::start_tag('video', array('id' => 'preview','class'=>'videopreview','controls'=>'controls'));echo html_writer::end_tag('video');
                      // echo html_writer::end_tag('div'); 
                        $recpaneltable[]='';
                        $recpaneltable[]=$recbox;
                        $recpaneltable[]='';
                        
                       $table->data[]=$recpaneltable;
                         
                       $recpaneltable=array();
                       
                       
                       $recpaneltable[]=get_string('uploading','feedcam');
                       
                        $recpaneltable[]= html_writer::start_tag('div', array('id' => 'container','class'=>'uploadingbar')).html_writer::end_tag('div').get_string('refreshhint','feedcam');;
                                        
                       //$recpaneltable[]=$deleterecent;
                      $recpaneltable[]='';
                      
                        
                        $table->data[]=$recpaneltable;
                        
                        $recpaneltable=array();
                        
                         $recpaneltable[]='';
                         if (has_capability('mod/feedcam:godatabase', $context)) { //   echo  '<form method=post action="" ><input type="submit" value="Feedcam'."'s".' Store" name="database" style="height: 35px; width: 180px; font-size:13px;color:#00BFFF;" /><img src="http://www.essentialsql.com/wp-content/uploads/2014/05/database-parts.jpg" height="42" width="60"></img></form>';
                                $url = new moodle_url('');
                              $backtostore= html_writer::tag('form',html_writer::empty_tag('input', array('type' => 'submit','name'=>'database', 'value' => get_string('store','feedcam'),'id'=>'store', 'class'=>'databasesbutton')), array('method' => 'post', 'action' => ''));
                             
                           }
                        $recpaneltable[]= $backtostore;
                        $recpaneltable[]='';
                      
                        
                        $table->data[]=$recpaneltable;
                         
                       echo html_writer::table($table); 
                            
                           
                              
                        
                            
                          
                          //  echo '<button id="stop" style="height: 35px; width: 150px; color:red;" disabled>Stop &FilledSmallSquare;</button> | ';

                    //    echo html_writer::start_tag('div', array('id' => 'buttons'));
                        // echo '<table align=center><tr><td>';
                        
                           //  echo get_string('clickon', 'feedcam');
                          
                            
                           
                        // echo '</td><tr></table>' ;
                      // echo html_writer::end_tag('div');
                       
                     echo html_writer::end_tag('form');
                    
                  //   echo html_writer::start_tag('div', array('id' => 'container','class'=>'uploadingbar'));echo html_writer::end_tag('div');
                        
                   // echo '<div align="center" id="container" style="padding:1em 1em;margin-top:80px; width: 600px; height: 200px;""></div>';
                   //  echo '</td></tr>';
                    // echo html_writer::end_tag('div');
               
                   //   echo '</form>';
                 //     echo '</table>';
                    //       echo html_writer::end_tag('fieldset');
           // echo '</section>';
            
             //$id = optional_param('id', 0, PARAM_INT); // course_module ID
             
            // echo $id;
           //  exit();
       //  echo '<script>window.uniqueId ='.$id.' </script>';    
         echo '<script>window.uniqueId ='.$id.'; </script>';
	$PAGE->requires->js('/mod/feedcam/js/record2.js');		
           
          
        
   
    }


     

if(((isset($postdatabse)) || (isset($postdelete))  || (isset($getvidname))  || !isset($postback)) && ($_SESSION['flip']==0)){
 
    
    //  echo '<fieldset><legend><font color="black"  size="4"><b style="font-family:  "Hoefler Text", Georgia, "Times New Roman", serif;">RECORDINGS </b></font> </legend>';
   
       
     echo $OUTPUT->heading(get_string('subheading', 'feedcam'), 4, null);
     
        $page = optional_param('page', 0, PARAM_INT); 
     // echo html_writer::empty_tag('hr');
      
       //  echo html_writer::start_tag('div', array('class' => 'page'));
        //               echo html_writer::start_tag('div', array('id'=>'firstdiv','class' => 'page')).'<br/>';
                     //  echo html_writer::tag('p', get_string('firstpara', 'feedcam'), array('id'=>'firstpara','class' => 'page'));
       //                echo html_writer::end_tag('div');
       //  echo html_writer::end_tag('div').'<br>';           
    
  

       
         //    global $DB;
       //  echo $getvidname;
         

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
                               
                               $revitem= strrev($itemname);
                               $cropeditem= substr($revitem,4);
                               
                               $aitemname=  trim(strrev($cropeditem).'wav');
                             
                  
                                $sql='SELECT videotitle FROM {videos} WHERE id = ?';    
                                $vtitle = $DB->get_field_sql($sql, array((int)$itemid));
                               
                             //  if(isset($postdelete)){
                                    echo html_writer::start_tag('div', array('class'=>'itemidprint'));
                                        echo $vtitle.' |';
                                    echo html_writer::end_tag('div');  
                            //   }
                        // echo "<div  style='float:right;'><font color='#A80707'><b>".$itemid." |  </font></b></div>";
                               
                          /*        
                                    $session['lastid']=$aitemid;
                                            echo $session['lastid'];

                                      $sql='SELECT rowscount FROM {videos} WHERE id = ?';    
                                      $lastrowcount = $DB->get_records_sql($sql, array($session['lastid']));

                                     //   print_r($$lastrowcount);
                                        foreach($lastrowcount as $value){
                                            $lastrownum=$value->rowscount; 
                                             echo $value->rowscount;  
                                        }
                                       //  $lastrowcount->rowscount;  
                                    
                               */     
                                    
                               
                            if(!($DB->record_exists('files', array('contextid' =>$context->id, 'itemid'=>$itemid)))){  

                                   $DB->delete_records('videos', array ('id'=> $itemid));
                                   $DB->delete_records('videos', array ('id'=> $aitemid));
                                 //  $DB->delete_records('videos', array ('id'=> $itemid+1));
                                 
                                   echo html_writer::start_tag('div', array('class'=>'curruptprint'));
                                   echo get_string('curruptprint', 'feedcam');
                                    echo html_writer::end_tag('div');  
                                  
                              //   echo "<div><font color='#A80707'>Sorry, Currupted media and did not store on server<font></div>";
                                 // mysqli_query($conn,"DELETE FROM mdl_videos WHERE name='$withvideoext' OR name='$withaudioext' ");

                                 //  $DB->delete_records("videos", array("name"=>$value));
                            }

                             else{
                                     fileDeletion($itemid,$itemname,$context->id);
                                     fileDeletion($itemid,".",$context->id);
                                     fileDeletion($aitemid,$aitemname,$context->id);
                                     fileDeletion($aitemid,".",$context->id);

                                        $vid=$DB->delete_records('videos', array ('id'=> $itemid));
                                        $aid=$DB->delete_records('videos', array ('id'=> $aitemid));
                                      //  $DB->delete_records('videos', array ('id'=> $itemid+1));
                              }
                         //  if(!file_exists('uploads/'.$value)){
                        //        echo "Sorry Video had been currupted and did not stored on server<br /><br/>";
                        //         mysqli_query($conn,"DELETE FROM mdl_videos WHERE name='$value' ");   //db
                                 
                             //  $DB->delete_records("videos", array(sql_compare_text("name")=>$value));
                       //    }
                           
                       //     else{
                       //          unlink('uploads/'.$value);
                                 
                       //          mysqli_query($conn,"DELETE FROM mdl_videos WHERE name='$value' ");  //db
                           //   $DB->delete_records("videos", array(sql_compare_text("name")=>$value));
                        //     }
                                 
                       }
                       
                   
                    // echo $lastreplycount->replycount; 
                       
               }
               
              // if(isset($postdelete)){
                    echo html_writer::start_tag('div', array('class'=>'curruptprint'));
                       echo get_string('deleteprint', 'feedcam');
                   echo html_writer::end_tag('div');
             //  }
                  // if(isset($getvidname)){
                       
                     echo "<meta http-equiv='refresh' content='3; url=view.php?id={$cm->id}&page=$page'>";
                 //  }
              }
              
               else{
                   echo get_string('selectfile', 'feedcam');
               }
           }
 
           
           
     
       
      //if ($isadmin) {
    //      echo "you are an admin".$isadmin;    
    //   } 
       
    // else { 
     //   echo "you are not an admin".$isadmin;
   //   }  
        $queryall= $DB->get_records_sql("SELECT * FROM {videos}");
        if(!$isadmin){
            $queryall= $DB->get_records_sql("SELECT * FROM {videos} WHERE user_id=$USER->id ");
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
             $lastupdate=$DB->update_record('videos', $update);
          }
            
           
       
       $pagestart= ($page*10)+1;
       $endpage= $pagestart+10-1;

      // $DB->get_record_sql('SELECT * FROM {videos} WHERE firstname = ? AND lastname = ?', array('Martin', 'Dougiamas'));
   if($isadmin){
       
      // $remainingsno=10-$_SESSION['sno'];
     //  echo $remainingsno;
      
     //  $initial=10;
       
      // $pagestart= ($page*10)+1;
     //  $endpage= $pagestart+10-1;
       
     //  echo "start".$pagestart;
     //  echo "end".$endpage;
       
     //  $countrows=$DB->count_records('videos', array('feedcam_id'=>$feedcam->id));
       
       
       if(isset($page) && $page>0){
         //  echo "SELECT * FROM {videos} WHERE COUNT(feedcam_id) >$pagestart AND  COUNT(feedcam_id)< $endpage";
           $query= $DB->get_records_sql("SELECT * FROM {videos} WHERE rowscount>=$pagestart AND rowscount<=$endpage");
        //   $queryall= $DB->get_records_sql("SELECT * FROM {videos}");
          // $page++;
         //  echo "<meta http-equiv='refresh' content='5; url=view.php?id={$cm->id}'>";
         }
       else{
           
           $query= $DB->get_records_sql("SELECT * FROM {videos}  WHERE rowscount<=$endpage");
         //  $queryall= $DB->get_records_sql("SELECT * FROM {videos}");
       }
   }
   else{
        $query= $DB->get_records_sql("SELECT * FROM {videos} WHERE user_id=$USER->id AND rowscount>=$pagestart AND rowscount<=$endpage");
       // $queryall=$query;
   }
     
      //  $query= mysqli_query($conn,"SELECT * FROM videos "); // db 
        
    //     if (mysqli_num_rows($query) == 0){  
    //db
        if(!$query || !$queryall){
            
            
            if(!$isadmin){
              echo html_writer::tag('form',html_writer::empty_tag('input', array('type' => 'submit','name'=>'back', 'value' => get_string('backbutton','feedcam'),'id'=>'backbutton')), array('method' => 'post', 'action' => "view.php?id={$cm->id}"));
            }
               // echo "<form method='post' action='view.php?id={$cm->id}'><input type=submit name='back' value='Back to Video Capture' name='home' /></form>";
                echo html_writer::start_tag('div', array('class'=>'itemidprint'));
                         echo get_string('existprint', 'feedcam');
                  echo html_writer::end_tag('div');
                  
                // if($isadmin){
                //   echo $OUTPUT->paging_bar(100, $page, 10, "view.php?id={$cm->id}&page=$page");
               //    }
          //  echo "<div style='float:right;'><font color='#A80707'><b>No Video File Exist</font></b></div>";
          }
            
        else { 
            
             $table = new html_table();
         //   if($admin){ 
          //     $table->attributes['class'] = 'datatable';
          //  }
            
            // $table->head = array ();
             $table->align=array();
             $table->rowclasses = array();
             $table->size=array();
             $table->data = array();
            
             
                      $table->size[] = '180px';
                      $table->align[] = 'left';
                    // }
                     
                     $table->size[] = '800px';
                     $table->align[] = 'left';
               
                   
               //    $table->data[] =$dataarr; 
           
              $stattable=array();
              $row='';
                   
           //  echo get_string('totaltestimonials', 'feedcam');
             $stattable[]=get_string('totaltestimonials', 'feedcam');
              
             $totaltesti=  floor(sizeof($queryall)/2);
                  $stattable[]= $totaltesti;
            // echo $OUTPUT->heading($totaltesti, 6, null);
          //   echo html_writer::empty_tag('hr');
            // echo html_writer::start_tag('div', array('class'=>'itemidprint','value'=>"$totaltesti")); echo html_writer::end_tag('div');
           
            // echo get_string('totalstudents', 'feedcam');
          if($isadmin){
                 
                  
              $table->data[] =$stattable;
              $stattable=array();
                 
              $stattable[]=get_string('totalstudents', 'feedcam');
                  
               
            //$stattable=array();
            // $result=$DB->count_records('videos', array('feedcam_id'=>$feedcam->id, 'user_id' =>$USER->id));
            //    $replycount=(int)floor($result/2); 
           // $totalstudent= $DB->get_records_sql("SELECT user_id FROM {videos}");  
             $sql='SELECT DISTINCT user_id FROM {videos} WHERE feedcam_id = ?';    
             $totalstudent = $DB->get_records_sql($sql, array($feedcam->id));
             
             
             foreach($totalstudent as $value){
                 $sql2='SELECT * FROM {videos} WHERE user_id = ?';
                 $sql3='SELECT username FROM {user} WHERE id = ?';
                
                 
                 $totalreplyperstu = $DB->get_records_sql($sql2, array($value->user_id));
                 $usernameper = $DB->get_records_sql($sql3, array($value->user_id));
                 
                 
               //  print_r( $totalreplyperstu);
                 
                 
                 foreach ($usernameper as $value) {
                    // echo $value->username;
                     $lastcol1=$value->username;
                 }
                 
                // $loopcount='';
                 foreach ($totalreplyperstu as $value) {
                      $lastcol2=$value->rowscount;
                 }
                 
             //echo "loop".$loopcount;
                // echo $totalreplyperstu->replycount+1;
                 // $lastcol2=$loopcount;
                  
                  $percentperstu= round(($lastcol2*100)/$totaltesti);
                  
                  
                  
                  
                  $row= $row.$lastcol1." | ".$lastcol2." | ".$percentperstu.'%'.' , ';
                  
                
                  
             }
             $stattable[]=$row;
             
         }
         
            
             $table->data[] =$stattable;
             
            if(!$isadmin){ 
              $stattable=array();
              $stattable[]=get_string('studentimetext', 'feedcam');
              $stattable[]=$studenttime." hours";
            
              $table->data[] =$stattable;        
           }
         
             $stattable=array();
             $stattable[]=get_string('testimonialstore', 'feedcam');
             
             if(!$isadmin){
                    $stattable[]= html_writer::tag('form',html_writer::empty_tag('input', 
                    array('type' => 'submit','name'=>'back', 'value' => get_string('backbutton2','feedcam'),'id'=>'backbutton2')),
                    array('method' => 'post', 'action' => "view.php?id={$cm->id}"));
             }
             else{
                 $stattable[]='';
             }
            $table->data[] =$stattable;
         
             
          echo html_writer::table($table);
          
         
        //  $pagingbar = paging_bar::make(120, 3, 20, 'http://domain.com/index.php');
// Optionally : $pagingbar->pagevar = 'mypage';
       //   echo $OUTPUT->paging_bar($pagingbar);
          //  echo "<hr>";
            // print_r($totalstudent);
            // echo html_writer::empty_tag('hr');
             
             
            
            
            
           // echo "<table style='margin-left:15%;'><tr>";
          //  echo "<div align=center><a href='view.php?id={$cm->id}'><input type=button value='Back to Video Capture' name='home' style='height: 40px; width: 180px;' /></a> | ";
          
            // echo '';
         echo html_writer::start_tag('form', array('method' => 'post', 'action' => '','id'=>'frm1'));
           
       
            
            echo html_writer::start_tag('div', array('id'=>'storetable'));
            
            
            $table = new html_table();
          if(!$admin){  
            $table->attributes['class'] = 'datatable';
          }
             $table->head = array ();
             $table->align=array();
             $table->rowclasses = array();
             $table->size=array();
             $table->data = array();
            
               //    echo '<table class="datatable">';
                //    echo '<tr>';
                    
               //  echo '<th width="50">S No.</th>';
                    $h1="S No.";
                   $table->head[] = $h1;
                   $table->size[] = '70px';
                   $table->align[] = 'center';
                       
                     if($isadmin){
                      // echo '<th width="180">Student Name</th>';
                         $table->head[] = 'Student Name';
                         $table->size[] = '180px';
                         $table->align[] = 'left';
                     }
                     
                  //  echo '<th width="250">Title of Testimonial</th>';
                     $table->head[] = 'Title of Testimonial';
                     $table->size[] = '300px';
                     $table->align[] = 'left';
                     
                  //  echo '<th width="180">Date/Time</th>';
                    $table->head[] = 'Date/Time';
                    $table->size[] = '200px';
                    $table->align[] = 'left';
                  //  if(!$isadmin){
                //      echo '<th width="180">Remaing Time</th>';
                 //   }
                   if($isadmin && $teacherdelete){ 
                    //  echo '<th>Select</th>';
                      $table->head[] = 'Select';
                      $table->align[] = 'center';
                   }
                   if(!$isadmin){ 
                      $table->head[] = 'Remove';
                      $table->align[] = 'center';
                   }
                  //  echo '</tr>';
                   
            
             
      /*       
        
        $table->attributes['class'] = 'admintable generaltable';
       // $table->colclasses[] = 'width="180"';
        
        foreach ($extracolumns as $field) {
            $table->head[] = ${$field};
            $table->colclasses[] = 'leftalign';
        }
        
        $table->head[] = $city;
        $table->colclasses[] = 'leftalign';
        $table->head[] = $country;
        $table->colclasses[] = 'leftalign';
        $table->head[] = $lastaccess;
        $table->colclasses[] = 'leftalign';
        $table->head[] = get_string('edit');
        $table->colclasses[] = 'centeralign';
        $table->head[] = "";
        $table->colclasses[] = 'centeralign';

        $table->id = "users";
            */
            

                //$table = new html_table();
               // $table->head = array('Lastname', 'Firstname', 'ID Number');
               // $table->data[] = array(... first row of data goes here ...);
               // $table->data[] = array( ... second row of data goes here ...);
               // echo html_writer::table($table);

           
            
           // echo "<table cellpadding=30 cellspacing=2 style='border-spacing:2em;'>";
           // echo "<tr><th>Id</th><th>Name</th><th>Delete</th></tr>";
            date_default_timezone_set("Asia/Calcutta");
           
       //   if(!$isadmin){
       //     $srno=1;
       //   }
            
           // while($row=$DB->get_records_list($query)){   //db
            
        // if(!isset($page) || $page==0){
          //if($page==0)  {
          //  $sno=0;
         //  }
       //      unset($_SESSION['sno']); 
            
      //      $_SESSION['sno']=$sno;
           //  $sno=$_SESSION['sno'];
       //      $_SESSION['page']=$page;
       //  }   
         
       //  else if($page>$_SESSION['page']) {
           //  echo  $_SESSION['page'].'prev';
          //  echo "crr".$page;
      //      $sno=$_SESSION['sno']+1;
             
      //       $_SESSION['page']=$page;
      //   }
      //   else if($page<$_SESSION['page']){
             
             
       //      $sno=$_SESSION['sno']-1;
             
        //     $_SESSION['page']=$page;
        // }
            // $_SESSION['page']=1;
            
            
        foreach ($query as $value) { 
            
          $dataarr=array();
           // echo $count;
            
                $vid = $value->id;
                $feedcamid = $value->feedcam_id;
                $userid = $value->user_id;
                $name = $value->name;
                $videotitle = $value->videotitle;
                $datetime = $value->datetime;
                $replycount = $value->replycount;
                $urll = $value->url;
                $rowscount = $value->rowscount;
                $videoids=$vid.'/'.$name;
               // $url=get_feedcam_doc_url($vid);
               //echo($url);
               
              //  echo($url);
              // exit();
                                       
                 //   $updateloginlink ="watch.php?id=$vid";
                                   
                                  
		   //  echo "<a href=\"javascript:create_window('$updateloginlink','500','800')\"><button id='edit' style='height: 40px; width: 200px;'>Update Login Data</button></a></td>";
                    
               // echo "<tr><td>$id</td><td><a href=\"javascript:create_window('watch.php?id=$id','500','800')\>$name</a><br /></td><td><input type=checkbox name=name[] value='$name' /></td></tr>";     
               // echo "<tr><td>$id</td><td><a href='watch.php?id=$id'>$name</a><br /></td><td><input type=checkbox name=name[] value='$name' /></td></tr>";
             //  if($vid%2!=0){
            //       $sno++;
            //   }
                
               
              // if($page==0){
             //    $update = new stdclass;
            //           $update->id = $vid;
            //           $update->rowscount = $sno;
            //     $lastupdate=$DB->update_record('videos', $update);
               
             // }
                
                if($vid%2!=0 ){
                   // echo "<tr onMouseover=$mouseover onMouseout=$mouseout>";
                    $mouseover="style.backgroundColor='#f5f5f5'";
                    $mouseout="style.backgroundColor='#FFFFFF'";
                
                   $table->rowclasses = "onMouseover=$mouseover onMouseout=$mouseout";
                   // echo "<td >$sno</td>";
                   
               //    $update = new stdclass;
                //       $update->id = $vid;
               //        $update->rowscount = $sno;
               //    $lastupdate=$DB->update_record('videos', $update);
                   
                   
                //    $_SESSION['sno']=$sno;
                  //  echo $_SESSION['sno'];
                 
                    //$dataarr[]=$sno;
               //    if($isadmin){
                     $dataarr[]=$rowscount;
              //     }
             //     if(!$isadmin){
              //         $dataarr[]=$srno;
              //         $srno++;
              //     }
                    //  $sno++;
                   
                }
                
              //  $link = new action_link();
              //      $link->url = new moodle_url("javascript:create_window('watch.php?id=$vid&cmid=$id')", array('id' => 2, 'action' => 'browse')); // required, but you can use a string instead
              //      $link->text = "$name"; // Required
              //      echo $OUTPUT->link($link);   
                  $sql='SELECT username FROM {user} WHERE id = ?';    
                 $username = $DB->get_field_sql($sql, array($userid));
                
                if($vid%2 !=0 ){
                    if($isadmin){   
                        $dataarr[]=$username;
                     }
                     
                    $dataarr[]="<a  href=\"javascript:create_window('watch.php?id=$vid&cmid=$id')\">$videotitle</a><br />";
                    $datetimeformat=date("Y-m-d H:i:s", $datetime);
                    $dataarr[]=$datetimeformat;
                }
               
                //  echo $datetime."datetime<br/>";
                
                     

                  
                    if($isadmin && $teacherdelete){
                        if($vid%2!=0){
                         // echo "<td><input type=checkbox name=videoarr[] value='$videoids' /></td></tr>"; 
                            $dataarr[]="<input type=checkbox name=videoarr[] value='$videoids' />";
                            
                            // $row[] = implode(' ', $dataarr);
                           // $rows[]=$dataarr;
                          //  $table->data[] = $dataarr; 
                        }
                     }
               
                   if(!$isadmin  && $vid%2!=0){ 
                         
                      $timelimit= $datetime+$studenttime*60*60;
                      $expire = date("Y-m-d H:i:s", $timelimit);
                      $current = date("Y-m-d H:i:s");
   
                     // echo $expire.'expp<br/>';
                     // echo $current.'cur';

                        if(($expire > $current)){  
                            
                            
                   //   $url = new moodle_url('');
                //   echo html_writer::tag('input',
                 //          html_writer::empty_tag('img', array('src'=>$OUTPUT->pix_url('t/delete'),'class'=>'iconsmall')),
                //           array('type' => 'submit','name'=>'database', 'value' => get_string('store','feedcam'),'id'=>'store', 'class'=>'databasesbutton')
               //           );
                               
                            
                       //     $link = new action_link();
                       //     $link->url = new moodle_url('http://domain.com/index.php', array('id' => 2, 'action' => 'browse')); // required, but you can use a string instead
                      //      $link->text = 'Browse page 2'; // Required
                       //     echo $OUTPUT->link($link);
                            
                    //     echo get_string('videotitle','feedcam');
                     //    echo html_writer::empty_tag('input', array('type' => 'button','name'=>'videotitle','id'=>'videotitle', 'class'=>'titlebutton', 'onchange'=>'saveVideoTitle(this.value)'));
                            
                            
                          //  echo $_SERVER['PHP_SELF']."?id={$cm->id}";
                            
                           // $url = new moodle_url('');
                            
                            
                        //    $icon = new moodle_action_icon();
                       //     $icon->image->src = $OUTPUT->pix_url('t/delete');
                        //    $icon->image->alt = 'What is moodle?';
                       //     $icon->link->url = new moodle_url("view.php?id={$cm->id}&vidname=$videoids");
                       //     $icon->add_confirm_action('Are you sure?'); // Optional. Equivalent to doing $icon->link->add_confirm_action('Are you sure?');
                        //    echo $OUTPUT->action_icon($icon);
                            
                            $delurl = new moodle_url("view.php?id={$cm->id}&vidname=$videoids");
                             
                           // echo html_writer::tag('form',html_writer::empty_tag('input', array('type' => 'submit','name'=>'database', 'value' => get_string('store','feedcam'),'id'=>'store', 'class'=>'databasesbutton')), array('method' => 'post', 'action' => ''));
                           // echo "<td>";
                            $lastcolumn =  html_writer::link($delurl,
                                     html_writer::empty_tag('img', array('src'=>$OUTPUT->pix_url('t/delete'),'class'=>'iconsmall'),
                                     array('class'=>'titlebutton','id' => $videoids, 'onclick'=>'getvideoid(this.id)', 'name'=> 'singledelurl')));
                           
                            $dataarr[] = $lastcolumn;
                            
                            // echo $itemid; 
                            
                            
                             
                            //$rows[]=$dataarr;
                            
                            
                         //    echo '<script>if(window.videotitle){'.$disablerec="enabled".'}else{'.$disablerec="disabled".'}</script>';
                               
                            
                            // echo link(new moodle_url($url, array('delete'=>$user->id, 'sesskey'=>sesskey())), html_writer::empty_tag('img', array('src'=>$OUTPUT->pix_url('t/delete'), 'alt'=>$strdelete, 'class'=>'iconsmall')), array('title'=>$strdelete));
                            
                            
                           //  echo "<td><input type=checkbox name=videoarr[] value='$videoids' /></td></tr>";
                            
                       }
                         

                          //  $metaexpire=$expire+60;
                         //   $page = $_SERVER['PHP_SELF'];
                         //   $sec = "3600";
                          //        echo "<meta http-equiv='refresh' content= '$sec' URL='$page'>";
                          //        echo "<meta http-equiv='expires' content='$expire' />";
                                  
                                  
                       //  echo "<td><input type=checkbox name=videoarr[] value='$videoids' /></td>";         
 
                 }   
                  
                   
                 if($vid%2!=0 ){
                     $table->data[] =$dataarr; 
                 }
                 
             }
             
             if($isadmin){


                $dataarr=array();
                $dataarr[]='';$dataarr[]='';$dataarr[]='';$dataarr[]=get_string('selectall', 'feedcam');;
                $dataarr[]= html_writer::empty_tag('input', array('type' => 'checkbox','name'=>'checkall','onclick'=>'checkedAll(frm1)','id'=>'checkall'));
                $table->data[] =$dataarr;
              }
              
              echo html_writer::table($table);
             //  echo "</table>";
              
            
            
           if (has_capability('mod/feedcam:deletemultiple', $context)) {
               if($isadmin && $teacherdelete){
                  echo html_writer::empty_tag('input', array('type' => 'submit','name'=>'delete', 'value' => get_string('deletemultiple','feedcam'),'id'=>'deletemul', 'class'=>'deletemulbutton' ));
               }
             
            } 
            
       //  if($isadmin){
            echo $OUTPUT->paging_bar(100, $page, 10, "view.php?id={$cm->id}&page=$page");
        //  }
         
          
         echo html_writer::end_tag('div');
          
         
         if(!$isadmin){
            echo html_writer::tag('form',html_writer::empty_tag('input', 
                    array('type' => 'submit','name'=>'back', 'value' => get_string('backbutton','feedcam'),'id'=>'backbutton')),
                    array('method' => 'post', 'action' => "view.php?id={$cm->id}"));
         }
            
          echo html_writer::end_tag('form');  
        
        }
   // echo '</fieldset>';
}


// Finish the page
echo $OUTPUT->footer();
