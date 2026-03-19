<h2>Get User Clips</h2>
<pre><code class="bash">
# Here is a curl example
curl -X GET 'http://localhost:8080/getuserclips.php?channel=twitch&limit=2&start_date=2023-02-15T00:00:00Z&end_date=2024-02-15T00:00:00Z&prefer_featured=false'
</code></pre>
<p>
    Make a GET call to the following url :<br>
    <code class="higlighted break-word">http://localhost:8080/getuserclips.php</code>
</p>

<pre><code class="json">
Result example :

{
  "data": [
    {
      "item": 1,
      "id": "CrispyJollyGullHassaanChop-nPlLKGxGRcBj37e4",
      "url": "https://clips.twitch.tv/CrispyJollyGullHassaanChop-nPlLKGxGRcBj37e4",
      "embed_url": "https://clips.twitch.tv/embed?clip=CrispyJollyGullHassaanChop-nPlLKGxGRcBj37e4",
      "broadcaster_id": "12826",
      "broadcaster_name": "Twitch",
      "creator_id": "932351392",
      "creator_name": "power_tester",
      "video_id": "152089018",
      "game_id": "",
      "language": "en",
      "title": "F1 2017 E3 Gameplay!",
      "view_count": 2828001,
      "created_at": "2023-07-12T01:07:36Z",
      "thumbnail_url": "https://static-cdn.jtvnw.net/twitch-clips/ErNyUZz5SyhsRkXAY9-3uA/AT-cm%7CErNyUZz5SyhsRkXAY9-3uA-preview-480x272.jpg",
      "duration": 59.9,
      "vod_offset": 228,
      "is_featured": false,
      "clip_url": "http://localhost:8080/getclipurl.php?id=CrispyJollyGullHassaanChop-nPlLKGxGRcBj37e4"
    },
    {
      "item": 2,
      "id": "VastOutstandingMinkNononoCat-hjFbXj-a8-WvyN0z",
      "url": "https://clips.twitch.tv/VastOutstandingMinkNononoCat-hjFbXj-a8-WvyN0z",
      "embed_url": "https://clips.twitch.tv/embed?clip=VastOutstandingMinkNononoCat-hjFbXj-a8-WvyN0z",
      "broadcaster_id": "12826",
      "broadcaster_name": "Twitch",
      "creator_id": "47622498",
      "creator_name": "Zekronz",
      "video_id": "1996381424",
      "game_id": "509658",
      "language": "en",
      "title": "CEO anwsers the real question.",
      "view_count": 105624,
      "created_at": "2023-12-06T02:28:09Z",
      "thumbnail_url": "https://static-cdn.jtvnw.net/twitch-clips/92BS8IYxW14_mvPlr8jdMw/AT-cm%7C92BS8IYxW14_mvPlr8jdMw-preview-480x272.jpg",
      "duration": 12.8,
      "vod_offset": 2202,
      "is_featured": false,
      "clip_url": "http://localhost:8080/getclipurl.php?id=VastOutstandingMinkNononoCat-hjFbXj-a8-WvyN0z"
    }
  ]
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
        <td>start_date</td>
        <td>String</td>
        <td>
            (optional) date formatted as YYYY-MM-DDTHH:MM:SSZ
        </td>
    </tr>
    <tr>
        <td>end_date</td>
        <td>String</td>
        <td>
            (optional) date formatted as YYYY-MM-DDTHH:MM:SSZ
        </td>
    </tr>
    <tr>
        <td>prefer_featured</td>
        <td>Boolean</td>
        <td>(optional - default: false)</td>
    </tr>
    <tr>
        <td>limit</td>
        <td>Integer</td>
        <td>(optional - default: 100) A limit on the number of objects to be returned, between 1 and 100.</td>
    </tr>
    <tr>
        <td>id</td>
        <td>String</td>
        <td>(example) ?id=VictoriousAwkwardPheasantKevinTurtle-xT8tH7fW0oU0vZ8gT4</td>
    </tr>
    <tr>
        <td>shuffle</td>
        <td>Boolean</td>
        <td>(example) Shuffle the returned objects. IE: ?channel=twitch&shuffle=true</td>
    </tr>
    <tr>
        <td>random</td>
        <td>Boolean</td>
        <td>(example) Pull a single random clip. IE: ?channel=twitch&random=true</td>
    </tr>
    <tr>
        <td>count</td>
        <td>Integer</td>
        <td>(example) Used with `random`. IE: ?channel=twitch&random=true&count=4</td>
    </tr>
    </tbody>
</table>