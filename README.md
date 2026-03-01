# Twitch API Public Gateway

## Overview

This is a way to run your own Twitch API "gate-way" service that only requires the user name/channel name to pull data. It acts as a public gateway to Twitch's API. This is useful when creating your own Twitch tools/apps and just want to get data from Twitch without passing in your client id and auth token into your code and manually refreshing your auth token every 3 months. Auth token automatically refreshes on the server every day. All requests use GET to pull data. Nothing is posted back to Twitch and nothing is stored on the server. Once set up, getting data from Twitch is as simple as going to a URL and parsing the returned JSON string.

## Installation

### Option 1: Using Docker (Recommended)

This branch includes a `Dockerfile` and `docker-compose.yml` to easily run the application locally or on a server.

1.  **Clone the repository:**
    ```bash
    git clone https://github.com/teklynk/twitch_api_public.git
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
- Nginx
- PHP 8.1+
- Composer
- Memcached

#### 2. Install Dependencies (Ubuntu Example)

**Install PHP and Extensions:**
```bash
sudo apt update
sudo apt install -y nginx php-fpm php-curl php-xml php-mbstring
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
    listen 80;
    server_name example.com;
    root /var/www/html/twitch_api_public/public;
    index index.php;

    add_header Access-Control-Allow-Origin *;

    access_log /var/log/nginx/access.log combined;
    error_log /var/log/nginx/error.log;

    # Deny access to . files, for security
    location ~ /\. {
      log_not_found off;
      deny all;
    }

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~* \.php$ {
      fastcgi_pass unix:/run/php/php8.4-fpm.sock; # Adjust version as needed
      include         fastcgi_params;
      fastcgi_param   SCRIPT_FILENAME    $document_root$fastcgi_script_name;
      fastcgi_param   SCRIPT_NAME        $fastcgi_script_name;
    }
}
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

## Documentation
[API Documentation](https://twitchapi.teklynk.com/docs/)