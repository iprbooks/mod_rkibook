<?php

defined('MOODLE_INTERNAL') || die;

function xmldb_rkibook_upgrade($oldversion) {
    global $CFG, $DB;

    $dbman = $DB->get_manager();

    return true;
}
