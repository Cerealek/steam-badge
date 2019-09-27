# steam-badge
Steam badge script for displaying your activity on web

## Installation

1. Clone the repository
2. Run `composer update` on the cloned folder to install Steam API client

## Setup

### 1. Get your Steam ID
Enter your Steam Profile URL at `https://steamid.xyz/`

The script requires the "Steam64 ID" value.

### 2. Get your Steam API key

Fill out the form at `https://steamcommunity.com/dev/apikey` (available only after login) and copy the API key.


### Set the Steam ID and API key in index.php:
`define('API_KEY', 'FOO'); // fill in your API key`
`define('STEAM_ID', 12345678901234567); // fill in your STEAM ID``

### That's it.
Now, you can simply run the index.php, and you should see your Steam badge displaying your current status, and recent activity, very much like it's displayed in your profile.
