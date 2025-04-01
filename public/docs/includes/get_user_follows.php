<h2>Get User Follows</h2>
<pre><code class="bash">
# Here is a curl example
curl -X GET 'http://localhost:8080/getuserfollows.php?channel=teklynk&limit=5&ref=cGZ2dGl8NzVwW5E7YWJsNDB9aXQwYTMwZWlwc3py&clientId=Y8VxbDF7b9Q0wWVhYmpyM1NuYzZhZGpuJSnDB0'
</code></pre>
<p>
    Make a GET call to the following url :<br>
    <code class="higlighted break-word">http://localhost:8080/getuserfollows.php</code>
    <br>
    <span class="blue">* Requires your Twitch access token with a scope of <strong>moderator:read:followers</strong> and your Twitch client id.</span>

<pre><code class="json">
Result example :

{
  "total": 188,
  "data": [
    {
      "user_id": "28845797",
      "user_login": "zarstrum",
      "user_name": "Zarstrum",
      "followed_at": "2025-02-27T13:58:04Z"
    },
    {
      "user_id": "472548624",
      "user_login": "drunkula",
      "user_name": "Drunkula",
      "followed_at": "2025-01-05T20:59:38Z"
    },
    {
      "user_id": "654301306",
      "user_login": "nahumshalman",
      "user_name": "NahumShalman",
      "followed_at": "2024-12-20T15:34:08Z"
    },
    {
      "user_id": "48806156",
      "user_login": "snoozaya_",
      "user_name": "Snoozaya_",
      "followed_at": "2024-12-13T20:21:52Z"
    },
    {
      "user_id": "588952005",
      "user_login": "exlipse7",
      "user_name": "exlipse7",
      "followed_at": "2024-12-13T20:21:35Z"
    }
  ],
  "pagination": {
    "cursor": "eyJiIjpudWxsLCJhIjp7IkN1cnNvciI6ImV5SjBjQ0k2SW5WelpYSTZOVGc0T1RVeU1EQTFPbVp2Ykd4dmQzTWlMQ0owY3lJNkluVnpaWEk2TmpVeE5qWTVOVFlpTENKcGNDSTZJblZ6WlhJNk5qVXhOalk1TlRZNlptOXNiRzkzWldSZllua2lMQ0pwY3lJNklqRTNNelF4TWpFeU9UVTNOakF4TVRVM09UUWlmUT09In19"
  }
}
</code></pre>
<h4>QUERY PARAMETERS</h4>
<table class="central-overflow-x">
    <thead>
    <tr>
        <th>Field</th>
        <th>Type</th>
        <th>Description</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td>clientId</td>
        <td>String</td>
        <td>
            (required) Twitch client id (base64 encoded)
        </td>
    </tr>
    <tr>
        <td>ref</td>
        <td>String</td>
        <td>
            (required) Twitch auth token (base64 encoded)
        </td>
    </tr>
    <tr>
        <td>channel</td>
        <td>String</td>
        <td>
            (required) Twitch channel name
        </td>
    </tr>
    <tr>
        <td>limit</td>
        <td>Integer</td>
        <td>(optional - default: 100) A limit on the number of objects to be returned, between 1 and 100.</td>
    </tr>
    <tr>
        <td>before</td>
        <td>String</td>
        <td>pagination: cursor</td>
    </tr>
    <tr>
        <td>after</td>
        <td>String</td>
        <td>pagination: cursor</td>
    </tr>
    </tbody>
</table>