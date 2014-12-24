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

require_once($CFG->dirroot . '/mod/testimonial/backup/moodle2/backup_testimonial_stepslib.php'); // Because it exists (must)
require_once($CFG->dirroot . '/mod/testimonial/backup/moodle2/backup_testimonial_settingslib.php'); // Because it exists (optional)
 
/**
 * testimonial backup task that provides all the settings and steps to perform one
 * complete backup of the activity
 */
class backup_testimonial_activity_task extends backup_activity_task {
 
    /**
     * Define (add) particular settings this activity can have
     */
    protected function define_my_settings() {
        // No particular settings for this activity
      //  $this->add_step(new backup_testimonial_activity_structure_step('testimonial_structure', 'testimonial.xml'));
    }
    /**
     * Define (add) particular steps this activity can have
     */
    protected function define_my_steps() {
        // Testimonial only has one structure step
       $this->add_step(new backup_testimonial_activity_structure_step('testimonial_structure', 'testimonial.xml'));
    }
 
    /**
     * Code the transformations to perform in the activity in
     * order to get transportable (encoded) links
     */
    static public function encode_content_links($content) {
    //     global $CFG;
 
    //    $base = preg_quote($CFG->wwwroot,"/");
        // Link to the list of testimonials
     //   $search="/(".$base."\/mod\/testimonial\/index.php\?id\=)([0-9]+)/";
     //   $content= preg_replace($search, '$@TESTIMONIALINDEX*$2@$', $content);
 
        // Link to testimonial view by moduleid
     //   $search="/(".$base."\/mod\/testimonial\/view.php\?id\=)([0-9]+)/";
     //   $content= preg_replace($search, '$@TESTIMONIALVIEWBYID*$2@$', $content);
 
        return $content;
    }
}