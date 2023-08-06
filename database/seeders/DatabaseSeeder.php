<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Category;
use App\Models\UnitOfMeasurement;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if(Role::all()->isEmpty()){
            Role::create(['name' => 'admin']);
            Role::create(['name' => 'farmer']);
            Role::create(['name' => 'client']);
            echo "Roles insertados correctamente." . PHP_EOL;
        }
        $userData = [
            'first_name' => 'Administración',
            'last_name' => 'Agrimarket',
            'email' => 'admin@agrimarket.com',
            'password' => bcrypt('password')
        ];
        $existingUser = User::where('email', $userData['email'])->first();
        if (!$existingUser) {
            $user = User::create($userData);
            $user->assignRole('admin');
            echo "Usuario insertado correctamente." . PHP_EOL;
        }

        if(Category::all()->isEmpty()){
            Category::create([
                "name"=> "Cereales",
                "active"=>true
            ]);
            Category::create([
                "name"=> "Frutas",
                "active"=>true
            ]);
            Category::create([
                "name"=> "Veduras",
                "active"=>true
            ]);
            Category::create([
                "name"=> "Legumbres",
                "active"=>true
            ]);
            Category::create([
                "name"=> "Industriales",
                "active"=>true
            ]);
            Category::create([
                "name"=> "Oleaginosos",
                "active"=>true
            ]);
            echo "Categorias insertadas correctamente." . PHP_EOL;
        }

        if(UnitOfMeasurement::all()->isEmpty()){
            UnitOfMeasurement::create([
                "name"=> "Kilogramo",
                "code"=> "KG",
                "active"=>true
            ]);
            UnitOfMeasurement::create([
                "name"=> "Tonelada",
                "code"=> "T",
                "active"=>true
            ]);
            UnitOfMeasurement::create([
                "name"=> "Litro",
                "code"=> "L",
                "active"=>true
            ]);
            UnitOfMeasurement::create([
                "name"=> "Metro cúbico",
                "code"=> "M3",
                "active"=>true
            ]);
            UnitOfMeasurement::create([
                "name"=> "Metro cuadrado",
                "code"=> "M2",
                "active"=>true
            ]);
            UnitOfMeasurement::create([
                "name"=> "Hectárea",
                "code"=> "HA",
                "active"=>true
            ]);
            UnitOfMeasurement::create([
                "name"=> "Piezas",
                "code"=> "PZ",
                "active"=>true
            ]);
            UnitOfMeasurement::create([
                "name"=> "Bolsa",
                "code"=> "BS",
                "active"=>true
            ]);
            echo "Unidades de medida insertadas correctamente." . PHP_EOL;
        }
    }
}
