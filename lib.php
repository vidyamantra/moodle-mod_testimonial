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

function testimonial_supports($feature) {
    
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
        case FEATURE_SHOW_DESCRIPTION:        return true;
        default: return null;
    }
}



function testimonial_get_completion_state($course, $cm, $userid, $type) {
    global $CFG,$DB,$USER;
     $testimonial = $DB->get_record('testimonial', array('id'=>$cm->instance), '*', MUST_EXIST);

    // If completion option is enabled, evaluate it and return true/false
    if($testimonial->completionrecord) {
        return $DB->record_exists('testimonial_videos', array('testimonial_id'=>$cm->instance, 'user_id'=>$USER->id));
    } 
    else {
        return $type;
    }
   
}
/**
 * Saves a new instance of the testimonial into the database
 *
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will create a new instance and return the id number
 * of the new instance.
 *
 * @param object $testimonial An object from the form in mod_form.php
 * @param mod_testimonial_mod_form $mform
 * @return int The id of the newly inserted testimonial record
 */
function testimonial_add_instance(stdClass $testimonial, mod_testimonial_mod_form $mform=null) {
    global $DB;

    $testimonial->timecreated = time();
    # You may have to add extra stuff in here #
    return $DB->insert_record('testimonial', $testimonial);
}

/**
 * Updates an instance of the testimonial in the database
 *
 * Given an object containing all the necessary data,
 * (defined by the form in mod_form.php) this function
 * will update an existing instance with new data.
 *
 * @param object $testimonial An object from the form in mod_form.php
 * @param mod_testimonial_mod_form $mform
 * @return boolean Success/Fail
 */
function testimonial_update_instance(stdClass $testimonial, mod_testimonial_mod_form $mform=null) {
    global $DB;
   
    $testimonial->timemodified = time();
    $testimonial->id = $testimonial->instance;
    # You may have to add extra stuff in here #
    return $DB->update_record('testimonial', $testimonial);
}

/**
 * Removes an instance of the testimonial from the database
 *
 * Given an ID of an instance of this module,
 * this function will permanently delete the instance
 * and any data that depends on it.
 *
 * @param int $id Id of the module instance
 * @return boolean Success/Failure
 */
function testimonial_delete_instance($id) {
    global $DB;

    if (! $testimonial = $DB->get_record('testimonial', array('id' => $id))) {
        return false;
    }

    # Delete any dependent records here #

    $DB->delete_records('testimonial', array('id' => $testimonial->id));

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
function testimonial_user_outline($course, $user, $mod, $testimonial) {

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
 * @param stdClass $testimonial the module instance record
 * @return void, is supposed to echp directly
 */
function testimonial_user_complete($course, $user, $mod, $testimonial) {
}

/**
 * Given a course and a time, this module should find recent activity
 * that has occurred in testimonial activities and print it out.
 * Return true if there was output, or false is there was none.
 *
 * @return boolean
 */
function testimonial_print_recent_activity($course, $viewfullnames, $timestart) {
    return false;  //  True if anything was printed, otherwise false
}

/**
 * Prepares the recent activity data
 *
 * This callback function is supposed to populate the passed array with
 * custom activity records. These records are then rendered into HTML via
 * {@link testimonial_print_recent_mod_activity()}.
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
function testimonial_get_recent_mod_activity(&$activities, &$index, $timestart, $courseid, $cmid, $userid=0, $groupid=0) {
}

/**
 * Prints single activity item prepared by {@see testimonial_get_recent_mod_activity()}

 * @return void
 */
function testimonial_print_recent_mod_activity($activity, $courseid, $detail, $modnames, $viewfullnames) {
}

/**
 * Function to be run periodically according to the moodle cron
 * This function searches for things that need to be done, such
 * as sending out mail, toggling flags etc ...
 *
 * @return boolean
 * @todo Finish documenting this function
 **/
function testimonial_cron () {
    return true;
}

/**
 * Returns an array of users who are participanting in this testimonial
 *
 * Must return an array of users who are participants for a given instance
 * of testimonial. Must include every user involved in the instance,
 * independient of his role (student, teacher, admin...). The returned
 * objects must contain at least id property.
 * See other modules as example.
 *
 * @param int $testimonialid ID of an instance of this module
 * @return boolean|array false if no participants, array of objects otherwise
 */
function testimonial_get_participants($testimonialid) {
    return false;
}

/**
 * Returns all other caps used in the module
 *
 * @example return array('moodle/site:accessallgroups');
 * @return array
 */
function testimonial_get_extra_capabilities() {
    return array();
}

////////////////////////////////////////////////////////////////////////////////
// Gradebook API                                                              //
////////////////////////////////////////////////////////////////////////////////

/**
 * Is a given scale used by the instance of testimonial?
 *
 * This function returns if a scale is being used by one testimonial
 * if it has support for grading and scales. Commented code should be
 * modified if necessary. See forum, glossary or journal modules
 * as reference.
 *
 * @param int $testimonialid ID of an instance of this module
 * @return bool true if the scale is used by the given testimonial instance
 */
function testimonial_scale_used($testimonialid, $scaleid) {
    global $DB;

    /** @example */
    if ($scaleid and $DB->record_exists('testimonial', array('id' => $testimonialid, 'grade' => -$scaleid))) {
        return true;
    } else {
        return false;
    }
}

/**
 * Checks if scale is being used by any instance of testimonial.
 *
 * This is used to find out if scale used anywhere.
 *
 * @param $scaleid int
 * @return boolean true if the scale is used by any testimonial instance
 */
function testimonial_scale_used_anywhere($scaleid) {
    global $DB;

    /** @example */
    if ($scaleid and $DB->record_exists('testimonial', array('grade' => -$scaleid))) {
        return true;
    } else {
        return false;
    }
}

/**
 * Creates or updates grade item for the give testimonial instance
 *
 * Needed by grade_update_mod_grades() in lib/gradelib.php
 *
 * @param stdClass $testimonial instance object with extra cmidnumber and modname property
 * @return void
 */
function testimonial_grade_item_update(stdClass $testimonial) {
    global $CFG;
    require_once($CFG->libdir.'/gradelib.php');

    /** @example */
    $item = array();
    $item['itemname'] = clean_param($testimonial->name, PARAM_NOTAGS);
    $item['gradetype'] = GRADE_TYPE_VALUE;
    $item['grademax']  = $testimonial->grade;
    $item['grademin']  = 0;

    grade_update('mod/testimonial', $testimonial->course, 'mod', 'testimonial', $testimonial->id, 0, null, $item);
}

/**
 * Update testimonial grades in the gradebook
 *
 * Needed by grade_update_mod_grades() in lib/gradelib.php
 *
 * @param stdClass $testimonial instance object with extra cmidnumber and modname property
 * @param int $userid update grade of specific user only, 0 means all participants
 * @return void
 */
function testimonial_update_grades(stdClass $testimonial, $userid = 0) {
    global $CFG, $DB;
    require_once($CFG->libdir.'/gradelib.php');

    /** @example */
    $grades = array(); // populate array of grade objects indexed by userid

    grade_update('mod/testimonial', $testimonial->course, 'mod', 'testimonial', $testimonial->id, 0, $grades);
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
function testimonial_get_file_areas($course, $cm, $context) {
    return array();
}

/**
 * Serves the files from the testimonial file areas
 *
 * @param stdClass $course
 * @param stdClass $cm
 * @param stdClass $context
 * @param string $filearea
 * @param array $args
 * @param bool $forcedownload
 * @return void this should never return to the caller
 */
function testimonial_pluginfile($course, $cm, $context, $filearea, array $args, $forcedownload) {
    global $DB, $CFG;

 //   if ($context->contextlevel != CONTEXT_MODULE) {
 //       send_file_not_found();
//    }
   $itemid = array_shift($args);
    require_login($course, true, $cm);
    
    if (! $file = get_testimonial_file($cm->id,$itemid)) return false;
    send_stored_file($file);

}


function testimonial_extend_navigation(navigation_node $navref, stdclass $course, stdclass $module, cm_info $cm) {
    
}

/**
 * Extends the settings navigation with the testimonial settings
 *
 * This function is called when the context for the page is a testimonial module. This is not called by AJAX
 * so it is safe to rely on the $PAGE.
 *
 * @param settings_navigation $settingsnav {@link settings_navigation}
 * @param navigation_node $testimonialnode {@link navigation_node}
 */
function testimonial_extend_settings_navigation(settings_navigation $settingsnav, navigation_node $testimonialnode=null) {
}



function get_testimonial_file($course_module_id,$mid) {
    
    $context = context_module::instance($course_module_id);
    $fs = get_file_storage();
    $files = $fs->get_area_files($context->id, 'mod_testimonial', 'testimonial_docs', $mid, $sort = false, $includedirs = false);
    if (!count($files)) return false;
    return array_shift($files);
} // function get_testimonial_file



function get_testimonial_doc_url($mid) {
    global $id; // the course_module id
    if (! $file = get_testimonial_file($id,$mid)) return false;
    return moodle_url::make_pluginfile_url(
        $file->get_contextid(),
        $file->get_component(),
        $file->get_filearea(),
        $file->get_itemid(),
        $file->get_filepath(),
        $file->get_filename(),
        $forcedownload = false);
} // function get_testimonial_doc_url


