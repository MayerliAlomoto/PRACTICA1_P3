<?php

declare(strict_types=1);

namespace App\Entities;

class Book extends Publication
{
    private string $isbn;
    private string $edition;
    private string $genre;

    public function __construct(
        int $id,
        string $title,
        string $description,
        \DateTime $publication_date,
        Author $author,
        string $isbn,
        string $edition,
        string $genre
    ) {
        parent::__construct($id, $title, $description, $publication_date, $author);
        $this->isbn = $isbn;
        $this->edition = $edition;
        $this->genre = $genre;
    }

    /* Getters */

    public function getIsbn(): string
    {
        return $this->isbn;
    }

    
    public function getEdition(): string
    {
        return $this->edition;
    }

   
    public function getGenre(): string
    {
        return $this->genre;
    }

    /* Setters */

    public function setIsbn(string $isbn): void
    {
        $this->isbn = $isbn;
    }

    
    public function setEdition(string $edition): void
    {
        $this->edition = $edition;
    }

    

    public function setGenre(string $genre): void
    {
        $this->genre = $genre;
    }
}
