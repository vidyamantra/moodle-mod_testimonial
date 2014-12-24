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

/**
 * Define the complete testimonial structure for backup, with file and id annotations
 */     
class backup_testimonial_activity_structure_step extends backup_activity_structure_step {
 
    protected function define_structure() {
        // To know if we are including userinfo
        $userinfo = $this->get_setting_value('userinfo');
        
        // Define each element separated
        $testimonial = new backup_nested_element('testimonial', array('id'), array('course', 'name', 'intro','introformat', 'timecreated', 'timemodified', 'completionrecord','studenttime','studenttimemin','teacherdelete'));
        $videos = new backup_nested_element('videos');
        $video = new backup_nested_element('video', array('id'), array('user_id','name','videotitle','url','question','datetime','replycount','rowscount'));

        // Build the tree
        $testimonial->add_child($videos);
        $videos->add_child($video);
 
        // Define sources
        $testimonial->set_source_table('testimonial', array('id' => backup::VAR_ACTIVITYID));     
        if ($userinfo) {
         $video->set_source_sql('SELECT * FROM {testimonial_videos} WHERE testimonial_id = ?', array(backup::VAR_PARENTID));
        }
        // Define id annotation
        $video->annotate_ids('user', 'user_id');
        // Define file annotations
         $testimonial->annotate_files('mod_testimonial', 'intro', null); // This file area does not have an itemid
 
        // Return the root element (testimonial), wrapped into standard activity structure
        return $this->prepare_activity_structure($testimonial);
 
    }
}

