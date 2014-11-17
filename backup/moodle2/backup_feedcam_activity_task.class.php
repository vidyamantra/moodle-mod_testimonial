<?php
 
require_once($CFG->dirroot . '/mod/feedcam/backup/moodle2/backup_feedcam_stepslib.php'); // Because it exists (must)
require_once($CFG->dirroot . '/mod/feedcam/backup/moodle2/backup_feedcam_settingslib.php'); // Because it exists (optional)
 
/**
 * feedcam backup task that provides all the settings and steps to perform one
 * complete backup of the activity
 */
class backup_feedcam_activity_task extends backup_activity_task {
 
    /**
     * Define (add) particular settings this activity can have
     */
    protected function define_my_settings() {
        // No particular settings for this activity
      //  $this->add_step(new backup_feedcam_activity_structure_step('feedcam_structure', 'feedcam.xml'));
    }
 
    /**
     * Define (add) particular steps this activity can have
     */
    protected function define_my_steps() {
        // Feedcam only has one structure step
       $this->add_step(new backup_feedcam_activity_structure_step('feedcam_structure', 'feedcam.xml'));
    }
 
    /**
     * Code the transformations to perform in the activity in
     * order to get transportable (encoded) links
     */
    static public function encode_content_links($content) {
         global $CFG;
 
        $base = preg_quote($CFG->wwwroot,"/");
 
      
        // Link to the list of feedcams
        $search="/(".$base."\/mod\/feedcam\/index.php\?id\=)([0-9]+)/";
        $content= preg_replace($search, '$@FEEDCAMINDEX*$2@$', $content);
 
        // Link to feedcam view by moduleid
        $search="/(".$base."\/mod\/feedcam\/view.php\?id\=)([0-9]+)/";
        $content= preg_replace($search, '$@FEEDCAMVIEWBYID*$2@$', $content);
 
        return $content;
    }
}