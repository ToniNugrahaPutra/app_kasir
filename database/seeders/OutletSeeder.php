<?php

namespace Database\Seeders;

use App\Models\Outlet;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class OutletSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        function generateOutletCode()
        {
            $timestamp = time();
            $kodeOutlet = 'OUT' . $timestamp;
            return $kodeOutlet;
        }

        Outlet::create([
            'name' => 'Kantin Sehat',
            'outlet_code' => generateOutletCode(),
            'slug' => 'kantin-sehat',
            'address' => 'Jl. Raya',
            'phone' => '081234567890',
            'email' => 'kantinsehat@gmail.com',
            'logo' => generateOutletCode() . '.png',
            'status' => true,
        ]);
    }
}
