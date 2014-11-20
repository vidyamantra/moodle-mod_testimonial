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
 * The mod_feedcam course module viewed event.
 *
 * @package mod_feedcam
 * @copyright 2014 Krishna Pratap Singh <krishna@vidyamantra.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Define the complete feedcam structure for backup, with file and id annotations
 */     
class backup_feedcam_activity_structure_step extends backup_activity_structure_step {
 
    protected function define_structure() {
 
        // To know if we are including userinfo
        $userinfo = $this->get_setting_value('userinfo');
 
        
        // Define each element separated
        $feedcam = new backup_nested_element('feedcam', array('id'), array('course', 'name', 'intro','introformat', 'timecreated', 'timemodified', 'completionrecord', 'completionwatch'));
        $video = new backup_nested_element('videos', array('id'), array('user_id','name','url'));
        $watching = new backup_nested_element('watching', array('id'), array('user_id','video_id'));

 
        // Build the tree
        $feedcam->add_child($video);
        $feedcam->add_child($watching);
 
        // Define sources
        $feedcam->set_source_table('feedcam', array('id' => backup::VAR_ACTIVITYID));

         $video->set_source_sql('SELECT * FROM {videos} WHERE feedcam_id = ?', array(backup::VAR_PARENTID));
         if ($userinfo) {
              $watching->set_source_table('feedcam_watching', array('feedcam_id' => backup::VAR_PARENTID));
          }
 
        // Define file annotations
         $watching->annotate_ids('user', 'user_id');
         $feedcam->annotate_files('mod_feedcam', 'intro', null); // This file area does not have an itemid
 
        // Return the root element (feedcam), wrapped into standard activity structure
        return $this->prepare_activity_structure($feedcam);
 
    }
}

