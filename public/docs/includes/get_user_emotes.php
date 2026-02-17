<h2>Get User Emotes</h2>
<pre><code class="bash">
# Here is a curl example
curl -X GET 'http://localhost:8080/getuseremotes.php?channel=twitch'
</code></pre>
<p>
    Make a GET call to the following url :<br>
    <code class="higlighted break-word">http://localhost:8080/getuseremotes.php</code>
</p>

<pre><code class="json">
Result example :

{
  "data": [
    {
      "id": "emotesv2_90ae588b6ca34c11b8778367d4c08290",
      "name": "twitchLOVE",
      "images": {
        "url_1x": "https://static-cdn.jtvnw.net/emoticons/v2/emotesv2_90ae588b6ca34c11b8778367d4c08290/static/light/1.0",
        "url_2x": "https://static-cdn.jtvnw.net/emoticons/v2/emotesv2_90ae588b6ca34c11b8778367d4c08290/static/light/2.0",
        "url_4x": "https://static-cdn.jtvnw.net/emoticons/v2/emotesv2_90ae588b6ca34c11b8778367d4c08290/static/light/3.0"
      },
      "tier": "",
      "emote_type": "subscriptions",
      "emote_set_id": "374814395",
      "format": [
        "static"
      ],
      "scale": [
        "1.0",
        "2.0",
        "3.0"
      ],
      "theme_mode": [
        "light",
        "dark"
      ]
    },
    {
      "id": "emotesv2_7d7473ef8ba54ce2b2f8e29d078f90bf",
      "name": "twitchAsk",
      "images": {
        "url_1x": "https://static-cdn.jtvnw.net/emoticons/v2/emotesv2_7d7473ef8ba54ce2b2f8e29d078f90bf/static/light/1.0",
        "url_2x": "https://static-cdn.jtvnw.net/emoticons/v2/emotesv2_7d7473ef8ba54ce2b2f8e29d078f90bf/static/light/2.0",
        "url_4x": "https://static-cdn.jtvnw.net/emoticons/v2/emotesv2_7d7473ef8ba54ce2b2f8e29d078f90bf/static/light/3.0"
      },
      "tier": "",
      "emote_type": "follower",
      "emote_set_id": "e105cd06-6f1c-4276-8fb8-da32fb664835",
      "format": [
        "static"
      ],
      "scale": [
        "1.0",
        "2.0",
        "3.0"
      ],
      "theme_mode": [
        "light",
        "dark"
      ]
    },
    {
      "id": "emotesv2_a3555e43b9594ca6835dbe15d52415c6",
      "name": "twitchLincLegend",
      "images": {
        "url_1x": "https://static-cdn.jtvnw.net/emoticons/v2/emotesv2_a3555e43b9594ca6835dbe15d52415c6/static/light/1.0",
        "url_2x": "https://static-cdn.jtvnw.net/emoticons/v2/emotesv2_a3555e43b9594ca6835dbe15d52415c6/static/light/2.0",
        "url_4x": "https://static-cdn.jtvnw.net/emoticons/v2/emotesv2_a3555e43b9594ca6835dbe15d52415c6/static/light/3.0"
      },
      "tier": "",
      "emote_type": "follower",
      "emote_set_id": "e105cd06-6f1c-4276-8fb8-da32fb664835",
      "format": [
        "static"
      ],
      "scale": [
        "1.0",
        "2.0",
        "3.0"
      ],
      "theme_mode": [
        "light",
        "dark"
      ]
    },
    {
      "id": "emotesv2_9c38e116e5e84f51b338ca0779ba7c2c",
      "name": "twitchDino",
      "images": {
        "url_1x": "https://static-cdn.jtvnw.net/emoticons/v2/emotesv2_9c38e116e5e84f51b338ca0779ba7c2c/static/light/1.0",
        "url_2x": "https://static-cdn.jtvnw.net/emoticons/v2/emotesv2_9c38e116e5e84f51b338ca0779ba7c2c/static/light/2.0",
        "url_4x": "https://static-cdn.jtvnw.net/emoticons/v2/emotesv2_9c38e116e5e84f51b338ca0779ba7c2c/static/light/3.0"
      },
      "tier": "",
      "emote_type": "follower",
      "emote_set_id": "e105cd06-6f1c-4276-8fb8-da32fb664835",
      "format": [
        "static"
      ],
      "scale": [
        "1.0",
        "2.0",
        "3.0"
      ],
      "theme_mode": [
        "light",
        "dark"
      ]
    },
    {
      "id": "emotesv2_91290c954bae4c53849c2b9d540e96c9",
      "name": "twitchSmart",
      "images": {
        "url_1x": "https://static-cdn.jtvnw.net/emoticons/v2/emotesv2_91290c954bae4c53849c2b9d540e96c9/static/light/1.0",
        "url_2x": "https://static-cdn.jtvnw.net/emoticons/v2/emotesv2_91290c954bae4c53849c2b9d540e96c9/static/light/2.0",
        "url_4x": "https://static-cdn.jtvnw.net/emoticons/v2/emotesv2_91290c954bae4c53849c2b9d540e96c9/static/light/3.0"
      },
      "tier": "",
      "emote_type": "follower",
      "emote_set_id": "e105cd06-6f1c-4276-8fb8-da32fb664835",
      "format": [
        "static"
      ],
      "scale": [
        "1.0",
        "2.0",
        "3.0"
      ],
      "theme_mode": [
        "light",
        "dark"
      ]
    },
    {
      "id": "emotesv2_196f36b3cc1b496585121be2826fb8e1",
      "name": "twitchFollow",
      "images": {
        "url_1x": "https://static-cdn.jtvnw.net/emoticons/v2/emotesv2_196f36b3cc1b496585121be2826fb8e1/static/light/1.0",
        "url_2x": "https://static-cdn.jtvnw.net/emoticons/v2/emotesv2_196f36b3cc1b496585121be2826fb8e1/static/light/2.0",
        "url_4x": "https://static-cdn.jtvnw.net/emoticons/v2/emotesv2_196f36b3cc1b496585121be2826fb8e1/static/light/3.0"
      },
      "tier": "",
      "emote_type": "follower",
      "emote_set_id": "e105cd06-6f1c-4276-8fb8-da32fb664835",
      "format": [
        "static"
      ],
      "scale": [
        "1.0",
        "2.0",
        "3.0"
      ],
      "theme_mode": [
        "light",
        "dark"
      ]
    }
  ],
  "template": "https://static-cdn.jtvnw.net/emoticons/v2/{{id}}/{{format}}/{{theme_mode}}/{{scale}}"
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
    </tbody>
</table>