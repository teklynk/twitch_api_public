<?php
//Get Twitch Tools nav links
$navJson = file_get_contents('nav.json');

header('Content-type: application/json');

echo $navJson;