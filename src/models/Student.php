<?php
namespace App\Models;

use Core\Model;

class Student extends Model {
    protected ?int $id = null;
    protected string $name;
    protected string $email;
    protected string $password_hash;
    protected ?string $gov_id = null;

    public function __construct(string $name, string $email, string $password_hash) {
        $this->name = $name;
        $this->email = $email;
        $this->password_hash = password_hash($password_hash, PASSWORD_DEFAULT);
    }

    // Método auxiliar para setar o ID vindo do banco (pois ID não deve ser mudado manualmente)
    public function hydrateId(int $id) {
        $this->id = $id;
    }
    
    // Método para validar senha (lógica de negócio não usa mágica)
    public function verifyPassword(string $password): bool {
        return password_verify($password, $this->password_hash);
    }
}