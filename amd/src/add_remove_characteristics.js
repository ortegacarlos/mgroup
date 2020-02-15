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
 * Add or remove checkbox according to the number of characteristics.
 *
 * @module      mod_mgroup/add_remove_characteristics
 * @copyright   2019 Carlos Ortega <carlosortega@udenar.edu.co>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery'], function($) {
    return {
        init: function () {
            $('#id_numberofcharacteristics').on('change', function(e) {
                e.preventDefault();
                //$('#attemptsform').find('input:checkbox').prop('checked', $(this).data('selectInfo'));
                alert($('#fgroup_id_grouphomocharacteristic').html());
            });
        }
    };
});
