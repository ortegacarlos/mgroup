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
 * Prints an instance of mod_mgroup.
 *
 * @package     mod_mgroup
 * @copyright   2019 Carlos Ortega <carlosortega@udenar.edu.co> Oscar Revelo Sánchez <orevelo@udenar.edu.co> Jesús Insuasti Portilla <insuasty@udenar.edu.co>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__.'/../../config.php');
require_once(__DIR__.'/lib.php');

// Course_module ID, or
$id = optional_param('id', 0, PARAM_INT);
$download = optional_param('download', '', PARAM_ALPHA);

// ... module instance id.
$m  = optional_param('m', 0, PARAM_INT);

if ($id) {
    $cm             = get_coursemodule_from_id('mgroup', $id, 0, false, MUST_EXIST);
    $course         = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $moduleinstance = $DB->get_record('mgroup', array('id' => $cm->instance), '*', MUST_EXIST);
} else if ($m) {
    $moduleinstance = $DB->get_record('mgroup', array('id' => $m), '*', MUST_EXIST);
    $course         = $DB->get_record('course', array('id' => $moduleinstance->course), '*', MUST_EXIST);
    $cm             = get_coursemodule_from_instance('mgroup', $moduleinstance->id, $course->id, false, MUST_EXIST);
} else {
    print_error(get_string('missingidandcmid', 'mgroup'));
}

require_login($course, true, $cm);

$modulecontext = context_module::instance($cm->id);

require_capability('mod/mgroup:view', $modulecontext);

$event = \mod_mgroup\event\course_module_viewed::create(array(
    'objectid' => $moduleinstance->id,
    'context' => $modulecontext
));
$event->add_record_snapshot('course', $course);
$event->add_record_snapshot('mgroup', $moduleinstance);
$event->trigger();

$url = new moodle_url('/mod/mgroup/view.php', array('id' => $cm->id));
if ($download !== '') {
    $url->param('download', $download);
}

$PAGE->set_url($url);

$user = null;
$groupsize = $DB->get_field('mgroup', 'groupsize', array('id' => $moduleinstance->id));
$individuals = array_chunk($DB->get_records('mgroup_individuals', array('mgroupid' => $moduleinstance->id)), (int)$groupsize);

if ($download == '') {
    $PAGE->set_title(format_string($moduleinstance->name));
    $PAGE->set_heading(format_string($course->fullname));
    $PAGE->set_context($modulecontext);

    echo $OUTPUT->header();
}

// Print PDF file
if ($download == 'pdf' && has_capability('mod/mgroup:downloaddata', $modulecontext)) {
    require_once($CFG->libdir . '/pdflib.php');

    $mgroupname = $DB->get_field('mgroup', 'name', array('id' => $moduleinstance->id));
    $filename = clean_filename("$course->shortname " . strip_tags(format_string($mgroupname, true))) . '.pdf';
    $date = gmdate("d\-M\-Y H:i:s", time());
    $teacher = 'teacher';
    $fontfamily = 'helvetica';

    $coursecontacts = new core_course_list_element($course);
    if ($coursecontacts->has_course_contacts()) {
        foreach ($coursecontacts->get_course_contacts() as $coursecontact) {
            $rolenames = array_map(function ($role) {
                return $role->displayname;
            }, $coursecontact['roles']);
            //$name = implode(', ', $rolenames).': '.$coursecontact['username'];
            $teachername = $coursecontact['username'];
        }
    }

    $pdf = new pdf();

    $pdf->SetTitle(get_string('title_file', 'mgroup'));
    $pdf->SetAuthor($teacher);
    $pdf->SetCreator($SITE->fullname);
    $pdf->SetKeywords(get_string('keywords_file', 'mgroup'));
    $pdf->SetSubject(get_string('subject_file', 'mgroup'));
    $pdf->SetMargins(25, 50);

    // Print Header file
    $pdf->setPrintHeader(true);
    $pdf->setHeaderMargin(20);
    $pdf->setHeaderFont(array($fontfamily, 'B', 12));
    $pdf->setHeaderData('mod/mgroup/pix/icon_header_file.png', 22, $SITE->fullname, "$CFG->wwwroot \n" . get_string('subject_file', 'mgroup'));

    // Print Footer file
    $pdf->setPrintFooter(true);
    $pdf->setFooterMargin(20);
    $pdf->setFooterFont(array($fontfamily, '', 10));

    $pdf->AddPage();

    $pdf->SetTextColor(255, 255, 255);
    $pdf->SetFillColor(10, 73, 250);
    $pdf->SetFont($fontfamily, 'B', 26);
    $pdf->Cell(0, 0, get_string('list_file', 'mgroup'), 0, 1, 'C', 1);

    $pdf->SetFont($fontfamily, '', 12);
    $pdf->Ln(6);
    $pdf->SetTextColor(0, 0, 0);
    $pdf->SetFillColor(222, 226, 230);

    $pdf->SetFont($fontfamily, 'B', 15);
    $pdf->Cell(0, 0, get_string('general_information_file', 'mgroup'), 0, 1, 'L');
    $pdf->SetFont($fontfamily, '', 12);
    $generalinformation = '<strong>'.get_string('teacher_course', 'mgroup').'</strong>';
    $generalinformation .= $teacher.'<br />';
    $generalinformation .= '<strong>'.get_string('course_file', 'mgroup').'</strong>';
    $generalinformation .= $course->fullname.'<br />';
    $generalinformation .= '<strong>'.get_string('date_file', 'mgroup').'</strong>';
    $generalinformation .= $date.'<br />';
    $pdf->writeHTML($generalinformation);
    $pdf->Ln(6);

    if (isset($individuals)) {
        foreach ($individuals as $group => $individual) {
            $pdf->SetMargins(25, 0);
            $pdf->Ln(1);
            $pdf->SetFont($fontfamily, 'B', 20);
            $pdf->Cell(0, 0, get_string('group', 'mgroup') . ' ' . ($group + 1), 0, 1, 'L');
            $pdf->Ln(1);
            $pdf->writeHTML('<hr>');
            $fill = true;
            $pdf->SetFont($fontfamily, '', 12);
            $index = 1;
            foreach ($individual as $values) {
                $pdf->SetMargins(35, 0);
                $pdf->Ln(1);
                if ($values->username != '0') {
                    if ($fill) {
                        $pdf->Cell(0, 0, $index++ . '. ' . $values->fullname, 'B', 1, 'L', $fill);
                        $fill = false;
                    } else {
                        $pdf->Cell(0, 0, $index++ . '. ' . $values->fullname, 'B', 1, 'L', $fill);
                        $fill = true;
                    }
                }
            }
            $pdf->Ln(6);
        }
    }

    $pdf->Output($filename);
    exit();
}

if (data_submitted() && confirm_sesskey() && has_capability('mod(mgroup:downloaddata', $modulecontext)) {
    redirect("view.php?id=$cm->id");
}

echo '<div class="clearer"></div>';

if (!empty($individuals)) {
    $downloadoptions = array();
    $options = array();
    $options['id'] = "$cm->id";
    $options['download'] = 'pdf';
    $button = $OUTPUT->single_button(new moodle_url('view.php', $options), get_string('downloadpdf', 'mgroup'));
    $downloadoptions[] = html_writer::tag('div', $button, array('class' => 'align-self-center'));
    echo html_writer::tag('div', implode('', $downloadoptions), array('class' => 'row justify-content-center'));
}

echo '<div class="clearer"></div>';

if (isset($individuals)) {
    foreach ($individuals as $group => $individual) {
        echo $OUTPUT->container_start('group', 'group');
        echo '<h3>'.get_string('group', 'mgroup').' '.($group + 1).'</h3><hr>';
        foreach ($individual as $values) {
            if ($values->username != '0') {
                $link = true;
                $user = $DB->get_record('user', array('id' => $values->userid));
                if ($values->userid == '0') {
                    $user = $DB->get_record('user', array('id' => 1));
                    $user->firstname = $values->fullname;
                    $user->lastname = '';
                    $link = false;
                }
                echo $OUTPUT->box_start('individual', 'individual');
                echo $OUTPUT->user_picture($user, array('courseid' => $course->id, 'size' => 50, 'popup' => true, 'includefullname' => true, 'link' => $link));
                echo $OUTPUT->box_end();
            }
        }
        echo $OUTPUT->container_end();
    }
}

echo $OUTPUT->footer();
