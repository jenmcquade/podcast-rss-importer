<?php

namespace App\Console\Commands;

use App\Support\PodcastImporter;
use Illuminate\Console\Command;

class ImportPodcast extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'podcast:import {url? : The URL of the Podcast RSS feed}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import a Podcast from an RSS feed URL.';

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
     * @return int
     */
    public function handle(): int
    {
        /**
         * Prompt for feed url
         */
        $url_regex = '/^(https?:\/\/)([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/';
        $url_is_set = preg_match($url_regex, $this->argument('url')) ? true : false;
        $url = $this->argument('url') === '' ? '' : $this->argument('url');
        while(false == $url_is_set) {
            $url = $this->ask('What is the RSS feed URL?');
            if($url !== null && preg_match($url_regex, $url)) {
                $url_is_set = true;
            } else {
                $this->line('Invalid URL format.');
            };
        }

        $this->line('Verifying the Podcast ...');

        try {
            $importer = new PodcastImporter( $url );
            $this->line('Podcast ' . $importer->getData()['channel']->title . ' has been found.');
        } catch (\Exception $e) {
            $this->error($e->getMessage());
            return 1;
        }

        $episode_count = count($importer->getData()['channel']->item);

        $importer->setConsoleStatusBar($this->output->createProgressBar($episode_count));

        $importer->startConsoleStatusBar();

        try {
            $success = $importer->addPodcastToDb();
        } catch (\Exception $e) {
            $this->error('Oops! Something went wrong: ' . $e->getMessage());
            return 1;
        }

        $importer->finishConsoleStatusBar();

        $results = explode('\n', $importer->getResult());
        foreach($results as $result) {
            $this->line($result);
        }

        if($success) {
            return 0;
        } else {
            return 1;
        }
    }
}
