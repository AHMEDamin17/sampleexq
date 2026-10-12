<?php
// Moodle Core Configuration Loader
require_once(__DIR__ . '/../moodle/config.php');
global $CFG, $USER, $DB;

// Default to active Moodle user if session is not started
if (empty($USER) || empty($USER->id)) {
    $USER = $DB->get_record('user', array('id' => 2));
}
?>
