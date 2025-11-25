<?php
namespace App\Mappers;

use Core\Database;
use App\Models\Student;
use PDO;

class StudentMapper {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = Database::getConnection();
    }

    public function save(Student $student) {
        $sql = "INSERT INTO students (name, email, password_hash) VALUES (:name, :email, :pass)";
        $stmt = $this->pdo->prepare($sql);
        
        $stmt->execute([
            ':name'  => $student->name,
            ':email' => $student->email,
            ':pass'  => $student->password_hash 
        ]);

        $id = (int)$this->pdo->lastInsertId();
        $student->hydrateId($id); 
    }

    public function login(string $email, string $password): ?Student {
        $stmt = $this->pdo->prepare("SELECT * FROM students WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $data = $stmt->fetch();

        if ($data) {
            $student = new Student($data['name'], $data['email'], 'dummy_pass');
            $reflector = new \ReflectionClass($student);
            $prop = $reflector->getProperty('password_hash');
            $prop->setAccessible(true);
            $prop->setValue($student, $data['password_hash']);
            
            $student->hydrateId($data['id']);

            if (password_verify($password, $data['password_hash'])) {
                return $student;
            }
        }
        return null;
    }
}