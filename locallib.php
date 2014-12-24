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
/**
 * 
 * @param type $itemid
 * @param type $filename
 * @param type $contextid
 */
 function fileDeletion($itemid,$filename,$contextid){
    $fs = get_file_storage();
    // Prepare file record object
    $fileinfo = array(
        'component' => 'mod_testimonial',
        'filearea' => 'testimonial_docs',     // usually = table name
        'itemid' =>  $itemid,        // usually = ID of row in table
        'contextid' => $contextid,      // ID of context
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
 
 // function for check current browser and return the name of browser
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
 
 //update the serial numbers of testimonials 
  function updateserialnum($testimonialid,$context){
      global $DB,$USER;
      
      $queryall= $DB->get_records_sql("SELECT * FROM {testimonial_videos} WHERE testimonial_id=$testimonialid");
        if (has_capability('mod/testimonial:isstudent', $context) && !(has_capability('mod/testimonial:isadmin', $context))) {
          $queryall= $DB->get_records_sql("SELECT * FROM {testimonial_videos} WHERE user_id=$USER->id AND testimonial_id=$testimonialid");
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
      
  }
  //check user is admin or not
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