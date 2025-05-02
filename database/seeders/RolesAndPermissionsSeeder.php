<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roleAdmin = Role::create(['name' => 'admin']);
        $roleCitoyen = Role::create(['name' => 'citoyen']);
        $roleAutorite = Role::create(['name' => 'autorite']);

    // Permissions pour les signalements
Permission::create(['name' => 'creer signalement']);
Permission::create(['name' => 'modifier signalement']);
Permission::create(['name' => 'supprimer signalement']);
Permission::create(['name' => 'voir signalements']);
Permission::create(['name' => 'traiter signalement']);
Permission::create(['name' => 'ajouter resolution']);
Permission::create(['name' => 'voir resolutions']);

// Permissions pour les commentaires
Permission::create(['name' => 'ajouter commentaire']);
Permission::create(['name' => 'supprimer commentaire']);

// Permissions pour les votes
Permission::create(['name' => 'voter signalement']);
Permission::create(['name' => 'voir votes']);

// Gestion des utilisateurs (admin uniquement)
Permission::create(['name' => 'voir utilisateurs']);
Permission::create(['name' => 'attribuer roles']);
Permission::create(['name' => 'supprimer utilisateur']);

// Gestion des catégories
Permission::create(['name' => 'creer categorie']);
Permission::create(['name' => 'modifier categorie']);
Permission::create(['name' => 'supprimer categorie']);
Permission::create(['name' => 'voir categories']);
      
$roleCitoyen->givePermissionTo([
    'creer signalement',
    'modifier signalement',
    'supprimer signalement',
    'ajouter commentaire',
    'voter signalement',
    'voir signalements',
    'voir resolutions',
]);

$roleAutorite->givePermissionTo([
    'voir signalements',
    'traiter signalement',
    'ajouter resolution',
    'ajouter commentaire',
    'voir votes',
]);

$roleAdmin->givePermissionTo(Permission::all()); 
    }
}
