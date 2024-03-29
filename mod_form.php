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
 * The main mod_mgroup configuration form.
 *
 * @package     mod_mgroup
 * @copyright   2019 Carlos Ortega <carlosortega@udenar.edu.co> Oscar Revelo Sánchez <orevelo@udenar.edu.co> Jesús Insuasti Portilla <insuasty@udenar.edu.co>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/course/moodleform_mod.php');

/**
 * Module instance settings form.
 *
 * @package    mod_mgroup
 * @copyright  2019 Carlos Ortega <carlosortega@udenar.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_mgroup_mod_form extends moodleform_mod {

    /**
     * Defines forms elements
     */
    public function definition() {
        global $CFG, $PAGE, $DB, $COURSE;

        $PAGE->requires->js_call_amd('mod_mgroup/add_remove_characteristics', 'init');
        
        $mform = $this->_form;

        // Adding the "general" fieldset, where all the common settings are showed.
        $mform->addElement('header', 'general', get_string('general', 'form'));

        // Adding the standard "name" field.
        $mform->addElement('text', 'name', get_string('mgroupname', 'mgroup'), array('size' => '64'));
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEANHTML);
        }
        $mform->applyFilter('name', 'trim');
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        $mform->addHelpButton('name', 'mgroupname', 'mgroup');

        // Adding the "groupsize" field.
        $mform->addElement('text', 'groupsize', get_string('groupsize', 'mgroup'), array('size' => '64'));
        $mform->setType('groupsize', PARAM_INT);
        $mform->addRule('groupsize', null, 'required', null, 'client');
        $mform->addRule('groupsize', null, 'numeric', 'extraruledata', 'client');
        $mform->addRule('groupsize', null, 'nopunctuation', null, 'client');
        $mform->setDefault('groupsize', 3);
        $mform->addHelpButton('groupsize', 'groupsize', 'mgroup');

        // Adding the "MBFI" instance
        $datasource = array();
        if ($DB->record_exists('modules', array('name' => 'mbfi'))) {
            $course = $DB->get_record('course', array('id' => $COURSE->id), '*', MUST_EXIST);
            $recordsmbfi = get_all_instances_in_course('mbfi', $course);
        }
        if (!empty($recordsmbfi)) {
            $options = array();
            foreach ($recordsmbfi as $recordmbfi) {
                $options[(int)$recordmbfi->id] = $recordmbfi->name;
            }
            $datasource[] = $mform->createElement('radio', 'datasource', '', get_string('mbfi', 'mgroup'), 1, null);
            $datasource[] = $mform->createElement('radio', 'datasource', '', get_string('uploadfile', 'mgroup'), 0, null);
            $mform->addGroup($datasource, 'datasourcear', get_string('datasource', 'mgroup'), array('<br />'), false);
            $mform->addHelpButton('datasorucear', 'datasource', 'mgroup');
            $mform->addRule('datasourcear', null, 'required', null, 'client');
            $mform->setDefault('datasource', $datasource[0]->_attributes['value']);
            $mbfi = $mform->addElement('select', 'mbfi', get_string('mbfiar', 'mgroup'), $options, null);
            $mbfi->setSelected(array_key_first($options));
            $mform->addHelpButton('mbfi', 'mbfiar', 'mgroup');
            $mform->hideIf('mbfi', 'datasource', 'neq', 1);
        }

        // Adding the "userfile" field.
        $mform->addElement('filepicker', 'userfile', get_string('userfile', 'mgroup'), null,
                array('maxbytes'=>1048576, 'accepted_types'=>'.csv'));
        $mform->addHelpButton('userfile', 'userfile', 'mgroup');
        if (empty($recordsmbfi)) {
            $mform->addRule('userfile', null, 'required', null, 'client');
        } else {
            $mform->hideIf('userfile', 'datasource', 'neq', 0);
        }
        
        //Adding chekbox verification of enrolled students
        $mform->addElement('advcheckbox', 'enrolled', '', get_string('enrolled', 'mgroup'), null, array(0, 1));
        $mform->addHelpButton('enrolled', 'enrolled', 'mgroup');
        if (!empty($recordsmbfi)) {
            $mform->hideIf('enrolled', 'datasource', 'neq', 0);
        }

        // Adding the standard "intro" and "introformat" fields.
        $this->standard_intro_elements();

        // Adding grouping parameters.
        $mform->addElement('header', 'groupingparameters', get_string('groupingparameters', 'mgroup'));

        // Adding the "numberofcharacteristics" field.
        $mform->addElement('text', 'numberofcharacteristics', get_string('numberofcharacteristics', 'mgroup'), array('size' => '64'));
        $mform->setType('numberofcharacteristics', PARAM_INT);
        if (empty($recordsmbfi)) {
            $mform->addRule('numberofcharacteristics', null, 'required', null, 'client');
        } else {
            $mform->hideIf('numberofcharacteristics', 'datasource', 'neq', 0);
        }
        $mform->addRule('numberofcharacteristics', null, 'numeric', 'extraruledata', 'client');
        $mform->addRule('numberofcharacteristics', null, 'nopunctuation', null, 'client');
        $mform->setDefault('numberofcharacteristics', 5);
        $mform->addHelpButton('numberofcharacteristics', 'numberofcharacteristics', 'mgroup');

        // Adding the "populationsize" field.
        $mform->addElement('text', 'populationsize', get_string('populationsize', 'mgroup'), array('size' => '64'));
        $mform->setType('populationsize', PARAM_INT);
        $mform->addRule('populationsize', null, 'required', null, 'client');
        $mform->addRule('populationsize', null, 'numeric', 'extraruledata', 'client');
        $mform->addRule('populationsize', null, 'nopunctuation', null, 'client');
        $mform->setDefault('populationsize', 50);
        $mform->addHelpButton('populationsize', 'populationsize', 'mgroup');

        // Adding the "selectionoperator" field.
        $mform->addElement('text', 'selectionoperator', get_string('selectionoperator', 'mgroup'), array('size' => '64'));
        $mform->setType('selectionoperator', PARAM_INT);
        $mform->addRule('selectionoperator', null, 'required', null, 'client');
        $mform->addRule('selectionoperator', null, 'numeric', 'extraruledata', 'client');
        $mform->setDefault('selectionoperator', 40);
        $mform->addHelpButton('selectionoperator', 'selectionoperator', 'mgroup');

        // Adding the "mutationoperator" field.
        $mform->addElement('text', 'mutationoperator', get_string('mutationoperator', 'mgroup'), array('size' => '64'));
        $mform->setType('mutationoperator', PARAM_FLOAT);
        $mform->addRule('mutationoperator', null, 'required', null, 'client');
        $mform->addRule('mutationoperator', null, 'numeric', 'extraruledata', 'client');
        $mform->setDefault('mutationoperator', 0.2);
        $mform->addHelpButton('mutationoperator', 'mutationoperator', 'mgroup');

        // Adding the "numberofgenerations" field.
        $mform->addElement('text', 'numberofgenerations', get_string('numberofgenerations', 'mgroup'), array('size' => '64'));
        $mform->setType('numberofgenerations', PARAM_INT);
        $mform->addRule('numberofgenerations', null, 'required', null, 'client');
        $mform->addRule('numberofgenerations', null, 'numeric', 'extraruledata', 'client');
        $mform->setDefault('numberofgenerations', 150);
        $mform->addHelpButton('numberofgenerations', 'numberofgenerations', 'mgroup');

        // Adding grouping settings.
        $mform->addElement('header', 'groupingsettings', get_string('groupingsettings', 'mgroup'));
        $groupingtype = array();
        $homogeneous = $mform->createElement('radio', 'groupingtype', '', get_string('homogeneous', 'mgroup'), 0, null);
        $heterogeneous = $mform->createElement('radio', 'groupingtype', '', get_string('heterogeneous', 'mgroup'), 1, null);
        $mixed = $mform->createElement('radio', 'groupingtype', '', get_string('mixed', 'mgroup'), 2, null);
        $groupingtype[] = $homogeneous;
        $groupingtype[] = $heterogeneous;
        $groupingtype[] = $mixed;
        $mform->addGroup($groupingtype, 'groupingtypear', get_string('groupingtypear', 'mgroup'), array('<br />'), false);
        $mform->addRule('groupingtypear', null, 'required', null, 'client');
        $mform->setDefault('groupingtype', 0);
        $mform->addHelpButton('groupingtypear', 'groupingtypear', 'mgroup');

        //Adding characteristic panel
        if ($mform->elementExists('numberofcharacteristics')) {
            $homocharacteristic = array();
            $hetecharacteristic = array();
            for ($i = 0; $i<50; $i++) {
                $homocharacteristic[] = $mform->createElement('radio', 'char'.($i+1), '', 'C'.($i+1), 0, null);
                $hetecharacteristic[] = $mform->createElement('radio', 'char'.($i+1), '', 'C'.($i+1), 1, null);
                $mform->setDefault('char'.($i+1), 0);
            }
            $mform->addGroup($homocharacteristic, 'grouphomocharacteristic', get_string('grouphomocharacteristic', 'mgroup'), array(''), false);
            $mform->addHelpButton('grouphomocharacteristic', 'grouphomocharacteristic', 'mgroup');
            $mform->hideIf('grouphomocharacteristic', 'groupingtype', 'neq', 2);
            $mform->addGroup($hetecharacteristic, 'grouphetecharacteristic', get_string('grouphetecharacteristic', 'mgroup'), array(''), false);
            $mform->addHelpButton('grouphetecharacteristic', 'grouphetecharacteristic', 'mgroup');
            $mform->hideIf('grouphetecharacteristic', 'groupingtype', 'neq', 2);
        }

        // Add standard elements.
        $this->standard_coursemodule_elements();

        // Add standard buttons.
        $this->add_action_buttons();
        
    }

    function validation($data, $files) {

        $errors = parent::validation($data, $files);

        if (array_key_exists('groupsize', $data)) {
            if (! $this->validation_groupsize((int)$data['groupsize'])) {
                $errors['groupsize'] = get_string('err_groupsize', 'mgroup');
            }
        }

        if (array_key_exists('numberofcharacteristics', $data)) {
            if (! $this->validation_groupsize((int)$data['numberofcharacteristics'])) {
                $errors['numberofcharacteristics'] = get_string('err_numberofcharacteristics', 'mgroup');
            }
        }

        if (array_key_exists('populationsize', $data)) {
            if (! $this->validation_groupsize((int)$data['populationsize'])) {
                $errors['populationsize'] = get_string('err_populationsize', 'mgroup');
            }
        }

        if (array_key_exists('selectionoperator', $data)) {
            if (! $this->validation_selectionoperator($data['selectionoperator'])) {
                $errors['selectionoperator'] = get_string('err_selectionoperator', 'mgroup');
            }
        }

        if (array_key_exists('mutationoperator', $data)) {
            if (! $this->validation_mutationoperator($data['mutationoperator'])) {
                $errors['mutationoperator'] = get_string('err_mutationoperator', 'mgroup');
            }
        }

        if (array_key_exists('numberofgenerations', $data)) {
            if (! $this->validation_groupsize((int)$data['numberofgenerations'])) {
                $errors['numberofgenerations'] = get_string('err_numberofgenerations', 'mgroup');
            }
        }

        return $errors;
    }

    
    function validation_groupsize($value) {
        return ($value <= 0 or ! is_int($value)) ? false : true;
    }

    function validation_selectionoperator($value) {
        return ($value <= 0 or $value > 100) ? false : true;
    }

    function validation_mutationoperator($value) {
        return ($value <= 0 or $value > 1) ? false : true;
    }
}
