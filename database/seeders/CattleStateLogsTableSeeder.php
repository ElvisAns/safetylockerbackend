<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CattleStateLogs;

class CattleStateLogsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        CattleStateLogs::factory(50)->create();
    }
}
