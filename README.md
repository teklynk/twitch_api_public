## What is this?

This is a way to run your own Twitch API "gate-way" service that only requires the user name/channel name to pull data. It acts as a public gateway to Twitch's API. This is useful when creating your own Twitch tools/apps and just want to get data from Twitch without passing in your client id and auth token into your code and manually refreshing your auth token every 3 months. Auth token automatically refreshes on the server every day. All requests use GET to pull data. Nothing is posted back to Twitch and nothing is stored on the server. Once set up, getting data from Twitch is as simple as going to a URL and parsing the returned JSON string.

## Requirements

Linux server running nginx, php, php-fpm, curl. No Database needed.

Set the web sites root directory in the nginx config to /var/www/html/twitch_api_public/public and not the entire /var/www/html directory.

If you want to use a Docker container, I recommend https://hub.docker.com/r/trafex/php-nginx/. It has Nginx and PHP configured and ready to go. Just modify the default nginx.config with the root path pointing to "root /var/www/html/twitch_api_public/public" and "server_name example.com". Add your files to /var/www/html/twitch_api_public/. Set permissions to (nobody).


If running this on a public server, I recommend using [Cloudflare](https://www.cloudflare.com/) for its Proxy, DDoS, Firewall and Rate-Limiting features.

## NEW (June 2024)
Getuserclips.php can now be filtered by "is_featured". This will show clips that the channel has set as Fetured.

https://example.com/getuserclips.php?channel=MrCoolStreamer&prefer_featured=true&limit=100

## NEW (September 2023)

Follows and Following endpoint now require a user access token and client ID that includes the user:read:follows and/or moderator:read:followers scope. This can be generated from [twitchtokengenerator.com](https://twitchtokengenerator.com/). The access token and client ID can then be used in the endpoint url: https://example.com/getuserfollowing.php?channel=MrCoolStreamer&limit=100&ref=accessTokenXyz123Abc&clientId=abc123xyz5678 

The access token and client ID values need to be base_64 encoded.
- javascript: btoa(stringToEncode);
- php: base64_encode(stringToEncode);

The JSON format for "followers" and "followed" has also changed. Please refer to: [https://dev.twitch.tv/docs/api/reference/#get-followed-channels](https://dev.twitch.tv/docs/api/reference/#get-followed-channels)

## NGINX Config Example
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
      fastcgi_pass unix:/run/php/php7.4-fpm.sock;
      include         fastcgi_params;
      fastcgi_param   SCRIPT_FILENAME    $document_root$fastcgi_script_name;
      fastcgi_param   SCRIPT_NAME        $fastcgi_script_name;
    }

    listen [::]:443 ssl ipv6only=on; # managed by Certbot
    listen 443 ssl; # managed by Certbot
    ssl_certificate /etc/letsencrypt/live/example.com/fullchain.pem; # managed by Certbot
    ssl_certificate_key /etc/letsencrypt/live/example.com/privkey.pem; # managed by Certbot
    include /etc/letsencrypt/options-ssl-nginx.conf; # managed by Certbot
    ssl_dhparam /etc/letsencrypt/ssl-dhparams.pem; # managed by Certbot

}

server {
    if ($host = example.com) {
        return 301 https://$host$request_uri;
    } # managed by Certbot

    listen         80;
    listen         [::]:80;
    server_name    example.com;
    return 404; # managed by Certbot
}
```

## APACHE Config Example
```apache
<VirtualHost *:443>
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
   
   SSLEngine on

   SSLCertificateFile /etc/apache2/certificate/apache2.crt
   SSLCertificateKeyFile /etc/apache2/certificate/private.key
</VirtualHost>
```

## Instructions and Notes

- **Rename** config/sample.auth to .auth

- **Rename** config/sample.client to .client

- **Rename** config/sample.secret to .secret

- Visit https://dev.twitch.tv/ to register your application. 
- On the dev.twitch.tv site, click "Your Console" in the upper right. Under "Applications" click "Register Your Application". 
- Give your Application a Name.
- OAuth Redirect URLs. When testing locally, you can set this to http://localhost. I like to add localhost and my public domain name entry. This will allow your domain(s) access to the Twitch API. (These domains with this OAuth token and client ID are allowed to access the Twitch API)
- Select Category > Chat Bot.

- Add your Twitch client ID to the .client file.

- Add your Twitch secret to the .secret file.

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
Example: https://example.com/getuserfollows.php?channel=MrCoolStreamer&limit=100&after=eyJiIjpudWxsLCJhIjp7IkN1cnNvciI6Ik1UQXkifX0
will pull the next 100 follows.

*Pull a single Random clip with: &random=true

Example: https://example.com/getuserclips.php?channel=MrCoolStreamer&limit=100&random=true

*Pull a single clip by its ID: id=DelightfulSuaveMacaroniNerfRedBlaster-2Z8TW9kD4d7jN_uy

Example: https://example.com/getuserclips.php?id=DelightfulSuaveMacaroniNerfRedBlaster-2Z8TW9kD4d7jN_uy

*Pull only featured clips

Example: https://example.com/getuserclips.php?channel=MrCoolStreamer&prefer_featured=true&limit=100


## End points examples:

https://example.com/getuserstatus.php?channel=MrCoolStreamer

https://example.com/getuserinfo.php?channel=MrCoolStreamer

https://example.com/getstream.php?channel=MrCoolStreamer

https://example.com/getuserfollows.php?channel=MrCoolStreamer&limit=100&ref=accesstokenxyz123&clientId=abc123xyz5678

https://example.com/getuserfollowing.php?channel=MrCoolStreamer&limit=100&ref=accesstokenxyz123&clientId=abc123xyz5678

https://example.com/getuseremotes.php?channel=MrCoolStreamer&limit=100

https://example.com/getglobalemotes.php

https://example.com/getuserclips.php?channel=MrCoolStreamer&limit=100

https://example.com/getuserclips.php?channel=MrCoolStreamer&limit=100&start_date=2023-02-15T00:00:00Z&end_date=2023-02-24T00:00:00Z&creator_name=MrCoolStreamer

https://example.com/getuserclips.php?channel=MrCoolStreamer&prefer_featured=true&limit=100

https://example.com/getviewers.php?channel=MrCoolStreamer

https://example.com/getgame.php?id=23123

https://example.com/getuserschedule.php?channel=MrCoolStreamer

https://example.com/getuserschedule.php?channel=MrCoolStreamer&ical=true - returns .ics download file that can imported into a calendar client.

https://example.com/getuserschedule.php?channel=MrCoolStreamer&html=true&format=0&limit=30 - returns html view (format=1 is an alternate date/time format). Event dates and times have been converted to your local time zone. This could be used as a OBS browser source or embedded as an iframe on a website.

https://example.com/getbttvemotes.php?channel=MrCoolStreamer

**Most endpoints can use 'id' instead of 'channel'. Examples: https://example.com/getuserinfo.php?id=55184769, https://example.com/getuserstatus.php?id=55184769, https://example.com/getuserschedule.php?id=55184769**

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
let getUserInfo = function (channel, callback) {
    let url = "https://example.com/getuserinfo.php?channel=" + channel;
    let xhr = new XMLHttpRequest();
    xhr.open("GET", url);
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4) {
            callback(JSON.parse(xhr.responseText));
            return true;
        } else {
            return false;
        }
    };
    xhr.send();
};

getUserInfo("MrCoolStreamer", function (result) {
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
      "item": 22,
      "id": "VictoriousAwkwardPheasantKevinTurtle-xT8tH7fW0oU0vZ8gT4",
      "url": "https://clips.twitch.tv/VictoriousAwkwardPheasantKevinTurtle-xT8tH7fW0oU0vZ8gT4",
      "embed_url": "https://clips.twitch.tv/embed?clip=VictoriousAwkwardPheasantKevinTurtle-xT8tH7fW0oU0vZ8gT4",
      "broadcaster_id": "159805577",
      "broadcaster_name": "Teklynk",
      "creator_id": "141981764",
      "creator_name": "MrCoolStreamer",
      "video_id": "",
      "game_id": "509670",
      "language": "en",
      "title": "I don't know what is happening",
      "view_count": 1,
      "created_at": "2022-08-08T17:33:04Z",
      "thumbnail_url": "https://clips-media-assets2.twitch.tv/_tduXpTTRFVsBuTb_XYZABC/vod-1543945678-offset-4866-preview-480x272.jpg",
      "duration": 30,
      "vod_offset": null,
      "clip_url": "https://clips-media-assets2.twitch.tv/_tduXpTTRFVsBuTb_XYZABC/vod-1543945678-offset-4866.mp4"
    }
  ]
}
```

## BetterTTV Emotes
getbttvemotes.php
```json
[
  {
    "id": "636ff60fb9076d0aaebbcf7c",
    "code": "Tekbot"
  },
  {
    "id": "5ba6d5ba6ee0c23989d52b10",
    "code": "bongoTap"
  },
  {
    "id": "5a6edb51f730010d194bdd46",
    "code": "PepoDance"
  },
  {
    "id": "5d922afbc0652668c9e52ead",
    "code": "peepoArrive"
  },
  {
    "id": "59f06613ba7cdd47e9a4cad2",
    "code": "PartyParrot"
  },
  {
    "id": "5c3427a55752683d16e409d1",
    "code": "peepoPooPoo"
  },
  {
    "id": "5bc7ff14664a3b079648dd66",
    "code": "peepoRun"
  },
  {
    "id": "5df2d1b7e7df1277b6070b1e",
    "code": "pepeJAM"
  },
  {
    "id": "5f21e57a65fe924464eecf0e",
    "code": "catRAVE"
  },
  {
    "id": "54fa8f1401e468494b85b537",
    "code": ":tf:"
  }
]
```
BTTV Emote URL: https://cdn.betterttv.net/emote/5a970ab2122e4331029f0d7e/3x