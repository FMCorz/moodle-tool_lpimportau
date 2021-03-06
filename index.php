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
 * Page to edit a plan.
 *
 * @package    tool_lp
 * @copyright  2015 David Monllao
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir.'/adminlib.php');

$pagetitle = get_string('pluginname', 'tool_lpimportau');

$context = context_system::instance();

$url = new moodle_url("/admin/tool/lpimportau/index.php");
$PAGE->set_context($context);
$PAGE->set_url($url);
$PAGE->set_title($pagetitle);
$PAGE->set_pagelayout('admin');
$PAGE->set_heading($pagetitle);

$form = new \tool_lpimportau\form\import($url->out(false), array('persistent' => null, 'context' => $context));

if ($data = $form->get_data()) {
    require_sesskey();

    $xml = $form->get_file_content('importfile');
    $importer = new \tool_lpimportau\framework_importer($xml);

    $error = $importer->get_error();
    if ($error) {
        $form->set_import_error($error);
    } else {

        unset($data->importfile);

        $framework = \tool_lp\api::create_framework($data);

        if ($framework) {
            $importer->import_to_framework($framework);
            redirect(new moodle_url('continue.php', array('id' => $framework->get_id())));
            die();
        } else {
            $form->set_import_error(get_string('invalidpersistent', 'tool_lp'));
        }
    }
}

echo $OUTPUT->header();
echo $OUTPUT->heading($pagetitle);

$form->display();

echo $OUTPUT->footer();
