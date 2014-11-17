<?php

/**
 * Define the complete feedcam structure for backup, with file and id annotations
 */     
class backup_feedcam_activity_structure_step extends backup_activity_structure_step {
 
    protected function define_structure() {
 
        // To know if we are including userinfo
        $userinfo = $this->get_setting_value('userinfo');
 
        // Define each element separated
        $feedcam = new backup_nested_element('feedcam', array('id'), array(
            'course', 'name', 'intro',
            'introformat', 'timecreated', 'timemodified', 'completionrecord', 'completionwatch'));
 
    //    $videos = new backup_nested_element('video');

        $video = new backup_nested_element('videos', array('id'), array(
                                                'user_id',
                                                'name',
                                                'url'));

    //    $watchings = new backup_nested_element('watching');

        $watching = new backup_nested_element('watching', array('id'), array(
                                                'user_id',
                                                'video_id'));

 
        // Build the tree
        $feedcam->add_child($video);
       // $videos->add_child($video);
 
        $feedcam->add_child($watching);
       // $watchings->add_child($watching);
 
      
        
        // Define sources
        $feedcam->set_source_table('feedcam', array('id' => backup::VAR_ACTIVITYID));

          $video->set_source_sql('
                SELECT *
                  FROM {videos}
                 WHERE feedcam_id = ?',
                array(backup::VAR_PARENTID));
        
         if ($userinfo) {
              $watching->set_source_table('feedcam_watching', array('feedcam_id' => backup::VAR_PARENTID));
        }
 
        // Define id annotations
      //   $watching->annotate_ids('user', 'user_id');
 
        // Define file annotations
          $watching->annotate_ids('user', 'user_id');
         $feedcam->annotate_files('mod_feedcam', 'intro', null); // This file area does not have an itemid
 
        // Return the root element (feedcam), wrapped into standard activity structure
        return $this->prepare_activity_structure($feedcam);
 
    }
}

