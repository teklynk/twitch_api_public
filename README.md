## More documentation and instructions coming soon!


## Requirements

Linux server running Nginx, PHP, PHP-FPM, cUrl

Set the web sites root directory in the nginx condig to /var/www/html/twitch_api/public and not the entire /var/www/html directory

## NGINX Config Example for HTTPS/SSL Only
```
server {
    listen 443;
    root /var/www/html/twitch_api_public/public;
    index index.php;
    server_name example.com;

    root /var/www/html/twitch_api/public;
    index index.php;

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

