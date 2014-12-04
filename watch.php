<?php
/**
 * watch and play the audio video files.

 *
 * @package    mod
 * @subpackage feedcam
 * @copyright  2014 krishna
 * @license    http://www.vidyamantra.com
 */


require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');
require_once ($CFG->dirroot.'/course/moodleform_mod.php');
$PAGE->requires->css('/mod/feedcam/style.css');

//echo '<link href="style.css" type="text/css" rel="stylesheet"></link>';

global $DB,$USER,$PAGE;
//require_once(dirname(__FILE__).'/lib.php');

$cmid = optional_param('cmid', 0, PARAM_INT);
//$id= $_GET['cmid'];
if ($cmid) {
    $cm         = get_coursemodule_from_id('feedcam', $cmid, 0, false, MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $feedcam  = $DB->get_record('feedcam', array('id' => $cm->instance), '*', MUST_EXIST);
} 


$context = context_module::instance($cm->id);




           $completion = new completion_info($course);
            if($completion->is_enabled($cm) && $feedcam->completionwatch) {
                 $completion->update_state($cm,COMPLETION_COMPLETE);
             }
//echo '<script src="http://localhost/moodle27d/mod/feedcam/js/need.js"></script>';


//echo '<fieldset><legend><font color="black"  size="4"><b style="font-family:  "Hoefler Text", Georgia, "Times New Roman", serif;">RECORDINGS </b></font> </legend>';

$id2='';$url='';$url2='';$feedcamid='';

 $avid = optional_param('id', 0, PARAM_INT);
    
    if(isset($avid)){
           
           $feedcamid=$feedcam->id;
           
           //$id=$avid;
           
           echo $avid.'avid';
           
           
           //fetching video from database
           $query = $DB->get_records_sql('SELECT * FROM {feedcam_videos} WHERE id = ?', array($avid));
               
           foreach ($query as $value) { 
                    $url=  $value->url;
                    $name=$value->name;
                    $title=$value->videotitle;
                    
                  if(!($DB->record_exists('feedcam_watching', array('user_id' =>$USER->id, 'feedcam_id'=>$feedcamid, 'video_id'=>$avid)))){  
                     $record1 = new stdClass();
                     $record1->user_id = $USER->id;
                     $record1->feedcam_id = $feedcamid;
                     $record1->video_id=$avid;
                     $lastinsertid1 = $DB->insert_record('feedcam_watching', $record1, false);
                  }
               }
               
               $revitem= strrev($name);
               $str = $revitem;
               // $strlen = strlen( $str );
              //  $id = "";
               // for( $i=0; $i<=$strlen; $i++ ) {
                 $char=substr( $str, 0, 1 );
                    if($char=='m') {
                       $cropeditem= substr($revitem,4);
                       $audioname=  trim(strrev($cropeditem).'wav');
                       
                     }
                   else{
                       $cropeditem= substr($revitem,3);
                       $audioname=  trim(strrev($cropeditem).'webm');
                       $x=$url;
                   }  
                  //  $id .= $char;
              //  }

                   
                   echo $audioname.'name';
                   
               
               
         //  while($row=mysqli_fetch_assoc($query)){
            
         //      $url=$row['url'];
         //      $name=$row['name'];
         //  }
                   
                 //   $revitem= strrev($name);
            //   $str = $revitem;
           //  $char=substr( $str, 0, 1 );
            // fetching audio from database
            $query2 = $DB->get_records_sql('SELECT * FROM {feedcam_videos} WHERE name = ?', array($audioname));
         //  $query= mysqli_query($conn,"SELECT * FROM feedcam_videos WHERE id='$id' ");
            
          
            
            foreach ($query2 as $value2) { 
            
            $id2=$value2->id;
            
              if($char=='m') {
                $url2=$value2->url;
              }
              else{
                  $url2=$value2->url;
                  $url=$url2;
                  $url2=$x;
              }
              
              
              
               if(!($DB->record_exists('feedcam_watching', array('user_id' =>$USER->id, 'feedcam_id'=>$feedcamid, 'video_id'=>$id2)))){
                     $record2 = new stdClass();
                     $record2->user_id = $USER->id;
                     $record2->feedcam_id = $feedcamid;
                     $record2->video_id=$id2;
                     $lastinsertid2 = $DB->insert_record('feedcam_watching', $record2, false);
               }
            }
           
            
           // echo $url;
         //   echo ' , aud '.$url2;
            
            echo '<script src="js/need.js"> </script>';
            echo '<link href="style.css" type="text/css" rel="stylesheet"></link>';
            
            echo html_writer::start_tag('div', array('class'=>'youwatching','align'=>'center'));
            
            
            
            $table = new html_table();
            
                 $table->align=array();
                 $table->rowclasses = array();
                 $table->size=array();
                 //$table->data = array();

                          $table->size[] = '100px';
                          $table->align[] = 'left';

                          $table->size[] = '680px';
                          $table->align[] = 'left';
                          
                          $table->size[] = '150px';
                          $table->align[] = 'left';


                   //    $table->data[] =$dataarr; 
                     $watchtable=array();
            
                      $watchtable[]='';
                      $watchtable[]=get_string('youwatching','feedcam').$title;
                      $watchtable[]='';
                   
                           
                     $table->data[]=$watchtable;
                     $watchtable=array();
                     
                     
                     //echo html_writer::start_tag('div', array('id' => 'buttonbar'));
                       
                       // echo "<A HREF='javascript:history.go(0)'>";
                       //   echo html_writer::empty_tag('input', array('type' => 'button','name'=>'home', 'value' => get_string('replaywatch','feedcam'),'id'=>'replaywatch', 'class'=>'watch'));
                      //  echo "</A>";
                        
                        
                       // $url = new moodle_url('javascript:history.go(0)');
                     
                   echo $url.'video';
                   echo $url2.'audio';
                     
                     // echo html_writer::start_tag('div', array('id' => 'video-container'));
                    $startdiv=html_writer::start_tag('div', array('id' => 'video-container'));
                      $audiobuff=html_writer::start_tag('audio', array('src'=> $url2 , 'id' => 'audio','class'=>'audiowatch','autoplay'=>'autoplay'));echo html_writer::end_tag('audio');
                      $videobuff=html_writer::start_tag('video', array('src'=> $url, 'id' => 'video','class'=>'videowatch','autoplay'=>'autoplay')).html_writer::end_tag('video');
                     $enddiv= html_writer::end_tag('div');
                     
                    
                     $watchtable[]=$audiobuff; 
                     $watchtable[]=$startdiv.$videobuff.$enddiv;
                     $watchtable[]='';
                         
                     $table->data[]=$watchtable;
                     $watchtable=array();
                     
                     
                     echo html_writer::start_tag('div', array('id' => 'video-controls'));
                        // echo '<div id="video-controls">';
                      //$delurl = new moodle_url("");
                             
                           // echo html_writer::tag('form',html_writer::empty_tag('input', array('type' => 'submit','name'=>'database', 'value' => get_string('store','feedcam'),'id'=>'store', 'class'=>'databasesbutton')), array('method' => 'post', 'action' => ''));
                           // echo "<td>";
                           //  $playbutt=  html_writer::link('',
                           //          html_writer::empty_tag('img', array('src'=>'pix/play.svg','class'=>'iconsmall'),
                           //          array('class'=>'watch2','id' => 'play-pause','value'=>'Pause', 'onclick'=>'getvideoid(this.id)')));
                     
                          $playbutt= html_writer::empty_tag('input', array('type' => 'button','value' => 'Pause','id'=>'play-pause', 'class'=>'watch2'));
                          $playrange= html_writer::empty_tag('input', array('type' => 'range', 'id'=>'seek-bar', 'class'=>'watchbar'));
                          $mutebutt= html_writer::empty_tag('input', array('type' => 'button', 'value' => get_string('mutewatch','feedcam'),'id'=>'mute', 'class'=>'watch2'));
                          $muterange= html_writer::empty_tag('input', array('type' => 'range', 'id'=>'volume-bar', 'class'=>'watchbarmute', 'min'=>'0', 'max'=>'1','step'=>'0.1', 'value'=>'1'));
                          
                     echo html_writer::end_tag('div');
                     
                     
                     
                    
                     
                     $watchtable[]=''; 
                     $watchtable[]=$playbutt.' '.$playrange.' '.$mutebutt.' '.$muterange;
                     $watchtable[]='';
                     
                     
                      $table->data[]=$watchtable;
                      
                     $watchtable=array();
                     
                      $startdivbutt=html_writer::start_tag('div', array('id' => 'controlbutton'));
                       
                        $replay = html_writer::link("javascript:history.go(0)",
                                     html_writer::empty_tag('img', array('src'=>'pix/replay.svg','class'=>'icon'),
                                     array('class'=>'watch','id' => 'replaywatch')));
                        
                       // echo "<button type='button' id='full-screen' style='height: 30px; width: 150px;'>Full-Screen</button>";
                       // $fullscreen= html_writer::empty_tag('input', array('type' => 'button','value' => get_string('fullscreenwatch','feedcam'),'id'=>'full-screen', 'class'=>'watch'));
                     
                       
                       // $urlclose = new moodle_url("javascript:window.close()");
                        $closewindow = html_writer::link("javascript:window.close()",
                                     html_writer::empty_tag('img', array('src'=>'pix/close.svg','class'=>'icon'),
                                     array('class'=>'watch','id' => 'close')));
                        
                     $enddivbutt= html_writer::end_tag('div');
                     
                     
                     
                      $watchtable[]='';
                      $watchtable[]=$startdivbutt.' replay '.$replay.'&nbsp&nbsp  exit'.$closewindow.' '.$enddivbutt;
                      $watchtable[]='';
                           
                     $table->data[]=$watchtable;
                     
                     echo html_writer::table($table); 
                     
                   echo html_writer::end_tag('div');
           //  echo html_writer::end_tag('div');
             //  echo "<font color='green'><b><div align='center'>you are watching : ".$title." </b><br/><br/></font>";
             //  echo "<video src='$url' id='player' style='border: 1px solid rgb(15, 158, 238); height: 500px; width: 700px;' autoplay></video></div><br/><br/>";
             //  echo "<audio autoplay src='$url2'></audio>"; 
               
               
            //  echo "<div align=center><A HREF='javascript:history.go(0)'><input type=button value='REPLAY' name='home' style='height: 30px; width: 220px;' /></A><a href='javascript:window.close()'><button id='edit' name='database' style='height: 30px; width: 200px;'>Close this Window</button></a></a></div>";
             
         
             

                  //  function goFullscreen(id) {
                      // Get the element that we want to take into fullscreen mode
                 //     var element = document.getElementById(id);


                  //    if (element.mozRequestFullScreen) {
                  //      element.mozRequestFullScreen();
                  //    }
                 //     else if (element.webkitRequestFullScreen) {
                 //       element.webkitRequestFullScreen();
                 //    }
                 //   }
             

             //     echo '<img class="video_player" src="image.jpg" id="player"></img>';
               //  echo '<div align="center"> <button onclick="goFullscreen('player'); return false" style="height: 30px; width: 200px;" >Click for Full Screen</button></div>';
                 
              // echo "<video src='$url' id='video' style='border: 1px solid rgb(15, 158, 238); height: 500px; width: 700px;' autoplay></video></div><br/><br/>";
              
            //  echo '<script type="text/javascript" charset="utf-8" src="/mod/feedcam/js/need.js"></script>';
           // $PAGE->requires->js('/mod/feedcam/js/need.js'); 
             
             
                         
                     //  echo '</video>';
                         echo '<!-- Video Controls -->';
                         
                     
                     $eventdata = array();
            $eventdata['context'] = $context;
            $eventdata['objectid'] = $avid;
            $eventdata['userid'] = $USER->id;
            $eventdata['courseid'] = $course->id;

            $event = \mod_feedcam\event\video_revealed::create($eventdata);
            $event->add_record_snapshot('course', $course);
            $event->add_record_snapshot('course_modules', $cm);
            $event->trigger();  
 
       }
       
      else{
          echo 'error';     
      }
     
 // echo '</fieldset>';

   