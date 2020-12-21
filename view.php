<?php

use Iprbooks\Rki\Sdk\Client;
use Iprbooks\Rki\Sdk\Managers\IntegrationManager;
use Iprbooks\Rki\Sdk\Models\Book;
use Iprbooks\Rki\Sdk\Models\User;

require(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/lib.php');
require_once($CFG->dirroot . '/mod/rkibook/vendor/autoload.php');

$id = optional_param('id', 0, PARAM_INT);
$i = optional_param('i', 0, PARAM_INT);

if ($id) {
    $cm = get_coursemodule_from_id('rkibook', $id, 0, false, MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $moduleinstance = $DB->get_record('rkibook', array('id' => $cm->instance), '*', MUST_EXIST);
} else if ($i) {
    $moduleinstance = $DB->get_record('rkibook', array('id' => $n), '*', MUST_EXIST);
    $course = $DB->get_record('course', array('id' => $moduleinstance->course), '*', MUST_EXIST);
    $cm = get_coursemodule_from_instance('rkibook', $moduleinstance->id, $course->id, false, MUST_EXIST);
} else {
    print_error(get_string('missingidandcmid', 'mod_rkibook'));
}

require_login($course, true, $cm);

$modulecontext = context_module::instance($cm->id);


$PAGE->set_url('/mod/rkibook/view.php', array('id' => $cm->id));
$PAGE->set_title(format_string($moduleinstance->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($modulecontext);

$user_id = get_config('rkibooks', 'user_id');
$token = get_config('rkibooks', 'user_token');
$client = new Client($user_id, $token);

$integrationManager = new IntegrationManager($client);

$book = new Book($client);
$book->get($moduleinstance->rkibookid);
$autoLoginUrl = $integrationManager->generateAutoAuthUrl($USER->email, "", User::STUDENT, $book->getId());

$style = file_get_contents($CFG->dirroot . "/mod/rkibook/style/rkibook.css");

$template = "<style>" . $style . "</style>
			<div class=\"rki-item\" data-id=\"" . $book->getId() . "\">
                <div class=\"row\" style='padding: 10px'>
                    <div id=\"rki-item-image-" . $book->getId() . "\" class=\"col-sm-2 pub-image\">
                        <img src=\"" . $book->getImage() . "\" class=\"img-responsive thumbnail\" alt=\"\">
                        <a id=\"rki-item-url-" . $book->getId() . "\" href=\"" . $autoLoginUrl . '&goto=' . $book->getId() . "\"></a>
                    </div>
                    <div class=\"col-sm-8\">
                        <div id=\"rki-item-title-" . $book->getId() . "\"><strong>Название:</strong> " . $book->getTitle() . " </div>
                        <div id=\"rki-item-title_additional-" . $book->getId() . "\" ><strong>Альтернативное
                            название:</strong> " . $book->getTitleAdditional() . " </div>
                        <div id=\"rki-item-pubhouse-" . $book->getId() . "\"><strong>Издательство:</strong> " . $book->getPubhouse() . " </div>
                        <div id=\"rki-item-authors-" . $book->getId() . "\"><strong>Авторы:</strong> " . $book->getAuthors() . " </div>
                        <div id=\"rki-item-pubyear-" . $book->getId() . "\"><strong>Год издания:</strong> " . $book->getPubyear() . " </div>
                        <div id=\"rki-item-description-" . $book->getId() . "\" ><strong>Описание:</strong> " . $book->getDescription() . " </div>
                        <div id=\"rki-item-keywords-" . $book->getId() . "\" ><strong>Ключевые слова:</strong> " . $book->getKeywords() . " </div>
                        <div id=\"rki-item-pubtype-" . $book->getId() . "\" ><strong>Тип издания:</strong> " . $book->getPubtype() . " </div>
                        <br>
                        <a id=\"rki-item-detail-read\" class=\"btn btn-secondary\" target=\"_blank\" href=\"" . $autoLoginUrl . '&goto=' . $book->getId() . "\">Читать</a>
                    </div>
                </div>
            </div>";

echo $OUTPUT->header();

echo $template;

echo $OUTPUT->footer();
