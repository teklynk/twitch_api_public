<h2>Get Stream</h2>
<pre><code class="bash">
# Here is a curl example
curl -X GET 'http://server_url/getstream.php?channel=gogcom'
</code></pre>
<p>
    Make a GET call to the following url :<br>
    <code class="higlighted break-word">http://server_url/getstream.php</code>
</p>

<pre><code class="json">
Result example :

{
  "data": [
    {
      "id": "318797173500",
      "user_id": "43255859",
      "user_login": "gogcom",
      "user_name": "GOGcom",
      "game_id": "4120",
      "game_name": "Vampire: The Masquerade - Redemption",
      "type": "live",
      "title": "@ashsaidhi indulges vampiric activities in Vampire: The Masquerade - Redemption 50% off!",
      "viewer_count": 88,
      "started_at": "2025-03-31T18:02:26Z",
      "language": "en",
      "thumbnail_url": "https://static-cdn.jtvnw.net/previews-ttv/live_user_gogcom-{width}x{height}.jpg",
      "tag_ids": [
        
      ],
      "tags": [
        "GOG",
        "English",
        "DRMfree"
      ],
      "is_mature": false
    }
  ],
  "pagination": {
    "cursor": "eyJiIjp7IkN1cnNvciI6ImV5SnpJam80T0M0Mk5qRXdOekk1TlRBME9UZzBOU3dpWkNJNlptRnNjMlVzSW5RaU9uUnlkV1Y5In0sImEiOnsiQ3Vyc29yIjoiIn19"
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
        <td>channel</td>
        <td>String</td>
        <td>
            (required) Twitch channel name
        </td>
    </tr>
    <tr>
        <td>id</td>
        <td>Integer</td>
        <td>
            (optional) Twitch channel id
        </td>
    </tr>
    </tbody>
</table>