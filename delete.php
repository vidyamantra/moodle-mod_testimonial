
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
require_once(dirname(__FILE__).'/locallib.php');
require_once ($CFG->dirroot.'/course/moodleform_mod.php');
global $DB,$USER;



$id = optional_param('cmid', 0, PARAM_INT);
//$id= $_GET['id'];
//echo $id;


if ($id) {
    $cm         = get_coursemodule_from_id('testimonial', $id, 0, false, MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $testimonial  = $DB->get_record('testimonial', array('id' => $cm->instance), '*', MUST_EXIST);
} 

$context = context_module::instance($id);

//$conn=mysqli_connect('localhost','root',"mummy","moodle27d");
 
$files=array();


if (isset($_POST['delete-file'])) {
  
    
    
   // $files=$_POST['delete-file'];
    $fiesstr= $_POST['delete-file'];
    //print_r($fiesstr);
    $files=explode(',',$_POST['delete-file']);
    
    
 //   $fileName = 'uploads/'.$_POST['delete-file'];
   // echo($_POST['delete-file']);
   
   // print_r(sizeof($files));
    
    foreach ($files as $value) {
        
      $file=$value;
  //  $withaudioext=
   // $vext="$withvideoext";
    
   // echo $withvideoext;
  //  exit();
       // $videoitemid = $DB->get_record_sql('SELECT id FROM {testimonial_videos} WHERE name = ?', array($withvideoext));
          //  $videoitemid = $DB->get_field('testimonial_videos', 'id', array ('name' => $vext));
         $sql='SELECT id FROM {testimonial_videos} WHERE name = ?';    
         $fileid = $DB->get_field_sql($sql, array($file));
      
        
        
    
       // echo $videoitemid;
       // echo 'name '.$withvideoext;
        
    
      //  if(!file_exists('uploads/'.$withvideoext)){
            if(!($DB->record_exists('files', array('contextid' =>$context->id, 'itemid'=>$fileid)))){  
                 
                 $DB->delete_records('testimonial_videos', array ('id'=> $fileid));
                // $DB->delete_records('testimonial_videos', array ('id'=> $audioitemid));
                 echo "Sorry, Video had been currupted and did not store on server<br /><br/>";
            //     mysqli_query($conn,"DELETE FROM testimonial_videos WHERE name='$withvideoext' OR name='$withaudioext' ");
                    
                 //  $DB->delete_records("testimonial_videos", array("name"=>$value));
               }

                else{
                    
                    fileDeletion($fileid,$file,$context->id);
                    fileDeletion($fileid,".",$context->id);
                                
                       $vid=$DB->delete_records('testimonial_videos', array ('id'=> $fileid));
                    //   $aud=$DB->delete_records('testimonial_videos', array ('id'=> $audioitemid));
                    // mysqli_query($conn,"DELETE FROM testimonial_videos WHERE name='$withvideoext' OR name='$withaudioext' ");
                     // $DB->delete_records("testimonial_videos", array('name'=>$withvideoext));
                     // $DB->delete_records("testimonial_videos", array('name'=>$withaudioext));
                    // echo "$file, ";
                 //   $DB->delete_records("testimonial_videos", array(sql_compare_text("name")=>$value));
             }
    }
    
    echo get_string('successdelrecent','testimonial'); 
  }
?>