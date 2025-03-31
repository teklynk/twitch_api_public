<h2>Get User Global Emotes</h2>
<pre><code class="bash">
# Here is a curl example
curl -X GET 'http://server_url/getglobalemotes.php'
</code></pre>
<p>
    Make a GET call to the following url :<br>
    <code class="higlighted break-word">http://server_url/getglobalemotes.php</code>
</p>

<pre><code class="json">
Result example :

{
  "data": [
    {
      "id": "emotesv2_2babf8f02c9e480dad8ced0e6e266d4a",
      "name": "INZOIPsyCat",
      "images": {
        "url_1x": "https://static-cdn.jtvnw.net/emoticons/v2/emotesv2_2babf8f02c9e480dad8ced0e6e266d4a/static/light/1.0",
        "url_2x": "https://static-cdn.jtvnw.net/emoticons/v2/emotesv2_2babf8f02c9e480dad8ced0e6e266d4a/static/light/2.0",
        "url_4x": "https://static-cdn.jtvnw.net/emoticons/v2/emotesv2_2babf8f02c9e480dad8ced0e6e266d4a/static/light/3.0"
      },
      "format": [
        "static",
        "animated"
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
      "id": "emotesv2_634d9f10a8bf4776ad46df1bb2c9a7ca",
      "name": "ClixHuh",
      "images": {
        "url_1x": "https://static-cdn.jtvnw.net/emoticons/v2/emotesv2_634d9f10a8bf4776ad46df1bb2c9a7ca/static/light/1.0",
        "url_2x": "https://static-cdn.jtvnw.net/emoticons/v2/emotesv2_634d9f10a8bf4776ad46df1bb2c9a7ca/static/light/2.0",
        "url_4x": "https://static-cdn.jtvnw.net/emoticons/v2/emotesv2_634d9f10a8bf4776ad46df1bb2c9a7ca/static/light/3.0"
      },
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
      "id": "emotesv2_a7ab2c184e334d4a9784e6e5d51947f7",
      "name": "WeDidThat",
      "images": {
        "url_1x": "https://static-cdn.jtvnw.net/emoticons/v2/emotesv2_a7ab2c184e334d4a9784e6e5d51947f7/static/light/1.0",
        "url_2x": "https://static-cdn.jtvnw.net/emoticons/v2/emotesv2_a7ab2c184e334d4a9784e6e5d51947f7/static/light/2.0",
        "url_4x": "https://static-cdn.jtvnw.net/emoticons/v2/emotesv2_a7ab2c184e334d4a9784e6e5d51947f7/static/light/3.0"
      },
      "format": [
        "static",
        "animated"
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

    </tbody>
</table>