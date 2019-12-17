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
 * @package     mod_mpgroup
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
function mpgroup_supports($feature) {
    switch ($feature) {
        case FEATURE_MOD_INTRO:
            return true;
        default:
            return null;
    }
}

/**
 * Saves a new instance of the mod_mpgroup into the database.
 *
 * Given an object containing all the necessary data, (defined by the form
 * in mod_form.php) this function will create a new instance and return the id
 * number of the instance.
 *
 * @param object $mpgroup An object from the form.
 * @param mod_mpgroup_mod_form $mform The form.
 * @return int The id of the newly inserted record.
 */
function mpgroup_add_instance($mpgroup, $mform = null) {
    global $DB, $CFG;

    if(isset($mform)) {
        $file = $mform->save_file('userfile', $CFG->dirroot.'/mod/mpgroup/files/userfile.txt', true);
    }

    $mpgroup->timecreated = time();

    $mpgroup->id = $DB->insert_record('mpgroup', $mpgroup);

    return $mpgroup->id;
}

/**
 * Updates an instance of the mod_mpgroup in the database.
 *
 * Given an object containing all the necessary data (defined in mod_form.php),
 * this function will update an existing instance with new data.
 *
 * @param object $mpgroup An object from the form in mod_form.php.
 * @param mod_mpgroup_mod_form $mform The form.
 * @return bool True if successful, false otherwise.
 */
function mpgroup_update_instance($mpgroup, $mform = null) {
    global $DB;

    $mpgroup->timemodified = time();
    $mpgroup->id = $mpgroup->instance;

    return $DB->update_record('mpgroup', $mpgroup);
}

/**
 * Removes an instance of the mod_mpgroup from the database.
 *
 * @param int $id Id of the module instance.
 * @return bool True if successful, false on failure.
 */
function mpgroup_delete_instance($id) {
    global $DB;

    $exists = $DB->get_record('mpgroup', array('id' => $id));
    if (!$exists) {
        return false;
    }

    $DB->delete_records('mpgroup', array('id' => $id));

    return true;
}
