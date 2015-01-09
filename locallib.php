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

defined('MOODLE_INTERNAL') || die();

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');

$id = optional_param('id', 0, PARAM_INT); // course_module ID, or

if ($id) {
    $cm         = get_coursemodule_from_id('testimonial', $id, 0, false, MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $testimonial  = $DB->get_record('testimonial', array('id' => $cm->instance), '*', MUST_EXIST);
    
    $context = context_module::instance($cm->id);
}
/**
 * 
 * @param int $itemid
 * @param string $filename
 */
 function fileDeletion($itemid,$filename){
    global $context;
    $fs = get_file_storage();
    // Prepare file record object
    $fileinfo = array(
        'component' => 'mod_testimonial',
        'filearea' => 'testimonial_docs',     // usually = table name
        'itemid' =>  $itemid,        // usually = ID of row in table
        'contextid' => $context->id,      // ID of context
        'filepath' => '/',               // any path beginning and ending in /
        'filename' => $filename);    // any filename
    // Get file
    $file = $fs->get_file($fileinfo['contextid'], $fileinfo['component'], $fileinfo['filearea'], 
            $fileinfo['itemid'], $fileinfo['filepath'], $fileinfo['filename']);
    // Delete it if it exists
    if ($file) {
        $file->delete();
    }
 }
 /**
  * 
  * @return string $ub
  */
 function checkBrowser(){
    $u_agent = $_SERVER['HTTP_USER_AGENT'];
       $ub = '';
        if(preg_match('/MSIE/i',$u_agent)){
            $ub = "ie";}
        elseif(preg_match('/Firefox/i',$u_agent)){
            $ub = "mozilla";}
        elseif(preg_match('/Chrome/i',$u_agent)){
            $ub = "chrome";}
        elseif(preg_match('/Safari/i',$u_agent)){
            $ub = "safari";}
        elseif(preg_match('/Flock/i',$u_agent)){
            $ub = "flock";}
        elseif(preg_match('/Opera/i',$u_agent)){
            $ub = "opera";}
        elseif(preg_match('/Netscape/i',$u_agent)){
            $ub = "netscape";}
        
    return $ub;
 }
 
 /**
  * 
  * @global object $DB
  * @global object $USER
  * @param int $testimonialid
  * @param object $context
  */ 
 function update_serial_num(){
    global $DB,$USER,$context,$testimonial;
      
    $queryall= $DB->get_records_sql("SELECT * FROM {testimonial_videos} WHERE testimonial_id=$testimonial->id");
     if (has_capability('mod/testimonial:preview', $context) && !(has_capability('mod/testimonial:manage', $context))) {
       $queryall= $DB->get_records_sql("SELECT * FROM {testimonial_videos} WHERE user_id=$USER->id AND testimonial_id=$testimonial->id");
     }
     
     $sno=0;
     foreach ($queryall as $value) { 
       $revitem= strrev($value->name);
        $str = $revitem;
        $char=substr( $str, 0, 4 );
        $vid = $value->id;
        $rowscount = $value->rowscount;
        if($char=='mbew'){
          $sno++;
        }
        $update = new stdclass;
        $update->id = $vid;
        $update->rowscount = $sno;
        $lastupdate=$DB->update_record('testimonial_videos', $update);
     }
  }
  /**
   * 
   * @global object $USER
   * @return boolean
   */
  function isadmin(){
    global $USER;
    $admins = get_admins();
      $isadmin = false;
       foreach ($admins as $admin) {
         if ($USER->id == $admin->id) { 
             $isadmin = true; break; 
         }
       }
    return $isadmin;  
  }
 /**
  * 
  * @return \html_table
  */
  function create_table(){
    $table = new html_table();
    
     $table->head = array ();
     $table->align=array();
     $table->size=array();
     $table->data = array();
     $table->rowclasses = array();
     
    return $table; 
  }
  /**
   * 
   * @global object $DB
   * @global object $sql
   * @global object $context
   * @global object $testimonial
   * @param array $names
   */
 function testimonial_deletion($names){
   global $DB,$sql,$context,$testimonial;
   
   foreach($names as $value){
       $idarr=array();
       if(isset($value)){
           $idarr = (explode('/',$value,2));
           $itemid=$idarr[0];
           $itemname=$idarr[1];

           //getting file name without extension
           $revitem= strrev($itemname);
           $cropeditem= substr($revitem,4);
           $aitemname=  trim(strrev($cropeditem).'wav');
           
           $sql='SELECT id FROM {testimonial_videos} WHERE name = ? AND testimonial_id = ?';    
           $aitemid = $DB->get_field_sql($sql, array($aitemname,$testimonial->id));

         if(!($DB->record_exists('files', array('contextid' =>$context->id, 'itemid'=>$itemid)))){  
               $DB->delete_records('testimonial_videos', array ('id'=> $itemid));
              if($aitemid){ 
                $DB->delete_records('testimonial_videos', array ('id'=> $aitemid));
              }
            }
        else{
            //deletion from moodle directory structure
            fileDeletion($itemid,$itemname,$context->id);fileDeletion($itemid,".",$context->id);
            
            if($aitemid){ 
             fileDeletion($aitemid,$aitemname,$context->id); fileDeletion($aitemid,".",$context->id);
            }
            //delete data from videos table
            $vid=$DB->delete_records('testimonial_videos', array ('id'=> $itemid));
            if($aitemid){ 
             $aid=$DB->delete_records('testimonial_videos', array ('id'=> $aitemid));
            }
         }
     }
  }
}

/**
 * 
 * @global object $DB
 * @global object $sql
 * @global object $context
 * @global object $testimonial
 * @param int $totaltestimonial
 * @return string
 */
 function student_stats($totaltestimonial){
     global $DB,$sql,$context,$testimonial;
     $row='';$statbox='';

      $sql='SELECT DISTINCT user_id FROM {testimonial_videos} WHERE testimonial_id = ?';    
      $totalstudent = $DB->get_records_sql($sql, array($testimonial->id));
             
       foreach($totalstudent as $value){
         $sql2='SELECT * FROM {testimonial_videos} WHERE user_id = ? AND testimonial_id = ?';
         $sql3='SELECT username FROM {user} WHERE id = ?';

         $totalreplyperstu = $DB->get_records_sql($sql2, array($value->user_id,$testimonial->id));
         $usernameper = $DB->get_records_sql($sql3, array($value->user_id));
         //student names
         foreach ($usernameper as $value) {
            $uservalues=$value->username;
         }
         //attempted testimonial
         $replyperstudent=0;
         foreach ($totalreplyperstu as $value) {
            if(isvideofile($value->name)=='mbew'){
               $replyperstudent++;
             }
         }
      //    $replyperstudent=round($replyperstudent/2);
          $uservalues=$uservalues.' | '.$replyperstudent;
         //percentage ratio per student
         if(isset($totaltestimonial)){  
          $uservalues=$uservalues.' | '.round(($replyperstudent*100)/$totaltestimonial).'%';
         }
         else{
          $uservalues=$uservalues.' | insufficient data';
         }
       if(!empty($row)){
         $row= $row.' , ['.$uservalues.'] ';  
        }
       else{ 
        $row= $row.' ['.$uservalues.'] ';
       }
      }
    return $row;  
 } 
 /**
  * 
  * @param string $name
  * @return string
  */
 function isvideofile($name){
     $revitem= strrev($name);
     $str = $revitem;
     $chars=substr( $str, 0, 4 );
    return $chars;
 }
  
  