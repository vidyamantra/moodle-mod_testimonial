<?php

/**
 * save and upload the audio video files into directories and link of files in database

 *
 * @package    mod
 * @subpackage testimonial
 * @copyright  2014 krishna
 * @license    http://www.vidyamantra.com
 */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');
require_once ($CFG->dirroot.'/course/moodleform_mod.php');
global $DB,$USER;


//
//}
/*
$id = optional_param('id', 0, PARAM_INT); // course_module ID, or
$n  = optional_param('n', 0, PARAM_INT);  // testimonial instance ID - it should be named as the first character of the module


/*
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

//require_login($course, true, $cm);
$context = context_module::instance($cm->id);
   
echo $context;
echo $context->id;
exit();
*/

$id = optional_param('cmid', 0, PARAM_INT);

//$id= $_GET['cmid'];
if ($id) {
    $cm         = get_coursemodule_from_id('testimonial', $id, 0, false, MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $testimonial  = $DB->get_record('testimonial', array('id' => $cm->instance), '*', MUST_EXIST);
} 

$context = context_module::instance($cm->id);

if($testimonial->intro) { // Conditions to show the intro can change to look for own settings or whatever
    $question= $testimonial->intro;
 }
 else{
    $question = " Sorry no question/dscription "; 
 }

 $videotitle= optional_param('vtitle', null, PARAM_RAW);
 if(strcmp($videotitle, '[object HTMLInputElement]')==0){
    $videotitle="Untitled testimonial";
 }
   
 $result=$DB->count_records('testimonial_videos', array('testimonial_id'=>$testimonial->id, 'user_id' =>$USER->id));
  $replycount=(int)floor($result/2);     
 // echo $replycount;
  
  $rowscount=$DB->count_records('testimonial_videos', array('testimonial_id'=>$testimonial->id));
  
  $completion = new completion_info($course);
$completion->set_module_viewed($cm);
  //$replycount=(int)floor($result2/2);     
 // echo $rowscount;
   
foreach(array('video', 'audio') as $type) {
    if (isset($_FILES["${type}-blob"])) {
    
     
     // echo 'uploads/';
        
//	$fileName = $_POST["${type}-filename"];
//       $uploadDirectory = 'uploads/'.$fileName;
      
        
        
       $uploaded_file_path = $_FILES["${type}-blob"]["tmp_name"];  // temp path to the actual file
        $filename = $_POST["${type}-filename"];                // the original (human readable) filename
       
        
       // print_r($filename);
        
      //  $mediaext=substr($filename, -3);
        
       // echo $mediaext;
        
     //   if(strcmp($mediaext,"wav")==0){
      //    $mediaid=  substr($filename, 0,-3);
          //  $mediaid= $i;
           
     //   }
        
     //   else{
      //    $mediaid=  substr($filename, 0,-4);
        //   $mediaid= $j;
          
     //   }
      //  echo $testimonial->id;
      //  echo $USER->id;
     //   echo $question.'<br><br>';
      //  exit();

       // print_r($DB->count_records('testimonial_videos', array('testimonial_id'=>$testimonial->id, 'user_id' =>$USER->id))); 
       // exit;
        
       // $countsql='SELECT replycount FROM {testimonial_videos} WHERE testimonial_id AND user_id = ? AND  question = ? ';
        
       
         
         
       
   
       
       //  else{
       //      $countsql='SELECT replycount FROM {testimonial_videos} WHERE testimonial_id AND user_id = ? AND  question = ? ';    
       //      $replycount = (int) $DB->get_fieldset_sql($countsql, array($testimonial->id, $USER->id, $question));
       //      $replycount++;
       //   }

        
        
       date_default_timezone_set("Asia/Calcutta");
        
         $record = new stdClass();
               $record->testimonial_id=$testimonial->id;
               $record->user_id = $USER->id;
               $record->name = $filename;
               $record->videotitle = $videotitle;
               $record->question = $question;
               $record->datetime = time();
              // $record->url = '';
               $lastinsertid = $DB->insert_record('testimonial_videos', $record, false);
        
               
               
      //  $query = $DB->get_records_sql('SELECT * FROM {testimonial_videos} WHERE name = ?', array($filename));
       //  $query = $DB->get_records('testimonial_videos', array('name'=>$filename));
         
                $sql='SELECT id FROM {testimonial_videos} WHERE name = ?';    
             $mediaid = $DB->get_field_sql($sql, array($filename));
            
           // foreach ($query as $value) { 
          //      $mediaid= $value->id;  
          //   }      
               
           //print_r($mediaid);    
               
      //  $file_storage = get_file_storage();
      //  $context = context_module::instance($id);
        
            $fileinfo = array(
                'contextid' => $context->id,
                'component' => 'mod_testimonial',    // mod_[your-mod-name]
                'filearea' => 'testimonial_docs',   // arbitrary string
                'itemid' => $mediaid,           // use a unique id in the context of the filearea and you should be safe
                'filepath' => '/',              // virtual path
                'filename' => "$filename");       // virtual filename

            $file_storage = get_file_storage();
            
            if ($file_storage->file_exists($fileinfo['contextid'],
                                 $fileinfo['component'],
                                 $fileinfo['filearea'],
                                 $fileinfo['itemid'],
                                 $fileinfo['filepath'],
                                 $fileinfo['filename'])) return false; // (this code is actually in a function)

  
            $file = $file_storage->create_file_from_pathname($fileinfo, $uploaded_file_path);
        
        
     //upload the audio-video files
    //   if (!move_uploaded_file($_FILES["${type}-blob"]["tmp_name"], $uploadDirectory)) {
   //         echo(" problem moving uploaded file");
   //      }
           //     echo "<br>".$filename." has been uploaded";
	//	echo($filename);
                 
               //  $tmp=$_FILES["${type}-blob"]["tmp_name"];
                
                
             $midint= (int)$mediaid;
             $url=get_testimonial_doc_url($midint);
             $url1="$url";
             
           //  echo $url;
              
        /*    $record1 = new stdClass();
            
               $record1->id = $midint;
               $record1->url = $url;
            //   $lastinsertid = $DB->insert_record('testimonial_videos', $record, false);
             $lastupdateid = $DB->update_record('testimonial_videos', $record1);
             
             echo $lastupdateid;
             */
           // print_r($url1);
             
             $update = new stdclass;
              
                $update->id = $mediaid;
                $update->url = $url1;
                $update->replycount = $replycount;
                $update->rowscount = $rowscount;
                
                $lastupdate=$DB->update_record('testimonial_videos', $update);
                
            //    if ($lastupdate) {
           //      echo "Success!";
           //     } else {
           ///      echo "Fail!";
           //     }/
             
             
          //    print_r($url);
             //  exit();
               
             //   $url="http://localhost/moodle27d/mod/testimonial/uploads/$fileName";
          
        
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