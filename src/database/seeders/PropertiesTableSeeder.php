<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use App\Models\Property;

class PropertiesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $path = base_path('property-data.csv');
        if (!File::exists($path)) {
            $this->command->info('CSV not found: ' . $path);
            return;
        }

        $rows = array_map('str_getcsv', file($path));
        $header = array_shift($rows);

        foreach ($rows as $row) {
            $data = array_combine($header, $row);
            Property::create([
                'name' => $data['Name'],
                'price' => (int)$data['Price'],
                'bedrooms' => (int)$data['Bedrooms'],
                'bathrooms' => (int)$data['Bathrooms'],
                'storeys' => (int)$data['Storeys'],
                'garages' => (int)$data['Garages'],
            ]);
        }
    }
}
