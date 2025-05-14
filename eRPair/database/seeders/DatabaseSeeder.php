<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use File;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;



class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Filament Admin',
            'email' => 'admin@admin.com',
            'password' => bcrypt('12345admin'),
        ]);
        
        $path = database_path('seeders\data\import.csv');

        if (!File::exists($path)) {
            $this->command->error("Archivo CSV no encontrado: $path");
            return;
        }

        $csv = array_map('str_getcsv', file($path));
        $header = array_map('strtolower', array_shift($csv)); // ['brand_name', 'model']

        foreach ($csv as $row) {
            $data = array_combine($header, $row);

            if (!isset($data['brand'], $data['model'])) {
                continue;
            }

            // Normalizar y limpiar
            $brandName = strtoupper(trim($data['brand']));

            $rawModel = $data['model'];

            // Quitar texto entre paréntesis
            $cleanModel = preg_replace('/\s*\(.*?\)\s*/', '', $rawModel);

            // Quitar espacios múltiples
            $cleanModel = preg_replace('/\s+/', ' ', $cleanModel);

            // Convertir a mayúsculas
            $modelName = strtoupper(trim($cleanModel));

            // Obtener o crear la marca
            $brand = DB::table('brands')->where('name', $brandName)->first();

            if (!$brand) {
                $brandId = DB::table('brands')->insertGetId([
                    'name' => $brandName,
                ]);
            } else {
                $brandId = $brand->id;
            }

            // Insertar modelo si no existe
            DB::table('device_models')->updateOrInsert(
                ['name' => $modelName, 'brand_id' => $brandId],
                ['name' => $modelName, 'brand_id' => $brandId]
            );
        }

        $this->command->info("Modelos de dispositivos importados correctamente.");
        DB::table('taxes')->insert([
            ['name' => 'IVA', 'percentage' => 10],
            ['name' => 'IVA', 'percentage' => 21],
            ['name' => 'SIN IMPUESTOS', 'percentage' => 0],
        ]);

        DB::table('brands')->insert([
            ['name' => 'APPLE'],
            ['name' => 'SAMSUNG'],
            ['name' => 'XIAOMI'],
            ['name' => 'HUAWEI'],
            ['name' => 'OPPO'],
        ]);

        
        DB::table('payment_methods')->insert([
            ['name' => 'TARJETA'],
            ['name' => 'EFECTIVO'],
            ['name' => 'TRANSFERENCIA'],
        ]);

        DB::table('document_types')->insert([
            ['name' => 'DNI'],
            ['name' => 'NIE'],
            ['name' => 'PASAPORTE'],
            ['name' => 'OTRO'],
        ]);

        DB::table('statuses')->insert([
            ['name' => 'PENDIENTE'],
            ['name' => 'PENDIENTE DE PIEZA'],
            ['name' => 'ASIGNADO'],
            ['name' => 'COMPLETADO'],
            ['name' => 'CANCELADO'],
        ]);

        DB::table('roles')->insert([
            ['name' => 'ADMIN'],
            ['name' => 'DEPENDIENTE'],
            ['name' => 'TÉCNICO'],
            ['name' => 'ENCARGADO'],
        ]);

        DB::table('types')->insert([
            ['name' => 'PANTALLA'],
            ['name' => 'BATERÍA'],
            ['name' => 'CONECTOR DE CARGA'],
            ['name' => 'FLEX DE CARGA'],
            ['name' => 'FLEX'],
            ['name' => 'LENTE'],
            ['name' => 'TAPA'],
            ['name'=> 'ACCESORIO'],
            ['name'=> 'OTRO'],
        ]);

        DB::table('categories')->insert([
            ['name' => 'SERVICIOS', 'tax_id' => 1],
            ['name' => 'PIEZAS', 'tax_id' => 1],
            ['name' => 'ACCESORIOS', 'tax_id' => 1],
            ['name' => 'REACONDICIONADOS', 'tax_id' => 3],
            ['name' => 'PROPINAS', 'tax_id' => 3],
        ]);

        
        DB::table('clients')->insert([
            [
            'document' => '12345678A',
            'name' => 'JUAN',
            'surname' => 'PÉREZ',
            'surname2' => 'GARCÍA',
            'phone_number' => '600123456',
            'phone_number_2' => '600654321',
            'postal_code' => '28001',
            'address' => 'CALLE MAYOR, 1',
            'document_type_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
            ],
            [
            'document' => '87654321B',
            'name' => 'MARÍA',
            'surname' => 'LÓPEZ',
            'surname2' => null,
            'phone_number' => '610987654',
            'phone_number_2' => null,
            'postal_code' => '28002',
            'address' => 'CALLE GRAN VÍA, 2',
            'document_type_id' => 2,
            'created_at' => now(),
            'updated_at' => now(),
            ],
            [
            'document' => '11223344C',
            'name' => 'CARLOS',
            'surname' => 'MARTÍNEZ',
            'surname2' => 'FERNÁNDEZ',
            'phone_number' => '620123789',
            'phone_number_2' => '620987321',
            'postal_code' => '28003',
            'address' => 'CALLE ALCALÁ, 3',
            'document_type_id' => 3,
            'created_at' => now(),
            'updated_at' => now(),
            ],
        ]);
        DB::table('companies')->insert([
            [
            'cif' => 'A12345678',
            'name' => 'TECH SOLUTIONS',
            'corporate_name' => 'TECH SOLUTIONS S.L.',
            'address' => 'CALLE INNOVACIÓN, 10',
            'postal_code' => '28004',
            'locality' => 'MADRID',
            'province' => 'MADRID',
            'discount' => 0,
            'created_at' => now(),
            'updated_at' => now(),
            ],
            [
            'cif' => 'B87654321',
            'name' => 'GREEN ENERGY',
            'corporate_name' => 'GREEN ENERGY S.A.',
            'address' => 'AVENIDA VERDE, 20',
            'postal_code' => '28005',
            'locality' => 'MADRID',
            'province' => 'MADRID',
            'discount' => 3.0,
            'created_at' => now(),
            'updated_at' => now(),
            ],
            [
            'cif' => 'C11223344',
            'name' => 'SMART DEVICES',
            'corporate_name' => 'SMART DEVICES CORP.',
            'address' => 'PASEO INTELIGENTE, 30',
            'postal_code' => '28006',
            'locality' => 'MADRID',
            'province' => 'MADRID',
            'discount' => 5.0,
            'created_at' => now(),
            'updated_at' => now(),
            ],
        ]);
        
        DB::table('stores')->insert([
            [
            'name' => 'PLAZA MAYOR',
            'address' => 'CALLE REPARACIÓN, 1',
            'work_order_number' => 0,
            'created_at' => now(),
            'updated_at' => now(),
            ],
            [
            'name' => 'POLIGONO SANTA BARBARA',
            'address' => 'CALLE ENERGÍA, 2',
            'work_order_number' => 0,
            'created_at' => now(),
            'updated_at' => now(),
            ],
            [
            'name' => 'VIALIA',
            'address' => 'CALLE CARGA, 3',
            'work_order_number' => 0,
            'created_at' => now(),
            'updated_at' => now(),
            ],
        ]);

        DB::table('repair_times')->insert([
            [
            'name' => '1 HORA',
            'created_at' => now(),
            'updated_at' => now(),
            ],
            [
            'name' => '2 HORAS',
            'created_at' => now(),
            'updated_at' => now(),
            ],
            [
            'name' => '3 HORAS',
            'created_at' => now(),
            'updated_at' => now(),
            ],
            [
            'name' => '2 DAYS',
            'created_at' => now(),
            'updated_at' => now(),
            ],
            [
                'name' => 'SE AVISARÁ CUANDO ENCONTREMOS LA AVERÍA.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'SE AVISARÁ CUANDO LA REPARACION ESTÉ FINALIZADA.',
                'created_at' => now(),
                'updated_at' => now(),
            ],

        ]);


        DB::table('devices')->insert([
            [
                'has_no_serial_or_imei' => false,
                'serial_number' => 'SN1234567890',
                'IMEI' => '123456789012345',
                'colour' => 'Black',
                'unlock_code' => '1234',
                'device_model_id' => 1,
                'client_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'has_no_serial_or_imei' => false,
                'serial_number' => 'SN0987654321',
                'IMEI' => '543210987654321',
                'colour' => 'White',
                'unlock_code' => '5678',
                'device_model_id' => 2,
                'client_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'has_no_serial_or_imei' => true,
                'serial_number' => null,
                'IMEI' => null,
                'colour' => 'Blue',
                'unlock_code' => null,
                'device_model_id' => 3,
                'client_id' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'has_no_serial_or_imei' => false,
                'serial_number' => 'SN1122334455',
                'IMEI' => '112233445566778',
                'colour' => 'Red',
                'unlock_code' => '4321',
                'device_model_id' => 4,
                'client_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'has_no_serial_or_imei' => false,
                'serial_number' => 'SN5566778899',
                'IMEI' => '998877665544332',
                'colour' => 'Green',
                'unlock_code' => '8765',
                'device_model_id' => 5,
                'client_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'has_no_serial_or_imei' => true,
                'serial_number' => null,
                'IMEI' => null,
                'colour' => 'Yellow',
                'unlock_code' => null,
                'device_model_id' => 6,
                'client_id' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'has_no_serial_or_imei' => false,
                'serial_number' => 'SN6677889900',
                'IMEI' => '667788990011223',
                'colour' => 'Purple',
                'unlock_code' => '1111',
                'device_model_id' => 7,
                'client_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'has_no_serial_or_imei' => false,
                'serial_number' => 'SN4455667788',
                'IMEI' => '445566778899001',
                'colour' => 'Pink',
                'unlock_code' => '2222',
                'device_model_id' => 8,
                'client_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'has_no_serial_or_imei' => true,
                'serial_number' => null,
                'IMEI' => null,
                'colour' => 'Orange',
                'unlock_code' => null,
                'device_model_id' => 9,
                'client_id' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'has_no_serial_or_imei' => false,
                'serial_number' => 'SN2233445566',
                'IMEI' => '223344556677889',
                'colour' => 'Gray',
                'unlock_code' => '3333',
                'device_model_id' => 10,
                'client_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'has_no_serial_or_imei' => false,
                'serial_number' => 'SN9988776655',
                'IMEI' => '998877665544332',
                'colour' => 'Silver',
                'unlock_code' => '4444',
                'device_model_id' => 11,
                'client_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'has_no_serial_or_imei' => true,
                'serial_number' => null,
                'IMEI' => null,
                'colour' => 'Gold',
                'unlock_code' => null,
                'device_model_id' => 12,
                'client_id' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'has_no_serial_or_imei' => false,
                'serial_number' => 'SN3344556677',
                'IMEI' => '334455667788990',
                'colour' => 'Black',
                'unlock_code' => '5555',
                'device_model_id' => 13,
                'client_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'has_no_serial_or_imei' => false,
                'serial_number' => 'SN7766554433',
                'IMEI' => '776655443322110',
                'colour' => 'White',
                'unlock_code' => '6666',
                'device_model_id' => 14,
                'client_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'has_no_serial_or_imei' => true,
                'serial_number' => null,
                'IMEI' => null,
                'colour' => 'Blue',
                'unlock_code' => null,
                'device_model_id' => 15,
                'client_id' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'has_no_serial_or_imei' => false,
                'serial_number' => 'SN5566778899',
                'IMEI' => '556677889900112',
                'colour' => 'Red',
                'unlock_code' => '7777',
                'device_model_id' => 16,
                'client_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'has_no_serial_or_imei' => false,
                'serial_number' => 'SN1122334455',
                'IMEI' => '112233445566778',
                'colour' => 'Green',
                'unlock_code' => '8888',
                'device_model_id' => 17,
                'client_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'has_no_serial_or_imei' => true,
                'serial_number' => null,
                'IMEI' => null,
                'colour' => 'Yellow',
                'unlock_code' => null,
                'device_model_id' => 18,
                'client_id' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'has_no_serial_or_imei' => false,
                'serial_number' => 'SN6677889900',
                'IMEI' => '667788990011223',
                'colour' => 'Purple',
                'unlock_code' => '9999',
                'device_model_id' => 19,
                'client_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'has_no_serial_or_imei' => false,
                'serial_number' => 'SN4455667788',
                'IMEI' => '445566778899001',
                'colour' => 'Pink',
                'unlock_code' => '0000',
                'device_model_id' => 20,
                'client_id' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            
        ]);

        DB::table('store_user')->insert([
            [
            'user_id' => 1,
            'store_id' => 1,
            'created_at' => now(),
            'updated_at' => now(),
            ],
            [
            'user_id' => 1,
            'store_id' => 2,
            'created_at' => now(),
            'updated_at' => now(),
            ],
            [
            'user_id' => 1,
            'store_id' => 3,
            'created_at' => now(),
            'updated_at' => now(),
            ],
        ]);

        DB::table('rol_user')->insert([
            [
            'user_id' => 1,
            'rol_id' => 1, // ADMIN
            'created_at' => now(),
            'updated_at' => now(),
            ],
            [
            'user_id' => 1,
            'rol_id' => 2, // DEPENDIENTE
            'created_at' => now(),
            'updated_at' => now(),
            ],
            [
            'user_id' => 1,
            'rol_id' => 3, // TÉCNICO
            'created_at' => now(),
            'updated_at' => now(),
            ],
        ]);

        $deviceModels = DB::table('device_models')->get();
        $parts = ['Pantalla', 'Batería', 'Conector de carga', 'Cámara', 'Reparación de placa'];
        $distributors = ['KF', 'PA', 'SS'];
        $typeId = DB::table('types')->where('name', 'PIEZA')->value('id');
        $categoryId = DB::table('categories')->where('name', 'PIEZAS')->value('id');

        foreach ($deviceModels as $deviceModel) {
            foreach ($parts as $part) {
            DB::table('items')->insert([
                'name' => "{$part} para {$deviceModel->name}",
                'cost' => rand(10, 100), // Random cost
                'price' => rand(150, 300), // Random price
                'distributor' => $distributors[array_rand($distributors)],
                'type_id' => match ($part) {
                    'Pantalla' => 1,
                    'Batería' => 2,
                    'Conector de carga' => 3,
                    'Cámara' => 6,
                    'Reparación de placa' => 9,
                    default => $typeId,
                },
                'category_id' => $categoryId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            }
        }
    }
}
