<?php

namespace Iprbooks\Rki\Sdk\Models;

use Iprbooks\Rki\Sdk\Client;
use Iprbooks\Rki\Sdk\Core\Model;

class Book extends Model
{

    private $apiMethod = '/books/{id}/get';


    public function __construct(Client $client, $response = null)
    {
        parent::__construct($client, $response);
        return $this;
    }

    protected function getApiMethod()
    {
        return $this->apiMethod;
    }


    public function getBookId()
    {
        return $this->getValue('book_id');
    }

    public function getHaveAudio()
    {
        return $this->getValue('have_audio');
    }

    public function isAudioPublication()
    {
        return $this->getValue('is_audio_publication');
    }

    public function getTitle()
    {
        return $this->getValue('title');
    }

    public function getAuthors()
    {
        return $this->getValue('authors');
    }

    public function getLongTitle()
    {
        return $this->getValue('longtitle');
    }

    public function getPubHouses()
    {
        return $this->getValue('pubhouses');
    }

    public function getYear()
    {
        return $this->getValue('year');
    }

    public function getDescription()
    {
        return $this->getValue('description');
    }

    public function getIsbn()
    {
        return $this->getValue('isbn');
    }

    public function getSearchindex()
    {
        return $this->getValue('searchindex');
    }

    public function getImage()
    {
        return $this->getValue('image');
    }

}