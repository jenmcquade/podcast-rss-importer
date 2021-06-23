<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use App\Support\PodcastImporter;
use App\Models\Podcast;
use App\Models\Episode;

class Support_PodcastImporterTest extends TestCase
{
    use DatabaseMigrations;

    public string $test_rss_url;
    public PodcastImporter $importer;
    public Podcast $podcast_db_data;

    public function setUp():void {
        parent::setUp();

        $this->test_rss_url = 'https://nosleeppodcast.libsyn.com/rss';

        $this->importer = new PodcastImporter( $this->test_rss_url );

        $this->importer->addPodcastToDb();

        $this->podcast_db_data = $this->importer->getDbPodcastData();
    }

    /**
     * Can add a Podcast RSS feed to the database
     *
     * @return void
     */
    public function test_can_add_podcast_to_database()
    {
        $podcast = Podcast::where('id', '=', '1')->first();

        $this->assertEquals('The NoSleep Podcast', $podcast->title);
    }

    /**
     * Can add a Podcast episode to the database
     * 
     * @return void
     */
    public function test_can_add_episodes_to_database()
    {
        $episode = Episode::where('podcast_id', '=', '1')->first();

        $this->assertNotEmpty($episode->audio_url);
    }

    /**
     * Verify One to Many relationship of Podcast to Episode
     * 
     * @return void
     */
    public function test_podcast_has_episodes() {
        $episode = Episode::where('podcast_id', '=', '1')->first();

        $this->assertEquals('The NoSleep Podcast', $episode->podcast->title);
    }

    /**
     * Verify Podcast can only be added once
     * 
     * @return void
     */
    public function test_podcast_can_only_be_added_once() {
        $this->importer->addPodcastToDb();
        $this->assertFalse($this->importer->is_success());
    }

}
