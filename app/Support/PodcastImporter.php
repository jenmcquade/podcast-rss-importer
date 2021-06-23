<?php

namespace App\Support;

use App\Models\Podcast;
use App\Models\Episode;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Collection;
use Exception;

class PodcastImporter
{
  private string $url;
  private Collection $data;
  private string $result;
  private bool $success;
  private Podcast $dbPodcastData;
  private Array $dbEpisodeData;
  private $consoleStatusBar;

  public function __construct( string $url )
  {
    $this->url = $url;
    $this->data = $this->getRssFeedAsCollection();
    $this->result = '';
    $this->success = false;
    $this->dbPodcastData = new Podcast;
    $this->dbEpisodeData = array();
  }

  public function addPodcastToDb(): bool {
    $podcast = new Podcast;
    $podcast->title = $this->data['channel']->title;
    $podcast->artwork_url = $this->data['channel']->image->url;
    $podcast->feed_url = $this->url;
    $podcast->description = $this->data['channel']->description;
    $podcast->language = $this->data['channel']->language;
    $podcast->website_url = $this->data['channel']->link;

    try {
      $podcast->save();
      $this->dbPodcastData = $podcast;
    } catch(\Illuminate\Database\QueryException $e) {
      $this->success = false;
      $errorCode = $e->errorInfo[1];
      if($errorCode == 1062) {
        $this->result = 'The Podcast "' . $podcast->title . '" has already been added!';
      } else {
        $this->result .= $e->getMessage();
      }
      return $this->is_success();
    }

    foreach($this->data['channel']->item as $item) {
      $this->addEpisodeToDb($podcast->id, $item);
      if( isset($this->consoleStatusBar) ) {
        $this->consoleStatusBar->advance();
      }
    }

    $this->success = true;
    $this->result .= '\nThe Podcast "' . $podcast->title . '" has successfully been added.';
  
    return $this->is_success();
  }

  private function getRssFeedAsCollection(): Collection {
    $response = Http::get($this->url);
    if (!$response->successful()) {
      throw new Exception('Could not retrieve the RSS file from the provided URL.');
    }
    $objData = simplexml_load_string($response->body(), 'SimpleXMLElement', LIBXML_NOCDATA);
    return collect($objData);
  }

  private function addEpisodeToDb( int $podcastId, object $episodeData ): bool {
    $episode = new Episode;
    $episode->podcast_id = $podcastId;
    $episode->title = $episodeData->title;
    $episode->description = $episodeData->description;
    $episode->audio_url = $episodeData->enclosure['url'];
    try {
      $episode->save();
      $this->success = true;
      array_push($this->dbEpisodeData, $episode);
      $this->result .= '\nThe episode "' . $episode->title . '" has successfully been added.';
    } catch(\Illuminate\Database\QueryException $e) {
      $errorCode = $e->errorInfo[1];
      $this->success = false;
      if($errorCode == 1062) {
        $this->result .= '\nThe episode "' . $episode->title . '" has already been added!';
      } else {
        $this->result .= $e->getMessage();
      }
      return $this->is_success();
    }

    return $this->is_success();
  }

  public function is_success() {
    return $this->success;
  }

  public function getResult() {
    return $this->result;
  }

  public function getData() {
    return $this->data;
  }

  public function getDbPodcastData() {
    return $this->dbPodcastData;
  }

  public function getDbEpisodeData() {
    return $this->dbEpisodeData;
  }

  public function setConsoleStatusBar( $statusBar ) {
    $this->consoleStatusBar = $statusBar;
  }

  public function startConsoleStatusBar() {
    $this->consoleStatusBar->start();
  }

  public function finishConsoleStatusBar() {
    $this->consoleStatusBar->finish();
  }

}
