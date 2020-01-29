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
 * Library of interface functions and constants.
 *
 * @package     mod_mgroup
 * @copyright   2019 Carlos Ortega <carlosortega@udenar.edu.co>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Return if the plugin supports $feature.
 *
 * @param string $feature Constant representing the feature.
 * @return true | null True if the feature is supported, null otherwise.
 */
function mgroup_supports($feature) {
    switch ($feature) {
        case FEATURE_MOD_INTRO:
            return true;
        default:
            return null;
    }
}

/**
 * Saves a new instance of the mod_mgroup into the database.
 *
 * Given an object containing all the necessary data, (defined by the form
 * in mod_form.php) this function will create a new instance and return the id
 * number of the instance.
 *
 * @param object $mgroup An object from the form.
 * @param mod_mgroup_mod_form $mform The form.
 * @return int The id of the newly inserted record.
 */
function mgroup_add_instance($mgroup, $mform = null) {
    global $DB;

    if(!mgroup_save_file($mform)) {
        \core\notification::error(get_string('err_savefile', 'mgroup'));
        return false;
    }

    $mgroup->timecreated = time();

    $mgroup->id = $DB->insert_record('mgroup', $mgroup);

    return $mgroup->id;
}

/**
 * Updates an instance of the mod_mgroup in the database.
 *
 * Given an object containing all the necessary data (defined in mod_form.php),
 * this function will update an existing instance with new data.
 *
 * @param object $mgroup An object from the form in mod_form.php.
 * @param mod_mgroup_mod_form $mform The form.
 * @return bool True if successful, false otherwise.
 */
function mgroup_update_instance($mgroup, $mform = null) {
    global $DB;

    $mgroup->timemodified = time();
    $mgroup->id = $mgroup->instance;

    return $DB->update_record('mgroup', $mgroup);
    
}

/**
 * Removes an instance of the mod_mgroup from the database.
 *
 * @param int $id Id of the module instance.
 * @return bool True if successful, false on failure.
 */
function mgroup_delete_instance($id) {
    global $DB;

    $exists = $DB->get_record('mgroup', array('id' => $id));
    if (!$exists) {
        return false;
    }

    $DB->delete_records('mgroup', array('id' => $id));

    return true;
}

/**
 * Save a file of the mod_mgroup.
 *
 * @param mod_mgroup_mod_form $mform The form.
 * @return bool True if successful, false on failure.
 */
function mgroup_save_file($mform = null) {
    global $CFG;

    if(isset($mform)) {
        if($file = $mform->save_file('userfile', $CFG->dataroot.'/temp/filestorage/userfile.txt', true)) {
            return true;
        }
    }

    return false;
}