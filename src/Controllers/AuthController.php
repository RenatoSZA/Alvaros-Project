<?php
namespace App\Controllers;

use Core\SessionManager;
use App\Models\Student;
use App\Mappers\StudentMapper;

class AuthController {

    public function loginForm() {
        if (SessionManager::isLogged()) {
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }
        require __DIR__ . '/../../views/login.php';
    }

    public function processLogin() {
        $email = $_POST['email'] ?? '';
        $password = $_POST['senha'] ?? '';

        $mapper = new StudentMapper();
        $student = $mapper->login($email, $password);

        if ($student) {
            SessionManager::set('user_id', $student->id);
            SessionManager::set('user_name', $student->name);
            SessionManager::set('user_type', 'student');

            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        } else {
            $error = "E-mail ou senha incorretos!";
            require __DIR__ . '/../../views/login.php';
        }
    }

    public function registerForm() {
        require __DIR__ . '/../../views/register.php';
    }

    public function processRegister() {
        try {
            $name = $_POST['nome'];
            $email = $_POST['email'];
            $password = $_POST['senha'];
            
            $newStudent = new Student($name, $email, $password);
            $mapper = new StudentMapper();
            
            $mapper->save($newStudent);

            SessionManager::set('user_id', $newStudent->id);    
            SessionManager::set('user_name', $newStudent->name);
            SessionManager::set('user_type', 'student');        

            header('Location: ' . BASE_URL . '/dashboard');
            exit;

        } catch (\PDOException $e) {
            if ($e->getCode() == 23000) { 
                $error = "Esse e-mail já está cadastrado!";
            } else {
                $error = "Erro no banco de dados: " . $e->getMessage();
            }
            require __DIR__ . '/../../views/register.php';
            
        } catch (\Exception $e) {
            $error = "Erro ao cadastrar: " . $e->getMessage();
            require __DIR__ . '/../../views/register.php';
        }
    }

    public function logout() {
        SessionManager::destroy();

        header('Location: ' . BASE_URL . '/login');
        exit;
    }
}