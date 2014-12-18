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
require_once($CFG->dirroot.'/course/moodleform_mod.php');
require_once($CFG->dirroot.'/mod/testimonial/locallib.php');
require_once($CFG->libdir.'/filelib.php');

/**
 * Module instance settings form
 */
class mod_testimonial_mod_form extends moodleform_mod {
    /**
     * Defines forms elements
     */
    public function definition() {
        
        global $CFG, $DB;
        
        $mform = $this->_form;
        $config = get_config('page');
        //-------------------------------------------------------------------------------
        // Adding the "general" fieldset, where all the common settings are showed
        $mform->addElement('header', 'general', get_string('general', 'form'));
        // Adding the standard "name" field
        $mform->addElement('text', 'name', get_string('testimonialname', 'testimonial'), array('size'=>'64'));
      
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
         }
        else {
            $mform->setType('name', PARAM_CLEAN);
         }
         
       //  $mform->addRule('name', null, 'required', null, 'client');
      //  $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
     //   $this->add_intro_editor($config->requiremodintro);
         
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        
        // Adding the standard "intro" and "introformat" fields
         $this->add_intro_editor();
         $mform->addHelpButton('introeditor', 'introeditor', 'testimonial');
        //-------------------------------------------------------------------------------
        // Adding the "additional" fieldset, where all the additional settings are showed   
         $mform->addElement('header', 'additional', get_string('additional', 'testimonial'));
        // Adding the standard "select" field
         
     //    $hours=array();
     //    $hours[0]="Time in hours";
     //     for($counthr=1;$counthr<=24;$counthr++){
     //        $hours[$counthr]="$counthr";
     //     }
     //   $options = $hours;
        
        
     //   $minutes=array();
     //    $minutes[0]="Time in minutes";
      //    for($countmin=1;$countmin<=60;$countmin++){
     //       $minutes[$countmin]="$countmin";
      //    }
     //   $optionsmin = $minutes;
            
         
       // $select = $mform->addElement('select', 'studenttime', get_string('studenttime', 'testimonial'), $options);
      //  $select = $mform->addElement('select', 'studenttimemin', get_string('studenttime2', 'testimonial'), $optionsmin);
        
        $select = $mform->addElement('duration', 'studenttime', get_string('studenttime', 'testimonial'),
                                                         array('optional' => true));
        
        
        $mform->addHelpButton('studenttime','studenttime', 'testimonial');
       //  $mform->addHelpButton('introeditor', 'introeditor', 'testimonial');
        // This will select the time in hours.
        $mform->addElement('advcheckbox', 'teacherdelete', get_string('teacherdelete', 'testimonial'), 'Yes', array('group' => 1), array(0, 1));
        $mform->addHelpButton('teacherdelete','teacherdelete', 'testimonial');
        //-------------------------------------------------------------------------------
        // Adding the rest of testimonial settings, spreeading all them into this fieldset
        // or adding more fieldsets ('header' elements) if needed for better logic
       // $mform->addElement('static', 'label1', 'testimonialsetting1', 'Your testimonial fields go here. Replace me!');

      //  $mform->addElement('header', 'testimonialfieldset', get_string('testimonialfieldset', 'testimonial'));
      //  $mform->addElement('static', 'label2', 'testimonialsetting2', 'Your testimonial fields go here. Replace me!');
        //-------------------------------------------------------------------------------
        // add standard elements, common to all modules
        $this->standard_coursemodule_elements();
        //-------------------------------------------------------------------------------
        // add standard buttons, common to all modules
        $this->add_action_buttons();
    }

   function get_data() {
        $data = parent::get_data();
        if (!$data) {
            return false;
        }
        // Set up completion section even if checkbox is not ticked
        if (!empty($data->completionunlocked)) {
            if (empty($data->completionrecord)) {
                $data->completionrecord = 0;
            }
       }
    return $data;
  }

   function add_completion_rules() {
        $mform =& $this->_form;
        $mform->addElement('checkbox', 'completionrecord', '', get_string('completionrecord', 'testimonial'));
        return array('completionrecord');
   }

    
  function completion_rule_enabled($data) {
    if(!empty($data['completionrecord'])){
        return $data['completionrecord'];
    }
  }
  
}