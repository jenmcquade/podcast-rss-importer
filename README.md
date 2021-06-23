## Laravel Podcast Importer

## What's Included

A local Laravel 8 Sail environment with
- MySQL 8.0
- Adminer 4.8.1

### Command Prompt Script

`app/Console/Commands/ImportPodcast.php`

### Podcast Importer Support Class

`app/Support/PodcastImporter.php`

## How to Run

1. Clone the repo: `git clone https://github.com/jenmcquade/podcast-rss-importer`
1. Change to project directory: `cd podcast-rss-importer`
1. Run Laravel Sail: `./vendor/bin/sail up`
1. Run Artisan Migrate: `./vendor/bin/sail artisan migrate`
1. Run RSS importer console command `./vendor/bin/sail artisan podcast:import`
1. Enter a valid Podcast RSS feed URL when prompted

You can also run the RSS importer console command by passing the Podcast RSS feed URL as an argument like this:
`./vendor/bin/sail artisan podcast:import http://your_rss_url.rss`

## How to verify the imported data
1. Open Adminer using http://localhost:8080
1. Enter the login information for the local database:
- Server: mysql
- Username: sail
- Password: password
- Database: rss_import
1. Click on the `podcasts` or `episodes` tables
1. Click `Select data` to see the data

## MySql Data

### podcasts

| Column        | Type         |
| --------------|-------------:|
| id            | bigint (AI)  |
| title         | text         |
| artwork_url   | text         |
| feed_url      | varchar(512) |
| description   | text         |
| language      | varchar(255) |
| website_url   | text         |
| created_at    | timestamp    |
| updated_at    | timestampe   |

### episodes
| Column        | Type         |
| --------------|-------------:|
| id            | bigint (AI)  |
| podcast_id    | int          |
| title         | text         |
| description   | text         |
| audio_url     | varchar(512) |
| created_at    | timestamp    |
| updated_at    | timestampe   |

## Included tests
A few sample feature tests are included.

Run `./vendor/bin/sail artisan test`

### Feature Tests

#### PodcastImporterTest.php

1. Can add podcast to database
1. Can add episodes to database
1. Podcast has episodes (Verify One to Many relationship)
1. Podcast can only be added once

#### Tested with
- https://www.omnycontent.com/d/playlist/2b465d4a-14ee-4fbe-a3c2-ac46009a2d5a/b1907157-de93-4ea2-a952-ac700085150f/-be1924e3-559d-4f7d-98e5-ac7000851521/podcast.rss
- https://nosleeppodcast.libsyn.com/rss
- https://www.omnycontent.com/d/playlist/aaea4e69-af51-495e-afc9-a9760146922b/43816ad6-9ef9-4bd5-9694-aadc001411b2/808b901f-5d31-4eb8-91a6-aadc001411c0/podcast.rss
- https://feeds.megaphone.fm/stuffyoushouldknow
- https://feeds.megaphone.fm/stuffyoumissedinhistoryclass
- https://www.omnycontent.com/d/playlist/aaea4e69-af51-495e-afc9-a9760146922b/d2c4e775-99ce-4c17-b04c-ac380133d68c/2c6993d0-eac8-4252-8c4e-ac380133d69a/podcast.rss
- https://feeds.megaphone.fm/VMP5705694065
- https://feeds.simplecast.com/54nAGcIl