<?php

use Iprbooks\Rki\Sdk\Client;
use Iprbooks\Rki\Sdk\collections\BooksCollection;
use Iprbooks\Rki\Sdk\Managers\IntegrationManager;
use Iprbooks\Rki\Sdk\Models\Book;
use Iprbooks\Rki\Sdk\Models\User;

define('AJAX_SCRIPT', true);
require_once('../../config.php');
require_once($CFG->dirroot . '/mod/rkibook/vendor/autoload.php');

require_login();

$page = optional_param('page', 0, PARAM_INT);
$title = optional_param('title', "", PARAM_TEXT);
$id = optional_param('rkibookid', 0, PARAM_TEXT);

$clientId = get_config('rkibook', 'user_id');
$token = get_config('rkibook', 'user_token');

//$clientId = 187;
//$token = '5G[Usd=6]~F!b+L<a4I)Ya9S}Pb{McGX';

$content = "";
$details = "";

try {
    $client = new Client($clientId, $token);
} catch (Exception $e) {
    die();
}

$integrationManager = new IntegrationManager($client);
$autoLoginUrl = $integrationManager->generateAutoAuthUrl($USER->email, "", User::STUDENT);


if ($id > 0) {
    $book = new Book($client);
    $book->get($id);
    $details .= getDetails($book, $autoLoginUrl);
}

$booksCollection = new BooksCollection($client);
$booksCollection->setFilter(BooksCollection::TITLE, $title);
$booksCollection->setOffset($booksCollection->getLimit() * $page);
$booksCollection->get();
$message = $booksCollection->getMessage();

foreach ($booksCollection as $book) {
    $content .= getTemplate($book, $autoLoginUrl);
}

$content .= pagination($booksCollection->getTotalCount(), $page + 1);

if (mb_strlen($content) < 200) {
    $content = '<div style="font-size: 150%; text-align: center;">' . $message . '</div>' . $content;
}
echo json_encode(['page' => $page, 'html' => $content, 'details' => $details]);

function getTemplate(Book $book, $autoLoginUrl)
{
    return "<div class=\"rki-item\" data-id=\"" . $book->getBookId() . "\">
                    <div class=\"row\" style='padding: 10px'>
                        <div id=\"rki-item-image-" . $book->getBookId() . "\" class=\"col-sm-3 pub-image\">
                            <img src=\"" . "https://ros-edu.ru/" . $book->getImage() . "\" class=\"img-responsive thumbnail\" alt=\"\">
                            <a id=\"rki-item-url-" . $book->getBookId() . "\" href=\"" . $autoLoginUrl . '&goto=' . $book->getBookId() . "\"></a>
                        </div>
                        <div class=\"col-sm-8\">
                            <div id=\"rki-item-title-" . $book->getBookId() . "\"><strong>Название:</strong> " . $book->getTitle() . " </div>
                            <div id=\"rki-item-title_additional-" . $book->getBookId() . "\" hidden><strong>Альтернативное
                                название:</strong> " . $book->getLongTitle() . " </div>
                            <div id=\"rki-item-pubhouse-" . $book->getBookId() . "\"><strong>Издательство:</strong> " . $book->getPubhouses() . " </div>
                            <div id=\"rki-item-authors-" . $book->getBookId() . "\"><strong>Авторы:</strong> " . $book->getAuthors() . " </div>
                            <div id=\"rki-item-pubyear-" . $book->getBookId() . "\"><strong>Год издания:</strong> " . $book->getYear() . " </div>
                            <div id=\"rki-item-description-" . $book->getBookId() . "\" hidden><strong>Описание:</strong> " . $book->getDescription() . " </div>
                            <div id=\"rki-item-isbn-" . $book->getBookId() . "\" hidden><strong>ISBN:</strong> " . $book->getIsbn() . " </div>
                            <br>
                            <a  class=\"btn btn-secondary rkibook-select\" data-id=\"" . $book->getBookId() . "\">Выбрать</a>
                        </div>
                    </div>
                </div>";
}

function getDetails(Book $book, $autoLoginUrl)
{
    return "<div class=\"row\">
                <div id=\"rki-item-detail-image\" class=\"col-sm-5 pub-image\">
                            <img src=\"" . $book->getImage() . "\" class=\"img-responsive thumbnail\" alt=\"\">
                            <a id=\"rki-item-url-" . $book->getBookId() . "\" href=\"" . $autoLoginUrl . '&goto=' . $book->getBookId() . "\"></a>
                        </div>
                <div class=\"col-sm-7\">
                    <br>
                    <div id=\"rki-item-detail-title\"><strong>Название:</strong> " . $book->getTitle() . " </div>
                    <div id=\"rki-item-detail-title_additional\"></div>
                    <div id=\"rki-item-detail-pubhouse\"><strong>Издательство:</strong> " . $book->getPubhouse() . " </div>
                    <div id=\"rki-item-detail-authors\"><strong>Авторы:</strong> " . $book->getAuthors() . " </div>
                    <div id=\"rki-item-detail-pubtype\"><strong>Тип издания:</strong> " . $book->getPubtype() . " </div>
                    <div id=\"rki-item-detail-pubyear\"><strong>Год издания:</strong> " . $book->getPubyear() . " </div>
                    <br>
                    <a id=\"rki-item-detail-read\" style=\"display: none\" class=\"btn btn-secondary\" target=\"_blank\">Читать</a>
                </div>
            </div>
            <br>
            <div id=\"rki-details-fields\">
                <div id=\"rki-item-detail-description\"><strong>Описание:</strong> " . $book->getDescription() . " </div>
                <br>
                <div id=\"rki-item-detail-keywords\"><strong>Ключевые слова:</strong> " . $book->getKeywords() . " </div>
            </div>";
}

function pagination($count, $page)
{
    $output = '';
    $output .= "<nav aria-label=\"Страница\" class=\"pagination pagination-centered justify-content-center\"><ul class=\"mt-1 pagination \">";
    $pages = ceil($count / 10);


    if ($pages > 1) {

        if ($page > 1) {
            $output .= "<li class=\"page-item\"><a data-page=\"" . ($page - 2) . "\" class=\"page-link rki-page\" ><span>«</span></a></li>";
        }
        if (($page - 3) > 0) {
            $output .= "<li class=\"page-item \"><a data-page=\"0\" class=\"page-link rki-page\">1</a></li>";
        }
        if (($page - 3) > 1) {
            $output .= "<li class=\"page-item disabled\"><span class=\"page-link rki-page\">...</span></li>";
        }


        for ($i = ($page - 2); $i <= ($page + 2); $i++) {
            if ($i < 1) continue;
            if ($i > $pages) break;
            if ($page == $i)
                $output .= "<li class=\"page-item active\"><a data-page=\"" . ($i - 1) . "\" class=\"page-link rki-page\" >" . $i . "</a ></li > ";
            else
                $output .= "<li class=\"page-item \"><a data-page=\"" . ($i - 1) . "\" class=\"page-link rki-page\">" . $i . "</a></li>";
        }


        if (($pages - ($page + 2)) > 1) {
            $output .= "<li class=\"page-item disabled\"><span class=\"page-link rki-page\">...</span></li>";
        }
        if (($pages - ($page + 2)) > 0) {
            if ($page == $pages)
                $output .= "<li class=\"page-item active\"><a data-page=\"" . ($pages - 1) . "\" class=\"page-link rki-page\" >" . $pages . "</a ></li > ";
            else
                $output .= "<li class=\"page-item \"><a data-page=\"" . ($pages - 1) . "\" class=\"page-link rki-page\">" . $pages . "</a></li>";
        }
        if ($page < $pages) {
            $output .= "<li class=\"page-item\"><a data-page=\"" . $page . "\" class=\"page-link rki-page\"><span>»</span></a></li>";
        }

    }

    $output .= "</ul></nav>";
    return $output;
}

die();