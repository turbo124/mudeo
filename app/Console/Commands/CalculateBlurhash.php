<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Song;

class CalculateBlurhash extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mudeo:blurhash';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculate image blurhash';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $songs = Song::where('blurhash', '=', '')->orderBy('id')->get();

        foreach ($songs as $song) {
            $this->info('Song: ' . $song->title);
        }        
    }
}
