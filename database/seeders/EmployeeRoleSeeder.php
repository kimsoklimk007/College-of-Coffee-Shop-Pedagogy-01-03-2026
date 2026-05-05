<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EmployeeRole;

class EmployeeRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Administrator',
                'name_kh' => 'Administrator',
                'description' => 'Full system access and management capabilities',
                'description_kh' => 'Access complet au système et capacités de gestion',
                'permissions' => [
                    'employee.create', 'employee.edit', 'employee.delete', 'employee.view',
                    'attendance.manage', 'attendance.approve',
                    'leave.manage', 'leave.approve',
                    'payroll.create', 'payroll.approve', 'payroll.view',
                    'reports.view', 'reports.export',
                    'settings.manage', 'system.admin'
                ],
                'base_salary' => 800.00,
                'is_active' => true,
            ],
            [
                'name' => 'Manager',
                'name_kh' => 'Manager',
                'description' => 'Department management and team supervision',
                'description_kh' => 'Gestion départementale et supervision d\'équipe',
                'permissions' => [
                    'employee.create', 'employee.edit', 'employee.view',
                    'attendance.manage', 'attendance.approve',
                    'leave.manage', 'leave.approve',
                    'payroll.view',
                    'reports.view', 'reports.export'
                ],
                'base_salary' => 600.00,
                'is_active' => true,
            ],
            [
                'name' => 'Supervisor',
                'name_kh' => 'Supervisor',
                'description' => 'Team supervision and basic operations',
                'description_kh' => 'Supervision d\'équipe et opérations de base',
                'permissions' => [
                    'employee.view',
                    'attendance.manage',
                    'leave.view',
                    'reports.view'
                ],
                'base_salary' => 450.00,
                'is_active' => true,
            ],
            [
                'name' => 'Cashier',
                'name_kh' => 'Caissier',
                'description' => 'Payment processing and customer service',
                'description_kh' => 'Traitement des paiements et service client',
                'permissions' => [
                    'attendance.view',
                    'leave.view',
                    'reports.view'
                ],
                'base_salary' => 350.00,
                'is_active' => true,
            ],
            [
                'name' => 'Barista',
                'name_kh' => 'Barista',
                'description' => 'Coffee preparation and customer service',
                'description_kh' => 'Préparation du café et service client',
                'permissions' => [
                    'attendance.view',
                    'leave.view'
                ],
                'base_salary' => 300.00,
                'is_active' => true,
            ],
            [
                'name' => 'Waiter/Waitress',
                'name_kh' => 'Serveur/Serveuse',
                'description' => 'Table service and customer assistance',
                'description_kh' => 'Service de table et assistance client',
                'permissions' => [
                    'attendance.view',
                    'leave.view'
                ],
                'base_salary' => 280.00,
                'is_active' => true,
            ],
            [
                'name' => 'Kitchen Staff',
                'name_kh' => 'Personnel de Cuisine',
                'description' => 'Food preparation and kitchen operations',
                'description_kh' => 'Préparation des aliments et opérations de cuisine',
                'permissions' => [
                    'attendance.view',
                    'leave.view'
                ],
                'base_salary' => 320.00,
                'is_active' => true,
            ],
            [
                'name' => 'Cleaner',
                'name_kh' => 'Agent de Nettoyage',
                'description' => 'Cleaning and maintenance duties',
                'description_kh' => 'Tâches de nettoyage et d\'entretien',
                'permissions' => [
                    'attendance.view',
                    'leave.view'
                ],
                'base_salary' => 250.00,
                'is_active' => true,
            ],
            [
                'name' => 'Security',
                'name_kh' => 'Sécurité',
                'description' => 'Security and safety operations',
                'description_kh' => 'Opérations de sécurité et de sûreté',
                'permissions' => [
                    'attendance.view',
                    'leave.view'
                ],
                'base_salary' => 400.00,
                'is_active' => true,
            ],
            [
                'name' => 'Intern',
                'name_kh' => 'Stagiaire',
                'description' => 'Training and learning position',
                'description_kh' => 'Poste de formation et d\'apprentissage',
                'permissions' => [
                    'attendance.view',
                    'leave.view'
                ],
                'base_salary' => 150.00,
                'is_active' => true,
            ],
        ];

        foreach ($roles as $role) {
            EmployeeRole::create($role);
        }
    }
}
