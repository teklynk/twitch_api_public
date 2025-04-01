<h2>Get Game</h2>
<pre><code class="bash">
# Here is a curl example
curl -X GET 'http://localhost:8080/getgame.php?id=1299144050'
</code></pre>
<p>
    Make a GET call to the following url :<br>
    <code class="higlighted break-word">http://localhost:8080/getgame.php</code>
</p>

<pre><code class="json">
Result example :

{
  "data": [
    {
      "id": "1299144050",
      "name": "Disney Speedstorm",
      "box_art_url": "https://static-cdn.jtvnw.net/ttv-boxart/1299144050_IGDB-{width}x{height}.jpg",
      "igdb_id": "191402",
      "box_art_url_scaled": "https://static-cdn.jtvnw.net/ttv-boxart/1299144050_IGDB-285x380.jpg"
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
        <td>name</td>
        <td>String</td>
        <td>
            (required) Game name: `Disney Speedstorm`
        </td>
    </tr>
    <tr>
        <td>id</td>
        <td>Integer</td>
        <td>
            (optional) Game id: `1299144050`
        </td>
    </tr>
    </tbody>
</table>