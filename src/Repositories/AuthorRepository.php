<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\RepositoryInterface;
use App\Config\Database;
use App\Entities\Author;
use PDO;

class AuthorRepository implements RepositoryInterface
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function findAll(): array
    {
        $stmt = $this->db->query('SELECT * FROM author'); 
        $authors = [];
        while ($row = $stmt->fetch()) {
            $authors[] = $this->hydrate($row);
        }
        return $authors;
    }

    public function findById(int $id): ?object
    {
        $stmt = $this->db->prepare('SELECT * FROM author WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ? $this->hydrate($row) : null;
    }

    public function create(object $entity): bool
    {
        if (!$entity instanceof Author) {
            throw new \InvalidArgumentException('Expected instance of Author');
        }

        $sql = 'INSERT INTO author (first_name, last_name, username, email, password, orcid, affiliation) 
                VALUES (:first_name, :last_name, :username, :email, :password, :orcid, :affiliation)';
        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':first_name' => $entity->getFirstName(),
            ':last_name' => $entity->getLastName(),
            ':username' => $entity->getUsername(),
            ':email' => $entity->getEmail(),
            ':password' => password_hash($entity->getPassword(), PASSWORD_BCRYPT),
            ':orcid' => $entity->getOrcid(),
            ':affiliation' => $entity->getAffiliation()
        ]);

    }

    public function update(object $entity): bool
    {
        if (!$entity instanceof Author) {
            throw new \InvalidArgumentException('Expected instance of Author');
        }

        $sql = 'UPDATE author SET first_name = :first_name, last_name = :last_name, username = :username, 
                email = :email, password = :password, orcid = :orcid, affiliation = :affiliation 
                WHERE id = :id';
        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':id' => $entity->getId(),
            ':first_name' => $entity->getFirstName(),
            ':last_name' => $entity->getLastName(),
            ':username' => $entity->getUsername(),
            ':email' => $entity->getEmail(),
            ':password' => password_hash($entity->getPassword(), PASSWORD_BCRYPT),
            ':orcid' => $entity->getOrcid(),
            ':affiliation' => $entity->getAffiliation()
        ]);
    }

    public function delete(int $id): bool
    {
        $sql = 'DELETE FROM author WHERE id = :id';
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    //Convierte fila SQL a entidad Author
    private function hydrate(array $row): Author
    {
        $author = new Author(
            (int)$row['id'],
            $row['first_name'],
            $row['last_name'],
            $row['username'],
            $row['email'],
            'temporal', 
            $row['orcid'],
            $row['affiliation'] ?? ''
         );
        //REEMPLAZAR HASH SIN REGENERAR
        $ref = new \ReflectionClass($author);
        $property = $ref->getProperty('password');
        $property->setAccessible(true);
        $property->setValue($author, $row['password']);


        return $author;       
    }
}