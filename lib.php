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
 * Library of interface functions and constants for module feedcam
 *
 * All the core Moodle functions, neeeded to allow the module to work
 * integrated in Moodle should be placed here.
 * All the feedcam specific functions, needed to implement all the module
 * logic, should go to locallib.php. This will help to save some memory when
 * Moodle is performing actions across all modules.
 *
 * @package    mod
 * @subpackage feedcam
 * @copyright  2011 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/** example constant */
//define('FEEDCAM_ULTIMATE_ANSWER', 42);

////////////////////////////////////////////////////////////////////////////////
// Moodle core API                                                            //
////////////////////////////////////////////////////////////////////////////////

/**
 * Returns the information on whether the module supports a feature
 *
 * @see plugin_supports() in lib/moodlelib.php
 * @param string $feature FEATURE_xx constant for requested feature
 * @return mixed true if the feature is supported, null if unknown
 */
function feedcam_supports($feature) {
    
    switch($feature) {
        case FEATURE_GROUPS:                  return false;
        case FEATURE_GROUPINGS:               return false;
        case FEATURE_GROUPMEMBERSONLY:        return false;
        case FEATURE_MOD_INTRO:               return true;
        case FEATURE_COMPLETION_TRACKS_VIEWS: return true;
        case FEATURE_COMPLETION_HAS_RULES:    return true;
        case FEATURE_GRADE_HAS_GRADE:         return false;
        case FEATURE_GRADE_OUTCOMES:          return false;
        case FEATURE_BACKUP_MOODLE2:          return true;
        case FEATURE_SHOW_DESCRIPTION:        return false;
      //  case FEATURE_COMPLETION_HAS_RULES: return true;
        default: return null;
    }
}



function feedcam_get_completion_state($course, $cm, $userid, $type) {
    global $CFG,$DB,$USER;

   // echo $cm->instance;
    echo $USER->id;
     $feedcam = $DB->get_record('feedcam', array('id'=>$cm->instance), '*', MUST_EXIST);

    // If completion option is enabled, evaluate it and return true/false
    if($feedcam->completionrecord && (!($feedcam->completionrecord && $feedcam->completionwatch))) {
        
     //  print_r('rec');
      // print_r($cm->instance);
      // echo '______________';
     //  print_r($feedcam->id);
      //  $countvid= $DB->count_records('videos', array('feedcam_id' => $feedcam->id, 'user_id' => $userid));
        
        echo $DB->record_exists('videos', array('feedcam_id'=>$cm->instance, 'user_id'=>$USER->id));
        
        return $DB->record_exists('videos', array('feedcam_id'=>$cm->instance, 'user_id'=>$USER->id));
        
    } 
    
   if($feedcam->completionwatch && (!($feedcam->completionrecord && $feedcam->completionwatch))) {
        
      //  print_r('wat');
        return $DB->record_exists('feedcam_watching', array('user_id'=>$USER->id, 'feedcam_id'=>$cm->instance));
    } 
    
    if(($feedcam->completionrecord && $feedcam->completionwatch)) {
        
     //   print_r('both');
        $rec=$DB->record_exists('videos', array('feedcam_id'=>$cm->instance, 'user_id'=>$USER->id));
        $wat=$DB->record_exists('feedcam_watching', array('user_id'=>$USER->id, 'feedcam_id'=>$cm->instance));
        
        return ($rec && $wat);
    }
    
    else {
      //  print_r('nothing');
        // Completion option is not enabled so just return $type
        return $type;
    }
   
}
/**
 * Saves a new instance of the feedcam into the database
 *
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will create a new instance and return the id number
 * of the new instance.
 *
 * @param object $feedcam An object from the form in mod_form.php
 * @param mod_feedcam_mod_form $mform
 * @return int The id of the newly inserted feedcam record
 */
function feedcam_add_instance(stdClass $feedcam, mod_feedcam_mod_form $mform = null) {
    global $DB;

    $feedcam->timecreated = time();

    # You may have to add extra stuff in here #

    return $DB->insert_record('feedcam', $feedcam);
}

/**
 * Updates an instance of the feedcam in the database
 *
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will update an existing instance with new data.
 *
 * @param object $feedcam An object from the form in mod_form.php
 * @param mod_feedcam_mod_form $mform
 * @return boolean Success/Fail
 */
function feedcam_update_instance(stdClass $feedcam, mod_feedcam_mod_form $mform = null) {
    global $DB;

    $feedcam->timemodified = time();
    $feedcam->id = $feedcam->instance;

    # You may have to add extra stuff in here #
    return $DB->update_record('feedcam', $feedcam);
}

/**
 * Removes an instance of the feedcam from the database
 *
 * Given an ID of an instance of this module,
 * this function will permanently delete the instance
 * and any data that depends on it.
 *
 * @param int $id Id of the module instance
 * @return boolean Success/Failure
 */
function feedcam_delete_instance($id) {
    global $DB;

    if (! $feedcam = $DB->get_record('feedcam', array('id' => $id))) {
        return false;
    }

    # Delete any dependent records here #

    $DB->delete_records('feedcam', array('id' => $feedcam->id));

    return true;
}

/**
 * Returns a small object with summary information about what a
 * user has done with a given particular instance of this module
 * Used for user activity reports.
 * $return->time = the time they did it
 * $return->info = a short text description
 *
 * @return stdClass|null
 */
function feedcam_user_outline($course, $user, $mod, $feedcam) {

    $return = new stdClass();
    $return->time = 0;
    $return->info = '';
    return $return;
}

/**
 * Prints a detailed representation of what a user has done with
 * a given particular instance of this module, for user activity reports.
 *
 * @param stdClass $course the current course record
 * @param stdClass $user the record of the user we are generating report for
 * @param cm_info $mod course module info
 * @param stdClass $feedcam the module instance record
 * @return void, is supposed to echp directly
 */
function feedcam_user_complete($course, $user, $mod, $feedcam) {
}

/**
 * Given a course and a time, this module should find recent activity
 * that has occurred in feedcam activities and print it out.
 * Return true if there was output, or false is there was none.
 *
 * @return boolean
 */
function feedcam_print_recent_activity($course, $viewfullnames, $timestart) {
    return false;  //  True if anything was printed, otherwise false
}

/**
 * Prepares the recent activity data
 *
 * This callback function is supposed to populate the passed array with
 * custom activity records. These records are then rendered into HTML via
 * {@link feedcam_print_recent_mod_activity()}.
 *
 * @param array $activities sequentially indexed array of objects with the 'cmid' property
 * @param int $index the index in the $activities to use for the next record
 * @param int $timestart append activity since this time
 * @param int $courseid the id of the course we produce the report for
 * @param int $cmid course module id
 * @param int $userid check for a particular user's activity only, defaults to 0 (all users)
 * @param int $groupid check for a particular group's activity only, defaults to 0 (all groups)
 * @return void adds items into $activities and increases $index
 */
function feedcam_get_recent_mod_activity(&$activities, &$index, $timestart, $courseid, $cmid, $userid=0, $groupid=0) {
}

/**
 * Prints single activity item prepared by {@see feedcam_get_recent_mod_activity()}

 * @return void
 */
function feedcam_print_recent_mod_activity($activity, $courseid, $detail, $modnames, $viewfullnames) {
}

/**
 * Function to be run periodically according to the moodle cron
 * This function searches for things that need to be done, such
 * as sending out mail, toggling flags etc ...
 *
 * @return boolean
 * @todo Finish documenting this function
 **/
function feedcam_cron () {
    return true;
}

/**
 * Returns an array of users who are participanting in this feedcam
 *
 * Must return an array of users who are participants for a given instance
 * of feedcam. Must include every user involved in the instance,
 * independient of his role (student, teacher, admin...). The returned
 * objects must contain at least id property.
 * See other modules as example.
 *
 * @param int $feedcamid ID of an instance of this module
 * @return boolean|array false if no participants, array of objects otherwise
 */
function feedcam_get_participants($feedcamid) {
    return false;
}

/**
 * Returns all other caps used in the module
 *
 * @example return array('moodle/site:accessallgroups');
 * @return array
 */
function feedcam_get_extra_capabilities() {
    return array();
}

////////////////////////////////////////////////////////////////////////////////
// Gradebook API                                                              //
////////////////////////////////////////////////////////////////////////////////

/**
 * Is a given scale used by the instance of feedcam?
 *
 * This function returns if a scale is being used by one feedcam
 * if it has support for grading and scales. Commented code should be
 * modified if necessary. See forum, glossary or journal modules
 * as reference.
 *
 * @param int $feedcamid ID of an instance of this module
 * @return bool true if the scale is used by the given feedcam instance
 */
function feedcam_scale_used($feedcamid, $scaleid) {
    global $DB;

    /** @example */
    if ($scaleid and $DB->record_exists('feedcam', array('id' => $feedcamid, 'grade' => -$scaleid))) {
        return true;
    } else {
        return false;
    }
}

/**
 * Checks if scale is being used by any instance of feedcam.
 *
 * This is used to find out if scale used anywhere.
 *
 * @param $scaleid int
 * @return boolean true if the scale is used by any feedcam instance
 */
function feedcam_scale_used_anywhere($scaleid) {
    global $DB;

    /** @example */
    if ($scaleid and $DB->record_exists('feedcam', array('grade' => -$scaleid))) {
        return true;
    } else {
        return false;
    }
}

/**
 * Creates or updates grade item for the give feedcam instance
 *
 * Needed by grade_update_mod_grades() in lib/gradelib.php
 *
 * @param stdClass $feedcam instance object with extra cmidnumber and modname property
 * @return void
 */
function feedcam_grade_item_update(stdClass $feedcam) {
    global $CFG;
    require_once($CFG->libdir.'/gradelib.php');

    /** @example */
    $item = array();
    $item['itemname'] = clean_param($feedcam->name, PARAM_NOTAGS);
    $item['gradetype'] = GRADE_TYPE_VALUE;
    $item['grademax']  = $feedcam->grade;
    $item['grademin']  = 0;

    grade_update('mod/feedcam', $feedcam->course, 'mod', 'feedcam', $feedcam->id, 0, null, $item);
}

/**
 * Update feedcam grades in the gradebook
 *
 * Needed by grade_update_mod_grades() in lib/gradelib.php
 *
 * @param stdClass $feedcam instance object with extra cmidnumber and modname property
 * @param int $userid update grade of specific user only, 0 means all participants
 * @return void
 */
function feedcam_update_grades(stdClass $feedcam, $userid = 0) {
    global $CFG, $DB;
    require_once($CFG->libdir.'/gradelib.php');

    /** @example */
    $grades = array(); // populate array of grade objects indexed by userid

    grade_update('mod/feedcam', $feedcam->course, 'mod', 'feedcam', $feedcam->id, 0, $grades);
}

////////////////////////////////////////////////////////////////////////////////
// File API                                                                   //
////////////////////////////////////////////////////////////////////////////////

/**
 * Returns the lists of all browsable file areas within the given module context
 *
 * The file area 'intro' for the activity introduction field is added automatically
 * by {@link file_browser::get_file_info_context_module()}
 *
 * @param stdClass $course
 * @param stdClass $cm
 * @param stdClass $context
 * @return array of [(string)filearea] => (string)description
 */
function feedcam_get_file_areas($course, $cm, $context) {
    return array();
}

/**
 * Serves the files from the feedcam file areas
 *
 * @param stdClass $course
 * @param stdClass $cm
 * @param stdClass $context
 * @param string $filearea
 * @param array $args
 * @param bool $forcedownload
 * @return void this should never return to the caller
 */
function feedcam_pluginfile($course, $cm, $context, $filearea, array $args, $forcedownload) {
    global $DB, $CFG;

 //   if ($context->contextlevel != CONTEXT_MODULE) {
 //       send_file_not_found();
//    }
   $itemid = array_shift($args);
    require_login($course, true, $cm);
    
    if (! $file = get_feedcam_file($cm->id,$itemid)) return false;
    send_stored_file($file);

}

////////////////////////////////////////////////////////////////////////////////
// Navigation API                                                             //
////////////////////////////////////////////////////////////////////////////////

/**
 * Extends the global navigation tree by adding feedcam nodes if there is a relevant content
 *
 * This can be called by an AJAX request so do not rely on $PAGE as it might not be set up properly.
 *
 * @param navigation_node $navref An object representing the navigation tree node of the feedcam module instance
 * @param stdClass $course
 * @param stdClass $module
 * @param cm_info $cm
 */
function feedcam_extend_navigation(navigation_node $navref, stdclass $course, stdclass $module, cm_info $cm) {
}

/**
 * Extends the settings navigation with the feedcam settings
 *
 * This function is called when the context for the page is a feedcam module. This is not called by AJAX
 * so it is safe to rely on the $PAGE.
 *
 * @param settings_navigation $settingsnav {@link settings_navigation}
 * @param navigation_node $feedcamnode {@link navigation_node}
 */
function feedcam_extend_settings_navigation(settings_navigation $settingsnav, navigation_node $feedcamnode=null) {
}



function get_feedcam_file($course_module_id,$mid) {
    
    $context = context_module::instance($course_module_id);
    $fs = get_file_storage();
    $files = $fs->get_area_files($context->id, 'mod_feedcam', 'feedcam_docs', $mid, $sort = false, $includedirs = false);
    if (!count($files)) return false;
    return array_shift($files);
} // function get_feedcam_file



function get_feedcam_doc_url($mid) {
    global $id; // the course_module id
    if (! $file = get_feedcam_file($id,$mid)) return false;
    return moodle_url::make_pluginfile_url(
        $file->get_contextid(),
        $file->get_component(),
        $file->get_filearea(),
        $file->get_itemid(),
        $file->get_filepath(),
        $file->get_filename(),
        $forcedownload = false);
} // function get_feedcam_doc_url


