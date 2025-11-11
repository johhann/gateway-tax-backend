<?php

namespace Database\Seeders;

use Database\Factories\TaxRequestFactory;
use Illuminate\Database\Seeder;

class TaxRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TaxRequestFactory::new()->count(23)->create();
    }
}
