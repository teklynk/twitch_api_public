<h2>Get User Info</h2>
<pre><code class="bash">
# Here is a curl example
curl -X GET 'http://server_url/getuserinfo.php?channel=gogcom'
</code></pre>
<p>
    Make a GET call to the following url :<br>
    <code class="higlighted break-word">http://server_url/getuserinfo.php</code>
</p>

<pre><code class="json">
Result example :

{
  "data": [
    {
      "id": "12826",
      "login": "twitch",
      "display_name": "Twitch",
      "type": "",
      "broadcaster_type": "partner",
      "description": "Twitch is where thousands of communities come together for whatever, every day. ",
      "profile_image_url": "https://static-cdn.jtvnw.net/jtv_user_pictures/d5e6ebb4-a245-4ebf-bea6-2183e2f39600-profile_image-300x300.png",
      "offline_image_url": "https://static-cdn.jtvnw.net/jtv_user_pictures/3f5f72bf-ae59-4470-8f8a-730d9ef87500-channel_offline_image-1920x1080.png",
      "view_count": 0,
      "created_at": "2007-05-22T10:39:54Z"
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