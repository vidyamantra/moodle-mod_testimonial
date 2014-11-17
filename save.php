<?php

/**
 * save and upload the audio video files into directories and link of files in database

 *
 * @package    mod
 * @subpackage feedcam
 * @copyright  2014 krishna
 * @license    http://www.vidyamantra.com
 */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');
require_once ($CFG->dirroot.'/course/moodleform_mod.php');
global $DB,$USER;

/*
$id = optional_param('id', 0, PARAM_INT); // course_module ID, or
$n  = optional_param('n', 0, PARAM_INT);  // feedcam instance ID - it should be named as the first character of the module


/*
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

//require_login($course, true, $cm);
$context = context_module::instance($cm->id);
   
echo $context;
echo $context->id;
exit();
*/

$id = optional_param('cmid', 0, PARAM_INT);
//$id= $_GET['cmid'];
if ($id) {
    $cm         = get_coursemodule_from_id('feedcam', $id, 0, false, MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $feedcam  = $DB->get_record('feedcam', array('id' => $cm->instance), '*', MUST_EXIST);
} 

$context = context_module::instance($cm->id);

foreach(array('video', 'audio') as $type) {
    
    if (isset($_FILES["${type}-blob"])) {
    
     
     // echo 'uploads/';
        
//	$fileName = $_POST["${type}-filename"];
//       $uploadDirectory = 'uploads/'.$fileName;
      
        
        
       $uploaded_file_path = $_FILES["${type}-blob"]["tmp_name"];  // temp path to the actual file
        $filename = $_POST["${type}-filename"];                // the original (human readable) filename
       
        
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
        
         $record = new stdClass();
               $record->feedcam_id=$feedcam->id;
               $record->user_id = $USER->id;
               $record->name = $filename;
              // $record->url = '';
               $lastinsertid = $DB->insert_record('videos', $record, false);
        
               
               
      //  $query = $DB->get_records_sql('SELECT * FROM {videos} WHERE name = ?', array($filename));
       //  $query = $DB->get_records('videos', array('name'=>$filename));
         
                $sql='SELECT id FROM {videos} WHERE name = ?';    
             $mediaid = $DB->get_field_sql($sql, array($filename));
             $mediaid=(int)$mediaid;
           // foreach ($query as $value) { 
          //      $mediaid= $value->id;  
          //   }      
               
           //print_r($mediaid);    
               
      //  $file_storage = get_file_storage();
      //  $context = context_module::instance($id);
        
            $fileinfo = array(
                'contextid' => $context->id,
                'component' => 'mod_feedcam',    // mod_[your-mod-name]
                'filearea' => 'feedcam_docs',   // arbitrary string
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
                
             $url=get_feedcam_doc_url($midint);
             $url1="$url";
             
           //  echo $url;
              
        /*    $record1 = new stdClass();
            
               $record1->id = $midint;
               $record1->url = $url;
            //   $lastinsertid = $DB->insert_record('videos', $record, false);
             $lastupdateid = $DB->update_record('videos', $record1);
             
             echo $lastupdateid;
             */
             
             $update = new stdclass;
              
                $update->id = $midint;
                $update->url = $url1;
                
                $lastupdate=$DB->update_record('videos', $update);
                
            //    if ($lastupdate) {
           //      echo "Success!";
           //     } else {
           ///      echo "Fail!";
           //     }/
             
             
          //    print_r($url);
             //  exit();
               
             //   $url="http://localhost/moodle27d/mod/feedcam/uploads/$fileName";
          
        
    }
}

                $eventdata1 = array();
            $eventdata1['context'] = $context;
            $eventdata1['objectid'] = $mediaid;
            $eventdata1['userid'] = $USER->id;
            $eventdata1['courseid'] = $course->id;

            $event = \mod_feedcam\event\video_submitted::create($eventdata1);
            $event->add_record_snapshot('course', $course);
            $event->add_record_snapshot('course_modules', $cm);
            $event->trigger();  

  


?>