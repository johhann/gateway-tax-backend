<?php

namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Database\Seeder;

class BranchesEfinSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $branchesEfin = [
            '64042' => '950757',
            '64762' => '953760',
            '64763' => '950206',
            '64764' => '953337',
            '64765' => '953096',
            '64776' => '953760',
            '64767' => '953417',
            '64768' => '331296',
            '64774' => '307870',
            '64770' => '302178',
            '63263' => '001050',
            '64775' => '330676',
        ];

        foreach ($branchesEfin as $branchId => $efin) {
            Branch::where('id', $branchId)
                ->update(['efin' => $efin]);
        }
    }
}
