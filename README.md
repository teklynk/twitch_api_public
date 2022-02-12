## What is this?

This is a way to run your own Twitch API "gate-way" service that only requires the user name/channel name to pull data. It acts as a public gateway to Twitch's API. This is useful when creating your own Twitch tools/apps and just want to get data from Twitch without passing in your client id and auth token into your code and manually refreshing your auth token every 3 months. Auth token automatically refreshes on the server every day. All requests use GET to pull data. Nothing is posted back to Twitch and nothing is stored on the server. Once set up, getting data from Twitch is as simple as going to a URL and parsing the returned JSON string.

## Requirements

Linux server running nginx, php, php-fpm, curl. No Database needed.

Set the web sites root directory in the nginx config to /var/www/html/twitch_api_public/public and not the entire /var/www/html directory.

If you want to use a Docker container, I recommend https://hub.docker.com/r/trafex/php-nginx/. It has Nginx and PHP configured and ready to go. Just modify the default nginx.config with the root path pointing to "root /var/www/html/twitch_api_public/public" and "server_name example.com". Add your files to /var/www/html/twitch_api_public/. Set permissions to (nobody).

If running this on a public server, I recommend using [Cloudflare](https://www.cloudflare.com/) for its Proxy, DDoS, Firewall and Rate-Limiting features.


## NGINX Config Example
```nginx
server {
    listen 443;
    root /var/www/html/twitch_api_public/public;
    index index.php;
    server_name example.com;

    location / {
        # First attempt to serve request as file, then as directory, then fall back to index.php
        try_files $uri $uri/ /index.php?q=$uri&$args;
    }

    # Redirect server error pages to the static page /50x.html
    error_page 500 502 503 504 /50x.html;
    location = /50x.html {
        root /var/lib/nginx/html;
    }

    # Pass the PHP scripts to PHP-FPM listening on php-fpm.sock
    location ~ \.php$ {
        try_files $uri =404;
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        fastcgi_pass unix:/run/php-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_param SCRIPT_NAME $fastcgi_script_name;
        fastcgi_index index.php;
        include fastcgi_params;
    }

    location ~* \.(jpg|jpeg|gif|png|css|js|ico|xml)$ {
        expires 5d;
    }

    # Deny access to . files, for security
    location ~ /\. {
        log_not_found off;
        deny all;
    }

    # Allow fpm ping and status from localhost
    location ~ ^/(fpm-status|fpm-ping)$ {
        access_log off;
        allow 127.0.0.1;
        deny all;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_pass unix:/run/php-fpm.sock;
    }
}
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

**Example Requests:**

https://example.com/getuserstatus.php?channel=MrCoolStreamer

https://example.com/getuserinfo.php?channel=MrCoolStreamer

https://example.com/getstream.php?channel=MrCoolStreamer

https://example.com/getuserfollows.php?channel=MrCoolStreamer&limit=100

https://example.com/getuserfollowing.php?channel=MrCoolStreamer&limit=100

https://example.com/getuseremotes.php?channel=MrCoolStreamer&limit=100

https://example.com/getglobalemotes.php

https://example.com/getuserclips.php?channel=MrCoolStreamer&limit=100

jQuery Ajax Example:

```javascript
$.ajax({url: "https://example.com/getuserinfo.php?channel=MrCoolStreamer", success: function(result) {
	console.log(result);
}});
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
	console.log(result.data[0]);
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

Example Response:

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

