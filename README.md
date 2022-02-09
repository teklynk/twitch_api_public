## What is this?

This is a way to run your own Twitch API service that only requires the user name/channel name to pull data. No need to first get the user id before making other requests. This is useful when creating your own Twitch tools/apps and just want to get data from Twitch without passing in your client id, auth token into your code and manually refreshing your auth token every 3 months. Auth token refreshes on the server every day. All requests use GET to pull data. Nothing is posted back to Twitch.

## Requirements

Linux server running nginx, php, php-fpm, curl. No Database needed.

Set the web sites root directory in the nginx config to /var/www/html/twitch_api_public/public and not the entire /var/www/html directory.

If you want to use a Docker container, I recommend https://hub.docker.com/r/trafex/php-nginx/. It has Nginx and PHP configured and ready to go. Just modify the default nginx.config with the root path pointing to "root /var/www/html/twitch_api_public/public" and "server_name example.com". Add your files to /var/www/html/twitch_api_public/. Set permissions to (nobody).

If running this on a public server, I recommend using Cloudflare for its Proxy, DDOS, Firewall and Rate-Limiting features.


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

- Rename config/sample.auth to .auth

- Rename config/sample.client to .client

- Rename config/sample.domain to .domain

- Rename config/sample.secret to .secret
- Rename config/sample.config.php to config.php

Visit https://dev.twitch.tv/ to register your application. 

Select Category > Chat Bot and add your applications domain/OAuth Redirect URLs.

- Add your Twitch client ID to the .client file.

- Add your Twitch secret to the .secret file.

These files are needed to generate your Twitch oAuth token.

## Getting data

Requests are returned in JSON format so that you can parse the data as needed. Some requests require a limit parameter in the url and has a max limit of 100.

**Requests:**

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
	$("#div1").html(result.data[0]['display_name']);
}});
```

JavaScript Example:

```javascript
let getUserInfo = function (channel, callback) {
    let url = "https://example.com/getuserinfo.php?channel=MrCoolStreamer";
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

getUserInfo(getChannel, function (result) {
    document.getElementById("div1").innerHTML = "<span class='user-name'>" + result.data[0]['display_name'] + "</span>"
});
```

CURL:

```bash
curl -X GET 'https://example.com/getuserinfo.php?channel=MrCoolStreamer'
```

PHP using CURL Example:

```php
curl_setopt($ch, CURLOPT_URL, "https://example.com/getuserinfo.php?channel=MrCoolStreamer";
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$result = curl_exec($ch);
echo "<div id='div1'>" . $result.data[0]['display_name'] . "</div>";
```