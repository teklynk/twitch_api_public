<h2>Get User Status</h2>
<pre><code class="bash">
# Here is a curl example
curl -X GET 'http://localhost:8080/getuserstatus.php?channel=twitch'
</code></pre>
<p>
    Make a GET call to the following url :<br>
    <code class="higlighted break-word">http://localhost:8080/getuserstatus.php</code>
</p>

<pre><code class="json">
Result example :

{
  "data": [
    {
      "broadcaster_id": "12826",
      "broadcaster_login": "twitch",
      "broadcaster_name": "Twitch",
      "broadcaster_language": "en",
      "game_id": "509658",
      "game_name": "Just Chatting",
      "title": "Twitch Public Access (March 2025) | w/ @merrykish @friskk @grayovercastart @pleasantlytwstd",
      "delay": 0,
      "tags": [
        "twıtch",
        "2025",
        "justchatting",
        "English",
        "twitchpublicaccess"
      ],
      "content_classification_labels": [
        
      ],
      "is_branded_content": false
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
        <td>id</td>
        <td>Integer</td>
        <td>
            (optional) Twitch channel id
        </td>
    </tr>
    </tbody>
</table>