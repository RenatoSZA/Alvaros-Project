<?php
namespace App\Controllers;

use Core\SessionManager;
use App\Models\Student;
use App\Mappers\StudentMapper;

class AuthController {

    // --- TELA DE LOGIN ---
    public function loginForm() {
        // Se já estiver logado, manda pro Dashboard
        if (SessionManager::isLogged()) {
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }
        // Mostra o HTML do Login
        require __DIR__ . '/../../views/login.php';
    }

    // --- PROCESSAR LOGIN ---
    public function processLogin() {
        $email = $_POST['email'] ?? '';
        $password = $_POST['senha'] ?? '';

        $mapper = new StudentMapper();
        $student = $mapper->login($email, $password);

        if ($student) {
            // Salva na sessão
            SessionManager::set('user_id', $student->id);
            SessionManager::set('user_name', $student->name);
            SessionManager::set('user_type', 'student');

            // Redireciona com BASE_URL
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        } else {
            $error = "E-mail ou senha incorretos!";
            require __DIR__ . '/../../views/login.php';
        }
    }

    // --- TELA DE CADASTRO (O método que estava faltando!) ---
    public function registerForm() {
        require __DIR__ . '/../../views/register.php';
    }

    public function processRegister() {
        try {
            // 1. Coleta os dados do formulário
            $name = $_POST['nome'];
            $email = $_POST['email'];
            $password = $_POST['senha'];
            
            // 2. Cria o objeto e salva no banco
            $newStudent = new Student($name, $email, $password);
            $mapper = new StudentMapper();
            
            // Ao salvar, o $newStudent ganha o ID automaticamente (graças ao Mapper acima)
            $mapper->save($newStudent);

            // 3. AUTO-LOGIN (Inicia a sessão direto)
            SessionManager::set('user_id', $newStudent->id);     // ID gerado agora
            SessionManager::set('user_name', $newStudent->name); // Nome do form
            SessionManager::set('user_type', 'student');         // Tipo padrão

            // 4. Redireciona direto para o Dashboard
            header('Location: ' . BASE_URL . '/dashboard');
            exit;

        } catch (\PDOException $e) {
            // Se der erro de banco (ex: Email duplicado)
            if ($e->getCode() == 23000) { // Código 23000 é "Duplicate entry"
                $error = "Esse e-mail já está cadastrado!";
            } else {
                $error = "Erro no banco de dados: " . $e->getMessage();
            }
            require __DIR__ . '/../../views/register.php';
            
        } catch (\Exception $e) {
            // Erros genéricos
            $error = "Erro ao cadastrar: " . $e->getMessage();
            require __DIR__ . '/../../views/register.php';
        }
    }

    // --- LOGOUT ---
    public function logout() {
        // 1. Destrói a sessão (limpa variáveis e mata o cookie)
        SessionManager::destroy();

        // 2. Redireciona para a tela de login usando o caminho absoluto
        header('Location: ' . BASE_URL . '/login');
        exit;
    }
}