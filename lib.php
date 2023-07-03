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
 * @copyright   2019 Carlos Ortega <carlosortega@udenar.edu.co> Oscar Revelo Sánchez <orevelo@udenar.edu.co> Jesús Insuasti Portilla <insuasty@udenar.edu.co>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if(isset($CFG->mgroup_javaserver)) {
    require_once($CFG->mgroup_javaserver);
}

//The content of the text file to be used in later functions
global $MGROUP_CONTENT_FILE;
$MGROUP_CONTENT_FILE = null;

/**
 * Return if the plugin supports $feature.
 *
 * @param string $feature Constant representing the feature.
 * @return true | null True if the feature is supported, null otherwise.
 */
function mgroup_supports($feature) {
    switch ($feature) {
        case FEATURE_GROUPS:
            return true;
        case FEATURE_GROUPINGS:
            return true;
        case FEATURE_MOD_INTRO:
            return true;
        case FEATURE_BACKUP_MOODLE2:
            return true;
        case FEATURE_COMPLETION_TRACKS_VIEWS:
            return true;
        case FEATURE_GRADE_HAS_GRADE:
            return false;
        case FEATURE_GRADE_OUTCOMES:
            return true;
        case FEATURE_SHOW_DESCRIPTION:
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
    global $DB, $CFG;

    $path = $CFG->dataroot.'/temp/filestorage/mgroupuserfile_'.(time() + rand()).'.csv';
    $characteristics = $mgroup->numberofcharacteristics;
    $datasource = '0';
    
    if (isset($mgroup->datasource)) {
        $datasource = $mgroup->datasource;
    }

    if ($datasource == '0') {
        if (!mgroup_save_file($path, $mform)) {
            print_error('error', '', new moodle_url('/course/view.php', array('id' => $mgroup->course)));
        }
    
        if (!mgroup_check_file($characteristics, $path)) {
            mgroup_delete_file($path);
            print_error('error', '', new moodle_url('/course/view.php', array('id' => $mgroup->course)));
        }
    
        if ($mgroup->enrolled == '0') {
            if (!mgroup_check_users_in_course($mgroup->course)) {
                mgroup_delete_file($path);
                print_error('error', '', new moodle_url('/course/view.php', array('id' => $mgroup->course)));
            }
        }
    } else {
        $mgroup->numberofcharacteristics = 5;
        $dimensionvalues = $DB->get_records('mbfi_characteristic_values', array('mbfiid' => $mgroup->mbfi), '', 'userid,username,fullname,email,extraversion,agreeableness,conscientiousness,neuroticism,openness');
        if (!mgroup_create_file($path, $dimensionvalues)) {
            print_error('error', '', new moodle_url('/course/view.php', array('id' => $mgroup->course)));
        }
        if (empty(mgroup_read_file($path))) {
            mgroup_delete_file($path);
            print_error('error', '', new moodle_url('/course/view.php', array('id' => $mgroup->course)));
        }
    }

    $results = mgroup_form_groups($mgroup, $path);

    if (isset($results)) {
        $mgroup->timecreated = time();
        $mgroup->id = $DB->insert_record('mgroup', $mgroup);

        foreach ($results as $group => $individuals) {
            foreach ($individuals as $username) {
                $dataindividual = new stdClass();
                $dataindividual->mgroupid = $mgroup->id;
                $dataindividual->workgroup = ($group + 1);
                $userid = $DB->get_field('user', 'id', array('username' => $username));
                if (isset($userid)) {
                    $dataindividual->userid = $userid;
                }
                $dataindividual->username = (string)$username;
                $dataindividual->fullname = mgroup_searching_individual_in_content_file($username);
                $dataindividual->timecreated = time();
                $DB->insert_record('mgroup_individuals', $dataindividual);
            }
        }
        
        return $mgroup->id;
    }
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
    global $DB, $CFG;

    $path = $CFG->dataroot.'/temp/filestorage/userfile_'.(time() + rand()).'.csv';
    $characteristics = $mgroup->numberofcharacteristics;
    $datasource = '0';
    
    if (isset($mgroup->datasource)) {
        $datasource = $mgroup->datasource;
    }

    if ($datasource == '0') {
        if (!mgroup_save_file($path, $mform)) {
            print_error('error', '', new moodle_url('/course/view.php', array('id' => $mgroup->course)));
        }
    
        if (!mgroup_check_file($characteristics, $path)) {
            mgroup_delete_file($path);
            print_error('error', '', new moodle_url('/course/view.php', array('id' => $mgroup->course)));
        }
    
        if ($mgroup->enrolled == '0') {
            if (!mgroup_check_users_in_course($mgroup->course)) {
                mgroup_delete_file($path);
                print_error('error', '', new moodle_url('/course/view.php', array('id' => $mgroup->course)));
            }
        }
    } else {
        $mgroup->numberofcharacteristics = 5;
        $dimensionvalues = $DB->get_records('mbfi_characteristic_values', array('mbfiid' => $mgroup->mbfi), '', 'userid,username,fullname,email,extraversion,agreeableness,conscientiousness,neuroticism,openness');
        if (!mgroup_create_file($path, $dimensionvalues)) {
            print_error('error', '', new moodle_url('/course/view.php', array('id' => $mgroup->course)));
        }
        if (empty(mgroup_read_file($path))) {
            mgroup_delete_file($path);
            print_error('error', '', new moodle_url('/course/view.php', array('id' => $mgroup->course)));
        }
    }

    $results = mgroup_form_groups($mgroup, $path);

    if (isset($results)) {
        $mgroupindividuals = $DB->get_records('mgroup_individuals', array('mgroupid' => $mgroup->instance));
        foreach ($mgroupindividuals as $individual) {
            $DB->delete_records('mgroup_individuals', array('id' => $individual->id));
        }
        foreach ($results as $group => $individuals) {
            foreach ($individuals as $username) {
                $dataindividual = new stdClass();
                $dataindividual->mgroupid = $mgroup->instance;
                $dataindividual->workgroup = ($group + 1);
                $userid = $DB->get_field('user', 'id', array('username' => $username));
                if (isset($userid)) {
                    $dataindividual->userid = $userid;
                }
                $dataindividual->username = (string)$username;
                $dataindividual->fullname = mgroup_searching_individual_in_content_file($username);
                $dataindividual->timecreated = time();
                $DB->insert_record('mgroup_individuals', $dataindividual);
            }
        }
        $mgroup->timemodified = time();
        $mgroup->id = $mgroup->instance;

        return $DB->update_record('mgroup', $mgroup);
    }
}

/**
 * Removes an instance of the mod_mgroup from the database.
 *
 * @param int $id Id of the module instance.
 * @return bool True if successful, false on failure.
 */
function mgroup_delete_instance($id) {
    global $DB;

    if (!$DB->get_record('mgroup', array('id' => $id))) {
        return false;
    }

    $result = true;

    // Delete any dependent records here.

    if (!$DB->delete_records('mgroup', array('id' => $id))) {
        $result = false;
    }
    if (!$DB->delete_records('mgroup_individuals', array('mgroupid' => $id))) {
        $result = false;
    }

    return $result;
}

/**
 * Search a individual in the content file.
 *
 * @param string $username Username of the individual.
 * @return string String with individual's fullname, null on failure.
 */
function mgroup_searching_individual_in_content_file($username) {
    global $MGROUP_CONTENT_FILE;

    foreach ($MGROUP_CONTENT_FILE as $content) {
        if (in_array((string)$username, $content, true)) {
            if (mb_detect_encoding($content[1], 'UTF-8', true) != 'UTF-8') {
                return (utf8_encode($content[1]));
            } else {
                return ($content[1]);
            }
        }
    }

    return null;
}

/**
 * Save a text file of the mod_mgroup.
 *
 * @param string $path Text file path.
 * @param mod_mgroup_mod_form $mform The form.
 * @return bool True if successful, false on failure.
 */
function mgroup_save_file($path, $mform) {

    if (isset($path, $mform)) {
        if ($mform->save_file('userfile', $path, true)) {
            return true;
        }
    }

    \core\notification::error(get_string('err_savefile', 'mgroup'));
    return false;
}

/**
 * Create a text file of the mod_mgroup.
 *
 * @param string $path Text file path.
 * @param object $dimensionvalues Object with values of each dimension.
 * @return bool True if successful, false on failure.
 */
function mgroup_create_file($path, $dimensionvalues) {
    global $DB;

    if (isset($path, $dimensionvalues)) {
        $data = array();
        foreach ($dimensionvalues as $values) {
            if ($data_user = $DB->get_record('user', array('id' => $values->userid), 'username, firstname, lastname, email')) {
                $fullname = $data_user->firstname.' '.$data_user->lastname;
                $values = (array)$values;
                array_shift($values);
                array_merge($values, array('username' => $data_user->username, 'fullname' => $fullname, 'email' => $data_user->email));
            } else {
                $values = (array)$values;
                array_shift($values);
            }
            $data[] = implode(',', $values);
        } 
        if (file_put_contents($path, implode("\n", $data)) !== false) {
            return true;
        }
    }

    \core\notification::error(get_string('err_createfile', 'mgroup'));
    return false;
}

/**
 * Delete a text file of the mod_mgroup.
 *
 * @param string $path Text file path.
 * @return bool True if successful, false on failure.
 */
function mgroup_delete_file($path) {

    if (isset($path)) {
        if (unlink($path)) {
            return true;
        }
    }

    \core\notification::error(get_string('err_deletefile', 'mgroup'));
    return false;
}

/**
 * Read a text file of the mod_mgroup.
 *
 * @param string $path Text file path.
 * @return object Array if successful, null on failure.
 */
function mgroup_read_file($path) {
    global $MGROUP_CONTENT_FILE;

    $parameters = array();

    if (isset($path)) {
        if ($content = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES)) {
            foreach ($content as $line) {
                $parameters[] = explode(',', $line);
            }
            $MGROUP_CONTENT_FILE = $parameters;
            return $parameters;
        }
    }

    \core\notification::error(get_string('err_readfile', 'mgroup'));
    return null;
}

/**
 * Check a text file of the mod_mgroup.
 *
 * @param int $characteristics Number of characteristics.
 * @return boolean True if successful, false on failure.
 */
function mgroup_check_file($characteristics, $path) {

    $content = mgroup_read_file($path);

    if (isset($characteristics, $content)) {
        $errrors = false;
        foreach ($content as $line_number => $line) {
            if (!mgroup_check_parameters($line, $characteristics)) {
                $errrors = true;
                \core\notification::error(get_string('err_checkparameters', 'mgroup', array('number' => $line_number + 1)));
            }
        }
        if (!$errrors) {
            return true;
        }
    }

    \core\notification::error(get_string('err_checkfile', 'mgroup'));
    return false;
}

/**
 * Check the parameters of each individual of the mod_mgroup.
 *
 * @param object $parameters Array with parameters.
 * @param int $characteristics Number of characteristics.
 * @return boolean True if successful, false on failure.
 */
function mgroup_check_parameters($parameters, $characteristics) {

    if (isset($parameters, $characteristics)) {
        if ($characteristics != (count($parameters) - 3)) {
            return false;
        }
        foreach ($parameters as $parameter) {
            if (is_null($parameter)) {
                return false;
            }
        }
    }

    return true;
}

/**
 * Check users enrolled in the course of the mod_mgroup.
 *
 * @param string $course Course id.
 * @return boolean True if successful, false on failure.
 */
function mgroup_check_users_in_course($course) {
    global $DB, $MGROUP_CONTENT_FILE;

    $users = search_users($course, NULL, NULL);

    if(isset($course) && (!empty($users))) {
        $errors = false;
        foreach ($MGROUP_CONTENT_FILE as $user) {
            list($username, $fullname) = $user;
            $userid = $DB->get_field('user', 'id', array('username' => $username));
            if (!array_key_exists($userid, $users)) {
                $errors = true;
                \core\notification::error(get_string('err_user', 'mgroup', array('name' => $fullname)));
            }
        }
        if (!$errors) {
            return true;
        }
    }
    \core\notification::error(get_string('err_checkusers', 'mgroup'));
    return false;
}

/**
 * Group conformation in the course of the mod_mgroup.
 *
 * @param object $mgroup An object from the form.
 * @param string $path Text field path.
 * @return object Array with results if successful, null on failure.
 */
function mgroup_form_groups($mgroup, $path) {
    $characteristics = $mgroup->numberofcharacteristics;
    $groupsize = $mgroup->groupsize;
    $populationsize = $mgroup->populationsize;
    $selectionoperator = $mgroup->selectionoperator;
    $mutationoperator = $mgroup->mutationoperator;
    $maxgenerations = $mgroup->numberofgenerations;
    $groupingtype = (int)$mgroup->groupingtype;
    $hetecharacteristics = null;
    $homocharacteristics = null;
    $results = null;
    if ($groupingtype == 2) {
        $hetecharacteristics = array();
        $homocharacteristics = array();
        for ($i=0; $i < $characteristics; $i++) {
            $char = 'char'.($i+1);
            $characteristic = $mgroup->$char;
            if ($characteristic == '1') {
                $hetecharacteristics[] = $i;
            } else {
                $homocharacteristics[] = $i;
            }
        }
    }

    try {
        $data = mgroup_get_data($path, $groupsize, $groupingtype, $hetecharacteristics, $homocharacteristics);
        
        $generations = 0;
        $ga = new TeamB_Pack\GA($data, $populationsize, $selectionoperator, $mutationoperator);
        $ga->initialPopulation();
        if (java_values($data->getGroupingType()) == 0) {
            $ga->checkFitnessMinimize();
        }
        $ga->evaluation();
        while ($generations < $maxgenerations) {
            $ga->tournDeterministicSelection(2);
            $ga->reproduction();
            $ga->mutation();
            if (java_values($data->getGroupingType()) == 0) {
                $ga->checkFitnessMinimize();
            }
            $ga->evaluation();
            $generations++;
        }
    
        $results = java_values($ga->getPopulation()[$ga->getBestPosition()]->getGenes());
    
        if (!mgroup_delete_file($path)) {
            print_error('error', '', new moodle_url('/course/view.php', array('id' => $mgroup->course)));
        }
    } catch (Exception $e) {
        \core\notification::error($e->getMessage());
    } finally {
        return $results;
    }
}

/**
 * Get a data class object from TeamB.
 *
 * @param string $path Text field path.
 * @param int $groupsize Group size
 * @param int $groupingtype Grouping type
 * @param object $hetecharacteristics Array with heterogeneous characteristics
 * @param object $homocharacteristics Array with homogeneous characteristics
 * @return object TeamB data class instance.
 */
function mgroup_get_data($path, $groupsize, $groupingtype, $hetecharacteristics, $homocharacteristics) {
    $data = null;
    try {
        $data = new TeamB_Pack\Data($path, $groupsize, $groupingtype, $hetecharacteristics, $homocharacteristics);
        return $data;
    } catch (Exception $e) {
        $groupsizeexception = new TeamB_Pack\GroupSizeException($e->getCause());
        switch (java_values($groupsizeexception->getCode()->toString())) {
            case 'invalid_size':
                throw new Exception(get_string('invalid_size', 'mgroup'));
                break;
            case 'invalid_bound_even':
                throw new Exception(get_string('invalid_bound_even', 'mgroup'));
                break;
            case 'invalid_bound_odd':
                throw new Exception(get_string('invalid_bound_odd', 'mgroup'));
                break;
            default:
                throw new Exception(get_string('data_exception', 'mgroup'));
                break;
        }
    }
}