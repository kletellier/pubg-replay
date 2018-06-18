<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
 
use Illuminate\Filesystem\Filesystem;
use App\Classes\DirectoryCleaner;

class FilesPurge extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'files:purge {{--with-replays}}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Purge old telemetry and replay files';

    private $directorycleaner;

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
        $this->info("Start process");
        $this->info("Purge old telemetry files");

        $filesystem = new Filesystem();
        $this->directorycleaner = new DirectoryCleaner($filesystem);

        $with_replay = $this->option("with-replays");

        $directories = array();
        $path_telemetry = storage_path("app/telemetry");
        $path_replay = storage_path("app/replay");

        $directories[] = $path_telemetry;
        if($with_replay)
        {
            $directories[] = $path_replay;
        }

        foreach ($directories as $directory) {
            $this->purgeDirectory($directory);
        }
        $this->info("End process");
    }

    private function purgeDirectory($directory)
    {
        $this->info("Purge folder " . $directory);
        $this->directorycleaner->setDirectory($directory);
        $ret = $this->directorycleaner->deleteFilesOlderThanMinutes(24*60);

        $nb = $ret->count();
        $this->info($nb . " file(s) deleted");
    }
}
