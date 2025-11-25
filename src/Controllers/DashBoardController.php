<?php
namespace App\Controllers;

use Core\SessionManager;

class DashboardController {
    
    public function index() {
        if (!SessionManager::isLogged()) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }
        $userName = SessionManager::get('user_name');
        require __DIR__ . '/../../views/dashboard_student.php';
    }

    // --- NOVA API JSON ---
    public function getStudentData() {
        // Verifica login
        if (!SessionManager::isLogged()) {
            http_response_code(401);
            echo json_encode(['error' => 'Não autorizado']);
            exit;
        }

        // Simulação dos dados que viriam do WorkoutMapper/Banco de Dados
        // Futuramente: $plan = $workoutMapper->getPlanForStudent($id);
        
        $data = [
            'user' => [
                'name' => SessionManager::get('user_name'),
                'lastAccess' => 'Hoje, ' . date('H:i')
            ],
            'stats' => [
                'freq' => 4, 
                'target' => 5, 
                'cal' => 65, 
                'cardio' => 80
            ],
            'plan' => [
                'seg' => [
                    'title' => 'Peito + Tríceps',
                    'exercises' => [
                        ['name' => 'Supino Reto', 'sets' => 3, 'reps' => '12', 'load' => 20, 'rest' => 60, 'note' => 'Cuidado com o ombro', 'img' => ''],
                        ['name' => 'Tríceps Corda', 'sets' => 4, 'reps' => '15', 'load' => 15, 'rest' => 45, 'note' => 'Estica tudo', 'img' => '']
                    ]
                ],
                'ter' => ['title' => 'Costas + Bíceps', 'exercises' => []],
                'qua' => ['title' => 'Pernas', 'exercises' => []],
                'qui' => ['title' => 'Ombros', 'exercises' => []],
                'sex' => ['title' => 'Cardio', 'exercises' => []],
                'sab' => ['title' => 'Full Body', 'exercises' => []]
            ]
        ];

        // Retorna JSON para o JavaScript
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}