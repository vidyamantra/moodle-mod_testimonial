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
 * @package    mod_testimonial
 * @subpackage backup-moodle2
 * @copyright 2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Define all the restore steps that will be used by the restore_testimonial_activity_task
 */

/**
 * Structure step to restore one testimonial activity
 */
class restore_testimonial_activity_structure_step extends restore_activity_structure_step {

    protected function define_structure() {

        $paths = array();
       
        $userinfo = $this->get_setting_value('userinfo');
        $paths[] = new restore_path_element('testimonial', '/activity/testimonial/testimonial');
        $paths[] = new restore_path_element('testimonial_videos', '/activity/testimonial/testimonial_videos');
        if ($userinfo) {
            $paths[] = new restore_path_element('testimonial_watching', '/activity/testimonial/testimonial_watching');
        }

        // Return the paths wrapped into standard activity structure
        return $this->prepare_activity_structure($paths);
    }

    protected function process_testimonial($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        
        $data->course = $this->get_courseid();
        $data->timeopen = $this->apply_date_offset($data->timeopen);
        $data->timeclose = $this->apply_date_offset($data->timeclose);
        $data->timemodified = $this->apply_date_offset($data->timemodified);

        // insert the testimonial record
        $newitemid = $DB->insert_record('testimonial', $data);
        // immediately after inserting "activity" record, call this
        $this->apply_activity_instance($newitemid);
    }

    protected function process_testimonial_videos($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        
        $data->user_id = $this->get_new_parentid('testimonial');
       // user_id,name,url,testimonial_id
       // $data->timemodified = $this->apply_date_offset($data->timemodified);

        $newitemid = $DB->insert_record('testimonial_videos', $data);
        $this->set_mapping('testimonial_videos', $oldid, $newitemid);
    }

    protected function process_testimonial_watching($data) {
        global $DB;

        $data = (object)$data;

        $data->testimonial_id = $this->get_new_parentid('testimonial');
        $data->video_id = $this->get_mappingid('testimonial_videos', $data->id);
        $data->user_id = $this->get_mappingid('user', $data->userid);

        $newitemid = $DB->insert_record('testimonial_watching', $data);
        // No need to save this mapping as far as nothing depend on it
        // (child paths, file areas nor links decoder)
    }

    protected function after_execute() {
        // Add choice related files, no need to match by itemname (just internally handled context)
        $this->add_related_files('mod_testimonial', 'intro', null);
    }
}
