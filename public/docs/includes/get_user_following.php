<h2>Get User Follows</h2>
<pre><code class="bash">
# Here is a curl example
curl -X GET 'http://localhost:8080/getuserfollowing.php?channel=teklynk&limit=5&ref=cGZ2dGl8NzVwW5E7YWJsNDB9aXQwYTMwZWlwc3py&clientId=Y8VxbDF7b9Q0wWVhYmpyM1NuYzZhZGpuJSnDB0'
</code></pre>
<p>
    Make a GET call to the following url :<br>
    <code class="higlighted break-word">http://localhost:8080/getuserfollowing.php</code>
    <br>
    <span class="blue">* Requires your Twitch access token with a scope of <strong>user:read:follows</strong> and your Twitch client id.</span>

<pre><code class="json">
Result example :

{
  "total": 564,
  "data": [
    {
      "broadcaster_id": "1273402269",
      "broadcaster_login": "retromaggie",
      "broadcaster_name": "retromaggie",
      "followed_at": "2025-03-07T19:26:13Z"
    },
    {
      "broadcaster_id": "64914688",
      "broadcaster_login": "liquessen",
      "broadcaster_name": "Liquessen",
      "followed_at": "2025-02-24T16:10:17Z"
    },
    {
      "broadcaster_id": "406887039",
      "broadcaster_login": "toastiibear",
      "broadcaster_name": "ToastiiBear",
      "followed_at": "2025-02-24T09:08:47Z"
    },
    {
      "broadcaster_id": "119614545",
      "broadcaster_login": "mrbowlerhatlive",
      "broadcaster_name": "MrBowlerHatLive",
      "followed_at": "2025-02-24T05:50:28Z"
    },
    {
      "broadcaster_id": "412782875",
      "broadcaster_login": "ilija_supertzar",
      "broadcaster_name": "ilija_supertzar",
      "followed_at": "2025-02-23T03:54:31Z"
    }
  ],
  "pagination": {
    "cursor": "eyJiIjpudWxsLCJhIjp7IkN1cnNvciI6ImV5SjBjQ0k2SW5WelpYSTZOalV4TmpZNU5UWTZabTlzYkc5M2N5SXNJblJ6SWpvaWRYTmxjam8wTVRJM09ESTROelVpTENKcGNDSTZJblZ6WlhJNk5qVXhOalk1TlRZNlptOXNiRzkzY3lJc0ltbHpJam9pTVRjME1ESTRNamczTVRjNU1qUXdNakU0TVNKOSJ9fQ"
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