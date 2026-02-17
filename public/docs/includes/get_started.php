<h1>Get started</h1>
<p>
    This is a way to run your own Twitch API "gate-way" service that only requires the user name/channel name to pull data. It acts as a public gateway to Twitch's API. This is useful when creating your own Twitch tools/apps and just want to get data from Twitch without passing in your client id and auth token into your code and manually refreshing your auth token every 3 months. Auth token automatically refreshes on the server every day. All requests use GET to pull data. Nothing is posted back to Twitch and nothing is stored on the server. Once set up, getting data from Twitch is as simple as going to a URL and parsing the returned JSON string.
</p>
    
<h2>Requirements</h2>
<ul>
    <li>PHP 7.4 or higher</li>
    <li>Composer</li>
    <li>nginx or Apache web server</li>
</ul>

<h2>Installation</h2>
<ol>
    <li>Clone the repository to your server.</li>
    <li>Install php composer. <a href="https://getcomposer.org/download/" target="_blank">Installation instructions</a>.</li>
    <li>Run `composer install` in the root directory of the cloned repository.</li>
    <li>Set up a virtual host for your domain/subdomain. Review the nginx.conf file as an example.</li>
    <li>Rename `sample.env` to `.env`</li>
    <li>Edit the `.env` file with your Twitch client id and auth token.</li>
    <li>Create file `.auth` inside the `config` directory. Example: `config/.auth`. Make sure that the `.auth` file has write permissions.</li>
</ol>

<h2>(Optional) Install Using Docker</h2>
<ol>
    <li>Install docker and docker-compose</li>
    <li>Clone the repository to your server or local machine.</li>
    <li>Rename `sample.env` to `.env`</li>
    <li>Edit the `.env` file with your Twitch client id and auth token.</li>
    <li>Create file `.auth` inside the `config` directory. Example: `config/.auth`. Make sure that the `.auth` file has write permissions.</li>
    <li>docker-compose build</li>
    <li>docker-compose up -d</li>
    <li>The `docker-compose.yml` will create 3 containers (php-fpm, composer, nginx)</li>
    <li>You should now be able to access the application by visiting http://localhost:8080 in your web browser.</li>
</ol>