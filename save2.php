<?php/*
echo "abcd";
require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
global $DB;
//require_once ($CFG->dirroot.'/course/moodleform_mod.php');

$id = optional_param('id', 0, PARAM_INT); // course_module ID, or
$n  = optional_param('n', 0, PARAM_INT);  // newmodule instance ID - it should be named as the first character of the module

if ($id) {
    $cm         = get_coursemodule_from_id('newmodule', $id, 0, false, MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $newmodule  = $DB->get_record('newmodule', array('id' => $cm->instance), '*', MUST_EXIST);
} elseif ($n) {
    $newmodule  = $DB->get_record('newmodule', array('id' => $n), '*', MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $newmodule->course), '*', MUST_EXIST);
    $cm         = get_coursemodule_from_instance('newmodule', $newmodule->id, $course->id, false, MUST_EXIST);
} else {
    error('You must specify a course_module ID or an instance ID');
}

//require_login($course, true, $cm);
//$context = get_context_instance(CONTEXT_MODULE, $cm->id);
$context = context_module::instance($id);




foreach(array('video', 'audio') as $type) {
    if (isset($_FILES["${type}-blob"])) {
    
     // echo 'uploads/';
        
	//$fileName = $_POST["${type}-filename"];
                                
       // $uploadDirectory = '/uploads/'.$fileName;
    //   $uploadDirectory = 'uploads/'.$fileName;
      
       
       
       
      /*    $uploaded_file_path = $_FILES["${type}-blob"]["tmp_name"];  // temp path to the actual file
          $filename = $_POST["${type}-filename"];                // the original (human readable) filename
      
          $file_storage = get_file_storage();
       

        // $context = context_module::instance($id);
         $fileinfo = array(
             'contextid' => $context->id,
             'component' => 'mod_newmodule',       // mod_[your-mod-name]
             'filearea' => 'newmodule_docs',  // arbitrary string
             'itemid' => $id,               // use a unique id in the context of the filearea and you should be safe
             'filepath' => '/',             // virtual path
             'filename' => $filename);      // virtual filename

        /// $file_storage = get_file_storage();
         if ($file_storage->file_exists($fileinfo['contextid'],
                              $fileinfo['component'],
                              $fileinfo['filearea'],
                              $fileinfo['itemid'],
                              $fileinfo['filepath'],
                              $fileinfo['filename'])) return false; // (this code is actually in a function)

         $file=$file_storage->create_file_from_pathname($fileinfo, $uploaded_file_path);
       
       
    */
         
         
         $fs = get_file_storage();
 
            // Prepare file record object
            $fileinfo = array(
                'contextid' => $context->id, // ID of context
                'component' => 'newmodule',     // usually = table name
                'filearea' => 'myarea',     // usually = table name
                'itemid' => 0,               // usually = ID of row in table
                'filepath' => '/',           // any path beginning and ending in /
                'filename' => 'myfile.txt'); // any filename

            // Create file containing text 'hello world'
            $fs->create_file_from_string($fileinfo, 'hello world');
       
     
      //  if (!move_uploaded_file($_FILES["${type}-blob"]["tmp_name"], $uploadDirectory)) {
       //     echo(" problem moving uploaded file");
      //  }
            // echo "<br>".$fileName." has been uploaded";
	//	echo($fileName);
                 
               //  $tmp=$_FILES["${type}-blob"]["tmp_name"];
             //    $url="http://localhost/moodle27d/mod/newmodule/uploads/$fileName";
                 
         
       //  mysqli_query($conn,"INSERT INTO videos VALUES ('','$fileName','$url')");         //db
         
        // $record = new stdClass();
          //      $record->name = $filename;
           //     $record->url = $url;
          //      $lastinsertid = $DB->insert_record('videos', $record, false);
    }
}
?>