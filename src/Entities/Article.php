<?php

declare(strict_types=1);

namespace App\Entities;

class Article extends Publication
{
    private string $doi;
    private string $journal;

    public function __construct(
        int $id,
        string $title,
        string $description,
        \DateTime $publication_date,
        Author $author,
        string $doi,
        string $journal
    ) {
        parent::__construct($id, $title, $description, $publication_date, $author);
        $this->doi = $doi;
        $this->journal = $journal;
    }

    /*Getters*/

    public function getDoi(): string
    {
        return $this->doi;
    }
    public function getJournal(): string
    {
        return $this->journal;
    }

    /*Setters*/

    public function setDoi(string $doi): void
    {
        $this->doi = $doi;
    }
    public function setJournal(string $journal): void
    {
        $this->journal = $journal;
    }
}
