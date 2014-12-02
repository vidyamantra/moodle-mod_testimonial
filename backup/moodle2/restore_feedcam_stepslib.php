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
 * @package    mod_feedcam
 * @subpackage backup-moodle2
 * @copyright 2010 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Define all the restore steps that will be used by the restore_feedcam_activity_task
 */

/**
 * Structure step to restore one feedcam activity
 */
class restore_feedcam_activity_structure_step extends restore_activity_structure_step {

    protected function define_structure() {

        $paths = array();
       
        $userinfo = $this->get_setting_value('userinfo');
        $paths[] = new restore_path_element('feedcam', '/activity/feedcam/feedcam');
        $paths[] = new restore_path_element('videos', '/activity/feedcam/videos');
        if ($userinfo) {
            $paths[] = new restore_path_element('feedcam_watching', '/activity/feedcam/feedcam_watching');
        }

        // Return the paths wrapped into standard activity structure
        return $this->prepare_activity_structure($paths);
    }

    protected function process_feedcam($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;
        
        $data->course = $this->get_courseid();
        $data->timeopen = $this->apply_date_offset($data->timeopen);
        $data->timeclose = $this->apply_date_offset($data->timeclose);
        $data->timemodified = $this->apply_date_offset($data->timemodified);

        // insert the feedcam record
        $newitemid = $DB->insert_record('feedcam', $data);
        // immediately after inserting "activity" record, call this
        $this->apply_activity_instance($newitemid);
    }

    protected function process_videos($data) {
        global $DB;

        $data = (object)$data;
        $oldid = $data->id;

        
        $data->user_id = $this->get_new_parentid('feedcam');
       // user_id,name,url,feedcam_id
       // $data->timemodified = $this->apply_date_offset($data->timemodified);

        $newitemid = $DB->insert_record('videos', $data);
        $this->set_mapping('videos', $oldid, $newitemid);
    }

    protected function process_feedcam_watching($data) {
        global $DB;

        $data = (object)$data;

        $data->feedcam_id = $this->get_new_parentid('feedcam');
        $data->video_id = $this->get_mappingid('videos', $data->id);
        $data->user_id = $this->get_mappingid('user', $data->userid);

        $newitemid = $DB->insert_record('feedcam_watching', $data);
        // No need to save this mapping as far as nothing depend on it
        // (child paths, file areas nor links decoder)
    }

    protected function after_execute() {
        // Add choice related files, no need to match by itemname (just internally handled context)
        $this->add_related_files('mod_feedcam', 'intro', null);
    }
}
