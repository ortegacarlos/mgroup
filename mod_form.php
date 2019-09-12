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
 * The main mod_mpgroup configuration form.
 *
 * @package     mod_mpgroup
 * @copyright   2019 Carlos Ortega <carlosortega@udenar.edu.co>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/course/moodleform_mod.php');

/**
 * Module instance settings form.
 *
 * @package    mod_mpgroup
 * @copyright  2019 Carlos Ortega <carlosortega@udenar.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_mpgroup_mod_form extends moodleform_mod {

    /**
     * Defines forms elements
     */
    public function definition() {
        global $CFG, $USER;

        $mform = $this->_form;

        // Adding the "general" fieldset, where all the common settings are showed.
        $mform->addElement('header', 'general', get_string('general', 'form'));

        // Adding the standard "name" field.
        // $mform->addElement('static', 'hello', get_string('hello', 'mpgroup', array('firstname' => $USER->firstname, 'lastname' => $USER->lastname)));
        $mform->addElement('text', 'name', get_string('mpgroupname', 'mpgroup'), array('size' => '64'));
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        $mform->addHelpButton('name', 'mpgroupname', 'mpgroup');

        // Adding the "populationsize" field.
        $mform->addElement('text', 'populationsize', get_string('populationsize', 'mpgroup'), array('size' => '64'));
        #$mform->setType('populationsize', PARAM_INT);
        $mform->addRule('populationsize', null, 'required', null, 'client');
        $mform->addRule('populationsize', get_string('err_number', 'mpgroup'), 'number', null, 'client');
        #$mform->addRule('populationsize', get_string('err_number', 'mpgroup'), 'nonzero', null, 'client');
        $mform->setDefault('populationsize', 50);
        $mform->addHelpButton('populationsize', 'populationsize', 'mpgroup');

        // Adding the "selectionoperator" field.
        $mform->addElement('text', 'selectionoperator', get_string('selectionoperator', 'mpgroup'), array('size' => '64'));
        #$mform->setType('selectionoperator', PARAM_INT);
        $mform->addRule('selectionoperator', null, 'required', null, 'client');
        $mform->addRule('selectionoperator', get_string('err_number', 'mpgroup'), 'number', null, 'client');
        #$mform->addRule('selectionoperator', get_string('err_number', 'mpgroup'), 'nonzero', null, 'client');
        $mform->setDefault('selectionoperator', 40);
        $mform->addHelpButton('selectionoperator', 'selectionoperator', 'mpgroup');

        // Adding the "mutationoperator" field.
        $mform->addElement('text', 'mutationoperator', get_string('mutationoperator', 'mpgroup'), array('size' => '64'));
        #$mform->setType('mutationoperator', PARAM_FLOAT);
        $mform->addRule('mutationoperator', null, 'required', null, 'client');
        $mform->addRule('mutationoperator', get_string('err_number', 'mpgroup'), 'number', null, 'client');
        #$mform->addRule('mutationoperator', get_string('err_number', 'mpgroup'), 'nonzero', null, 'client');
        $mform->setDefault('mutationoperator', 0.2);
        $mform->addHelpButton('mutationoperator', 'mutationoperator', 'mpgroup');

        // Adding the "userfile" field.
        $mform->addElement('filepicker', 'userfile', get_string('userfile', 'mpgroup'), null,
                array('maxbytes'=>50, 'accepted_types'=>'.txt'));
        $mform->addRule('userfile', null, 'required', null, 'client');
        $mform->addHelpButton('userfile', 'userfile', 'mpgroup');

        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEANHTML);
        }

        // Adding the standard "intro" and "introformat" fields.
        $this->standard_intro_elements();

        /*
        // Adding the rest of mpgroup settings, spreading all them into this fieldset
        // ... or adding more fieldsets ('header' elements) if needed for better logic.
        $mform->addElement('static', 'label1', 'mpgroupsettings', get_string('mpgroupsettings', 'mpgroup'));
        $mform->addElement('header', 'mpgroupfieldset', get_string('mpgroupfieldset', 'mpgroup'));

        */
        // Add standard elements.
        $this->standard_coursemodule_elements();

        // Add standard buttons.
        $this->add_action_buttons();
        
    }
}
