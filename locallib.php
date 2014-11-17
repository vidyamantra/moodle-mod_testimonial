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
 * Internal library of functions for module feedcam
 *
 * All the feedcam specific functions, needed to implement the module
 * logic, should go here. Never include this file from your lib.php!
 *
 * @package    mod
 * @subpackage feedcam
 * @copyright  2011 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Does something really useful with the passed things
 *
 * @param array $things
 * @return object
 */
//function feedcam_do_something_useful(array $things) {
//    return new stdClass();
//}



  
   function fileDeletion($itemid,$filename,$contextid){
        
         $fs = get_file_storage();
            // Prepare file record object
            $fileinfo = array(
                'component' => 'mod_feedcam',
                'filearea' => 'feedcam_docs',     // usually = table name
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