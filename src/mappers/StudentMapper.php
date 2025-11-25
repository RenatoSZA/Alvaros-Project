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
        // SQL de inserção
        $sql = "INSERT INTO students (name, email, password_hash) VALUES (:name, :email, :pass)";
        $stmt = $this->pdo->prepare($sql);
        
        // Executa a query
        $stmt->execute([
            ':name'  => $student->name,
            ':email' => $student->email,
            // Acessando propriedade protegida via método mágico __get
            ':pass'  => $student->password_hash 
        ]);

        // O PULO DO GATO:
        // Pega o ID gerado pelo banco e injeta de volta no objeto Student
        $id = (int)$this->pdo->lastInsertId();
        $student->hydrateId($id); 
    }

    // ... (o resto do arquivo, método login, etc, continua igual) ...
    public function login(string $email, string $password): ?Student {
        $stmt = $this->pdo->prepare("SELECT * FROM students WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $data = $stmt->fetch();

        if ($data) {
            $student = new Student($data['name'], $data['email'], 'dummy_pass');
            // Hack para injetar o hash real do banco
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