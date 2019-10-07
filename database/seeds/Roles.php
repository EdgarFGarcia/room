<?php

use Illuminate\Database\Seeder;

class Roles extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('roles')->insert([
            [
          		'role' => 'Driver',
          		'created_at' => date('Y-m-d H:i:s'),
          		'updated_at' => date('Y-m-d H:i:s'),
          	],
            [
          		'role' => 'Duty Manager',
          		'created_at' => date('Y-m-d H:i:s'),
          		'updated_at' => date('Y-m-d H:i:s'),
          	],
            [
          		'role' => 'TMD Specialist',
          		'created_at' => date('Y-m-d H:i:s'),
          		'updated_at' => date('Y-m-d H:i:s'),
          	],
            [
          		'role' => 'Guest Service Specialist',
          		'created_at' => date('Y-m-d H:i:s'),
          		'updated_at' => date('Y-m-d H:i:s'),
          	],
            [
          		'role' => 'IT Programmer',
          		'created_at' => date('Y-m-d H:i:s'),
          		'updated_at' => date('Y-m-d H:i:s'),
          	],
            [
          		'role' => 'Room Attendant',
          		'created_at' => date('Y-m-d H:i:s'),
          		'updated_at' => date('Y-m-d H:i:s'),
          	],
            [
          		'role' => 'Assistant Cook',
          		'created_at' => date('Y-m-d H:i:s'),
          		'updated_at' => date('Y-m-d H:i:s'),
          	],
            [
          		'role' => 'Stockman',
          		'created_at' => date('Y-m-d H:i:s'),
          		'updated_at' => date('Y-m-d H:i:s'),
          	],
            [
          		'role' => 'Naterials Controller',
          		'created_at' => date('Y-m-d H:i:s'),
          		'updated_at' => date('Y-m-d H:i:s'),
          	],
            [
          		'role' => 'Guest Attendant',
          		'created_at' => date('Y-m-d H:i:s'),
          		'updated_at' => date('Y-m-d H:i:s'),
          	],
            [
          		'role' => 'Maintenance Man',
          		'created_at' => date('Y-m-d H:i:s'),
          		'updated_at' => date('Y-m-d H:i:s'),
          	],
            [
          		'role' => 'Cook',
          		'created_at' => date('Y-m-d H:i:s'),
          		'updated_at' => date('Y-m-d H:i:s'),
          	],
            [
          		'role' => 'Gardener',
          		'created_at' => date('Y-m-d H:i:s'),
          		'updated_at' => date('Y-m-d H:i:s'),
          	],
            [
          		'role' => 'Party Coordinator',
          		'created_at' => date('Y-m-d H:i:s'),
          		'updated_at' => date('Y-m-d H:i:s'),
          	],
            [
          		'role' => 'Locale Accountant',
          		'created_at' => date('Y-m-d H:i:s'),
          		'updated_at' => date('Y-m-d H:i:s'),
          	],
            [
          		'role' => 'Room Steward',
          		'created_at' => date('Y-m-d H:i:s'),
          		'updated_at' => date('Y-m-d H:i:s'),
          	],
            [
          		'role' => 'Head Cook',
          		'created_at' => date('Y-m-d H:i:s'),
          		'updated_at' => date('Y-m-d H:i:s'),
          	],
            [
          		'role' => 'Finance Officer',
          		'created_at' => date('Y-m-d H:i:s'),
          		'updated_at' => date('Y-m-d H:i:s'),
          	],
            [
          		'role' => 'FNB - Supervisor',
          		'created_at' => date('Y-m-d H:i:s'),
          		'updated_at' => date('Y-m-d H:i:s'),
          	],
            [
          		'role' => 'Executive Office Secretary',
          		'created_at' => date('Y-m-d H:i:s'),
          		'updated_at' => date('Y-m-d H:i:s'),
          	],
            [
          		'role' => 'Procurement Officer',
          		'created_at' => date('Y-m-d H:i:s'),
          		'updated_at' => date('Y-m-d H:i:s'),
          	],
            [
          		'role' => 'Manager',
          		'created_at' => date('Y-m-d H:i:s'),
          		'updated_at' => date('Y-m-d H:i:s'),
          	],
            [
          		'role' => 'Internal Audit Specialist',
          		'created_at' => date('Y-m-d H:i:s'),
          		'updated_at' => date('Y-m-d H:i:s'),
          	],
            [
          		'role' => 'TMD - Talent Acquisition Officer',
          		'created_at' => date('Y-m-d H:i:s'),
          		'updated_at' => date('Y-m-d H:i:s'),
          	],
            [
          		'role' => 'IT Technical Support',
          		'created_at' => date('Y-m-d H:i:s'),
          		'updated_at' => date('Y-m-d H:i:s'),
          	],
            [
          		'role' => 'Finance Associate',
          		'created_at' => date('Y-m-d H:i:s'),
          		'updated_at' => date('Y-m-d H:i:s'),
          	],
            [
          		'role' => 'Senior IT Programmer',
          		'created_at' => date('Y-m-d H:i:s'),
          		'updated_at' => date('Y-m-d H:i:s'),
          	],
            [
          		'role' => 'TMD - HRIS & Timekeeping Officer',
          		'created_at' => date('Y-m-d H:i:s'),
          		'updated_at' => date('Y-m-d H:i:s'),
          	],
            [
          		'role' => 'Chief Financial Officer',
          		'created_at' => date('Y-m-d H:i:s'),
          		'updated_at' => date('Y-m-d H:i:s'),
          	],
            [
          		'role' => 'Interior Designer',
          		'created_at' => date('Y-m-d H:i:s'),
          		'updated_at' => date('Y-m-d H:i:s'),
          	],
            [
          		'role' => 'Quality Management Officer',
          		'created_at' => date('Y-m-d H:i:s'),
          		'updated_at' => date('Y-m-d H:i:s'),
          	],
            [
          		'role' => 'IT Tech Support Officer',
          		'created_at' => date('Y-m-d H:i:s'),
          		'updated_at' => date('Y-m-d H:i:s'),
          	],
            [
          		'role' => 'Marketing Specialist',
          		'created_at' => date('Y-m-d H:i:s'),
          		'updated_at' => date('Y-m-d H:i:s'),
          	],
            [
          		'role' => 'Graphic Artist',
          		'created_at' => date('Y-m-d H:i:s'),
          		'updated_at' => date('Y-m-d H:i:s'),
          	],
            [
          		'role' => 'Procurement Specialist',
          		'created_at' => date('Y-m-d H:i:s'),
          		'updated_at' => date('Y-m-d H:i:s'),
          	],
            [
          		'role' => 'Project Engineer',
          		'created_at' => date('Y-m-d H:i:s'),
          		'updated_at' => date('Y-m-d H:i:s'),
          	],
            [
          		'role' => 'Asst Accounting Manager',
          		'created_at' => date('Y-m-d H:i:s'),
          		'updated_at' => date('Y-m-d H:i:s'),
          	],
            [
          		'role' => 'Treasury Accountant',
          		'created_at' => date('Y-m-d H:i:s'),
          		'updated_at' => date('Y-m-d H:i:s'),
          	],
            [
          		'role' => 'IT Jr. Programmer/Tech Support',
          		'created_at' => date('Y-m-d H:i:s'),
          		'updated_at' => date('Y-m-d H:i:s'),
          	],
            [
          		'role' => 'Employee Welfare Officer',
          		'created_at' => date('Y-m-d H:i:s'),
          		'updated_at' => date('Y-m-d H:i:s'),
          	],
            [
          		'role' => 'Training Officer',
          		'created_at' => date('Y-m-d H:i:s'),
          		'updated_at' => date('Y-m-d H:i:s'),
          	],
            [
          		'role' => 'Internal Audit Officer',
          		'created_at' => date('Y-m-d H:i:s'),
          		'updated_at' => date('Y-m-d H:i:s'),
          	],
            [
          		'role' => 'Internal Audit Supervisor',
          		'created_at' => date('Y-m-d H:i:s'),
          		'updated_at' => date('Y-m-d H:i:s'),
          	],
            [
          		'role' => 'Facilities Attendant',
          		'created_at' => date('Y-m-d H:i:s'),
          		'updated_at' => date('Y-m-d H:i:s'),
          	],
            [
          		'role' => 'Owner',
          		'created_at' => date('Y-m-d H:i:s'),
          		'updated_at' => date('Y-m-d H:i:s'),
          	],
            [
          		'role' => 'IT Manager',
          		'created_at' => date('Y-m-d H:i:s'),
          		'updated_at' => date('Y-m-d H:i:s'),
          	],
            [
          		'role' => 'Restaurant Consultant',
          		'created_at' => date('Y-m-d H:i:s'),
          		'updated_at' => date('Y-m-d H:i:s'),
          	],
            [
          		'role' => 'Android Developer',
          		'created_at' => date('Y-m-d H:i:s'),
          		'updated_at' => date('Y-m-d H:i:s'),
          	],
            [
          		'role' => 'iOS Developer',
          		'created_at' => date('Y-m-d H:i:s'),
          		'updated_at' => date('Y-m-d H:i:s'),
          	],
            [
          		'role' => 'CFO',
          		'created_at' => date('Y-m-d H:i:s'),
          		'updated_at' => date('Y-m-d H:i:s'),
          	],
            [
          		'role' => 'Marketing Officer',
          		'created_at' => date('Y-m-d H:i:s'),
          		'updated_at' => date('Y-m-d H:i:s'),
          	],
            [
          		'role' => 'Finance Manager',
          		'created_at' => date('Y-m-d H:i:s'),
          		'updated_at' => date('Y-m-d H:i:s'),
          	],
            [
          		'role' => 'Quality Management Specialist',
          		'created_at' => date('Y-m-d H:i:s'),
          		'updated_at' => date('Y-m-d H:i:s'),
          	],
            [
          		'role' => 'Scanning and Filing Clerk',
          		'created_at' => date('Y-m-d H:i:s'),
          		'updated_at' => date('Y-m-d H:i:s'),
          	],
            [
          		'role' => 'Multimedia Artist',
          		'created_at' => date('Y-m-d H:i:s'),
          		'updated_at' => date('Y-m-d H:i:s'),
          	],
            [
          		'role' => 'FNB Manager',
          		'created_at' => date('Y-m-d H:i:s'),
          		'updated_at' => date('Y-m-d H:i:s'),
          	],
            [
          		'role' => 'Web Developer',
          		'created_at' => date('Y-m-d H:i:s'),
          		'updated_at' => date('Y-m-d H:i:s'),
          	],
            [
          		'role' => 'Chief Executive Officer',
          		'created_at' => date('Y-m-d H:i:s'),
          		'updated_at' => date('Y-m-d H:i:s'),
          	],
        ]);
    }
}
