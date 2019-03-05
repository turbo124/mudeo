 <?php

use Illuminate\Database\Seeder;

class RandomDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

    $this->command->info('Running RandomDataSeeder');

        Eloquent::unguard();

        $faker = Faker\Factory::create();

        $user = factory(\App\Models\User::class)->create();

        $song = factory(\App\Models\Song::class)->create([
            'user_id' => $user->id,
        ]);

        

        $company = factory(\App\Models\Company::class)->create([
            'account_id' => $account->id,
        ]);

        $account->default_company_id = $company->id;
        $account->save();

        $user = factory(\App\Models\User::class)->create([
            'account_id' => $account->id,
            'confirmation_code' => $this->createDbHash(config('database.default'))
        ]);

    }

}