<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

     public function run()
     {
         // Optionnel : créer les rôles via Spatie si ce n’est pas encore fait
         $adminRole = Role::firstOrCreate(['name' => 'admin']);
         $autoriteRole = Role::firstOrCreate(['name' => 'autorite']);
         $citoyenRole = Role::firstOrCreate(['name' => 'citoyen']);
 
         // Créer un admin
         $admin = User::create([
             'nom' => 'Admin',
             'prenom' => 'Principal',
             'email' => 'admin@example.com',
             'password' => Hash::make('password'),
             'telephone' => '770000000',
             'adresse' => 'Dakar',
             'role' => 'admin',
         ]);
         $admin->assignRole($adminRole);
 
         // Créer une autorité
         $autorite = User::create([
             'nom' => 'Autorite',
             'prenom' => 'Locale',
             'email' => 'autorite@example.com',
             'password' => Hash::make('password'),
             'telephone' => '771111111',
             'adresse' => 'Thiès',
             'role' => 'autorite',
         ]);
         $autorite->assignRole($autoriteRole);
 
         // Créer un citoyen
         $citoyen = User::create([
             'nom' => 'Citoyen',
             'prenom' => 'Ordinaire',
             'email' => 'citoyen@example.com',
             'password' => Hash::make('password'),
             'telephone' => '772222222',
             'adresse' => 'Kaolack',
             'role' => 'citoyen',
         ]);
         $citoyen->assignRole($citoyenRole);
     }
}