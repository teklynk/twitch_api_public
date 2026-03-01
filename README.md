# Twitch API Public Gateway

## Overview

This is a way to run your own Twitch API "gate-way" service that only requires the user name/channel name to pull data. It acts as a public gateway to Twitch's API. This is useful when creating your own Twitch tools/apps and just want to get data from Twitch without passing in your client id and auth token into your code and manually refreshing your auth token every 3 months. Auth token automatically refreshes on the server every day. All requests use GET to pull data. Nothing is posted back to Twitch and nothing is stored on the server. Once set up, getting data from Twitch is as simple as going to a URL and parsing the returned JSON string.

## Recent Updates

### June 2024
`getuserclips.php` can now be filtered by "is_featured". This will show clips that the channel has set as Featured.
Example: `https://example.com/getuserclips.php?channel=MrCoolStreamer&prefer_featured=true&limit=100`

### September 2023
Follows and Following endpoint now require a user access token and client ID that includes the `user:read:follows` and/or `moderator:read:followers` scope. This can be generated from twitchtokengenerator.com. The access token and client ID can then be used in the endpoint url: `https://example.com/getuserfollowing.php?channel=MrCoolStreamer&limit=100&ref=accessTokenXyz123Abc&clientId=abc123xyz5678`

The access token and client ID values need to be base_64 encoded.
- javascript: `btoa(stringToEncode);`
- php: `base64_encode(stringToEncode);`

The JSON format for "followers" and "followed" has also changed. Please refer to: Twitch API Reference

## Installation

### Option 1: Using Docker (Recommended)

This branch includes a `Dockerfile` and `docker-compose.yml` to easily run the application locally or on a server.

1.  **Clone the repository:**
    ```bash
    git clone <repository-url>
    cd twitch_api_public
    ```

2.  **Configure Environment:**
    Rename `sample.env` to `.env` and add your Twitch Client ID and Secret.
    ```bash
    cp sample.env .env
    ```
    *See Configuration below for details on getting Twitch credentials.*

3.  **Build and Run:**
    ```bash
    docker-compose up -d --build
    ```
    This will start the Nginx, PHP-FPM, and Memcached containers.

4.  **Access the API:**
    The API should now be accessible at `http://localhost:8080` (or your server's IP).

**Docker Notes:**
- **Stopping:** To stop the containers, run `docker-compose down`.

### Option 2: Manual Installation

If you prefer not to use Docker, you can run this on a standard LAMP/LEMP stack.

#### 1. Prerequisites
- Linux server (Ubuntu/Debian recommended)
- Nginx or Apache
- PHP 8.1+
- Composer
- Memcached

#### 2. Install Dependencies (Ubuntu Example)

**Install PHP and Extensions:**
```bash
sudo apt update
sudo apt install -y php-fpm php-curl php-xml php-mbstring
```

**Install Memcached:**
```bash
sudo apt install -y memcached php-memcached libmemcached-dev
sudo service memcached start
```

**Install Composer:**
```bash
curl -sS https://getcomposer.org/installer -o composer-setup.php
sudo php composer-setup.php --install-dir=/usr/local/bin --filename=composer
rm composer-setup.php
```

#### 3. Project Setup

1.  **Clone the repository:**
    ```bash
    cd /var/www/html
    git clone <repository-url>
    cd twitch_api_public
    ```

2.  **Install PHP Packages:**
    ```bash
    composer install
    ```

3.  **Configure Environment:**
    ```bash
    cp sample.env .env
    ```
    Edit `.env` and add your Twitch credentials.

#### 4. Web Server Configuration

Set the web site's root directory in the nginx/apache config to `/var/www/html/twitch_api_public/public`.

**NGINX Config Example:**
```nginx
server {
    server_name    example.com;
    root           /var/www/html/twitch_api_public/public;
    index          index.php;

    add_header Access-Control-Allow-Origin *;

    # Deny access to . files, for security
    location ~ /\. {
      log_not_found off;
      deny all;
    }

    location / {
      try_files $uri $uri/ =404;
    }

    location ~* \.php$ {
      fastcgi_pass unix:/run/php/php8.1-fpm.sock; # Adjust version as needed
      include         fastcgi_params;
      fastcgi_param   SCRIPT_FILENAME    $document_root$fastcgi_script_name;
      fastcgi_param   SCRIPT_NAME        $fastcgi_script_name;
    }

    listen 80;
    listen [::]:80;
}
```

## APACHE Config Example
```apache
<VirtualHost *:80>
   DocumentRoot /var/www/html/twitch_api_public/public
   ServerName example.com;

   ErrorLog ${APACHE_LOG_DIR}/error.log
   CustomLog ${APACHE_LOG_DIR}/access.log combined

   <Directory "/var/www/html/twitch_api_public/public">
        Options Indexes FollowSymLinks MultiViews
        AllowOverride All
        Require all granted
   
        DirectoryIndex index.php
        RewriteEngine On

        RewriteCond %{REQUEST_FILENAME} !-d
        RewriteCond %{REQUEST_FILENAME} !-f
        RewriteRule ^[^.]+$ index.php [L]
   </Directory>
   
</VirtualHost>
```

## Instructions and Notes

- Visit https://dev.twitch.tv/ to register your application. 
- On the dev.twitch.tv site, click "Your Console" in the upper right. Under "Applications" click "Register Your Application". 
- Give your Application a Name.
- OAuth Redirect URLs. When testing locally, you can set this to http://localhost. I like to add localhost and my public domain name entry. This will allow your domain(s) access to the Twitch API. (These domains with this OAuth token and client ID are allowed to access the Twitch API)
- Select Category > Chat Bot.
- Add your Twitch client ID and Twitch secret to the .env file.

These files are needed to generate your Twitch oAuth token.

## Getting data

Requests are returned in JSON format so that you can parse the data as needed. Some requests require a limit parameter in the url and have a max limit of 100.

## Example Requests

Pagination is possible with &after=cursor_value and &before=cursor_value
You can get the cursor value from the first request.
```json
"pagination": {
  "cursor": "eyJiIjpudWxsLCJhIjp7IkN1cnNvciI6Ik1UQXkifX0"
}
```
Example: 
`https://example.com/getuserfollows.php?channel=MrCoolStreamer&limit=100&after=eyJiIjpudWxsLCJhIjp7IkN1cnNvciI6Ik1UQXkifX0`
will pull the next 100 follows.

*Pull a single Random clip with: `&random=true`. Set the `count=3` value to limit how many random clips are returned. If not set, then only 1 random clip is returned.

Example: `https://example.com/getuserclips.php?channel=MrCoolStreamer&limit=100&random=true&count=3`

*Pull a single clip by its ID: id=DelightfulSuaveMacaroniNerfRedBlaster-2Z8TW9kD4d7jN_uy

Example: `https://example.com/getuserclips.php?id=DelightfulSuaveMacaroniNerfRedBlaster-2Z8TW9kD4d7jN_uy`

*Pull only featured clips

Example: `https://example.com/getuserclips.php?channel=MrCoolStreamer&prefer_featured=true&limit=100`

*Ignore / skip newer Twitch clip URLs. `&ignore=new`


## End points examples:

`https://example.com/getuserstatus.php?channel=MrCoolStreamer`

`https://example.com/getuserinfo.php?channel=MrCoolStreamer`

`https://example.com/getstream.php?channel=MrCoolStreamer`

`https://example.com/getuserfollows.php?channel=MrCoolStreamer&limit=100&ref=accesstokenxyz123&clientId=abc123xyz5678`

`https://example.com/getuserfollowing.php?channel=MrCoolStreamer&limit=100&ref=accesstokenxyz123&clientId=abc123xyz5678`

`https://example.com/getuseremotes.php?channel=MrCoolStreamer&limit=100`

`https://example.com/getglobalemotes.php`

`https://example.com/getuserclips.php?channel=MrCoolStreamer&limit=100`

`https://example.com/getuserclips.php?channel=MrCoolStreamer&limit=100&start_date=2023-02-15T00:00:00Z&end_date=2023-02-24T00:00:00Z&creator_name=MrCoolStreamer`

`https://example.com/getuserclips.php?channel=MrCoolStreamer&prefer_featured=true&limit=100`

`https://example.com/getviewers.php?channel=MrCoolStreamer`

`https://example.com/getgame.php?id=23123`

`https://example.com/getuserschedule.php?channel=MrCoolStreamer`

`https://example.com/getuserschedule.php?channel=MrCoolStreamer&ical=true` - returns .ics download file that can imported into a calendar client.

`https://example.com/getuserschedule.php?channel=MrCoolStreamer&html=true&format=0&limit=30` - returns html view (format=1 is an alternate date/time format). Event dates and times have been converted to your local time zone. This could be used as a OBS browser source or embedded as an iframe on a website.

`https://example.com/getbttvemotes.php?channel=MrCoolStreamer`

**Most endpoints can use 'id' instead of 'channel'. Examples: `https://example.com/getuserinfo.php?id=55184769`, `https://example.com/getuserstatus.php?id=55184769`, `https://example.com/getuserschedule.php?id=55184769`**

jQuery Ajax Example:

```javascript
let channel = "MrCoolStreamer";
$.ajax({url: "https://example.com/getuserinfo.php?channel=" + channel, success: function(result) {
	console.log(result);
}});

// Example2: Json data - Ajax call
let clips_json = JSON.parse($.getJSON({
	'url': "https://example.com/getuserclips.php?channel=" + channel + "&limit=100",
	'async': false
}).responseText);

console.log(clips_json.data[0]['thumbnail_url']);
```

JavaScript Example:

```javascript
let getUserInfo = function (channel) {
    let url = "https://example.com/getuserinfo.php?channel=" + channel;
    return fetch(url)
        .then(response => response.json());
};

getUserInfo("MrCoolStreamer").then(result => {
	console.log(result);
});
```

CURL Example:

```bash
curl -X GET 'https://example.com/getuserinfo.php?channel=MrCoolStreamer'
```

PHP using CURL Example:

```php
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://example.com/getuserinfo.php?channel=MrCoolStreamer");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$result = curl_exec($ch);
curl_close($ch);
var_dump($result);
```

Example Responses:

```json
{
  "data": [
    {
      "id": "141981764",
      "login": "mrcoolstreamer",
      "display_name": "MrCoolStreamer",
      "type": "",
      "broadcaster_type": "partner",
      "description": "Supporting third-party developers building Twitch integrations from chatbots to game integrations.",
      "profile_image_url": "https://static-cdn.jtvnw.net/jtv_user_pictures/8a6381c7-d0c0-4576-b179-38bd5ce1d6af-profile_image-300x300.png",
      "offline_image_url": "https://static-cdn.jtvnw.net/jtv_user_pictures/3f13ab61-ec78-4fe6-8481-8682cb3b0ac2-channel_offline_image-1920x1080.png",
      "view_count": 5980557,
      "created_at": "2016-12-14T20:32:28Z"
    }
  ]
}
```
## Clips
getuserclips.php

```json
{
  "data": [
    {
      "item": 1,
      "id": "CrispyJollyGullHassaanChop-nPlLKGxGRcBj37e4",
      "url": "https://www.twitch.tv/twitch/clip/CrispyJollyGullHassaanChop-nPlLKGxGRcBj37e4",
      "embed_url": "https://clips.twitch.tv/embed?clip=CrispyJollyGullHassaanChop-nPlLKGxGRcBj37e4",
      "broadcaster_id": "12826",
      "broadcaster_name": "Twitch",
      "creator_id": "932351392",
      "creator_name": "power_tester",
      "video_id": "",
      "game_id": "",
      "language": "en",
      "title": "F1 2017 E3 Gameplay!",
      "view_count": 4181052,
      "created_at": "2023-07-12T01:07:36Z",
      "thumbnail_url": "https://static-cdn.jtvnw.net/twitch-clips/ErNyUZz5SyhsRkXAY9-3uA/AT-cm%7CErNyUZz5SyhsRkXAY9-3uA-preview-480x272.jpg",
      "duration": 59.9,
      "vod_offset": null,
      "is_featured": false,
      "clip_url": "https://production.assets.clips.twitchcdn.net/ErNyUZz5SyhsRkXAY9-3uA/AT-cm%7CErNyUZz5SyhsRkXAY9-3uA.mp4?sig=8f18716f777aa3f0061754dadeae7cb85c50bf21&token=%7B%22authorization%22%3A%7B%22forbidden%22%3Afalse%2C%22reason%22%3A%22%22%7D%2C%22clip_uri%22%3A%22https%3A%2F%2Fproduction.assets.clips.twitchcdn.net%2FErNyUZz5SyhsRkXAY9-3uA%2FAT-cm%257CErNyUZz5SyhsRkXAY9-3uA.mp4%22%2C%22clip_slug%22%3A%22CrispyJollyGullHassaanChop-nPlLKGxGRcBj37e4%22%2C%22device_id%22%3Anull%2C%22expires%22%3A1772459550%2C%22user_id%22%3A%22%22%2C%22version%22%3A3%7D"
    }
  ]
}
```