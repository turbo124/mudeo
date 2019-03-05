<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
	/**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('Running DatabaseSeeder');

        Eloquent::unguard();

        $this->call('RandomDataSeeder');

    }
}
