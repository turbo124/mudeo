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
        $user2 = factory(\App\Models\User::class)->create();

        $tags = factory(\App\Models\Tag::class,20)->create();


        $songs = factory(\App\Models\Song::class,100)->create([
            'user_id' => $user->id,
        ])->each(function ($song) use ($user, $user2, $tags){

            $videos = factory(\App\Models\Video::class,3)->create([
                'user_id' => $user->id,
            ]);

                $song->videos()->sync($videos);
            
                $song->tags()->sync($tags);


            $song_comments = factory(\App\Models\SongComment::class,5)->create([
                'user_id' => $user->id,
                'song_id' => $song->id,
            ]);

            $song_comments = factory(\App\Models\SongComment::class,5)->create([
                'user_id' => $user2->id,
                'song_id' => $song->id,
            ]);

        });

        
    }

}