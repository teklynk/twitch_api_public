<!--
 API Documentation HTML Template  - 1.0.1
 Copyright © 2016 Florian Nicolas
 Licensed under the MIT license.
 https://github.com/ticlekiwi/API-Documentation-HTML-Template
 !-->
<!DOCTYPE html>
<html class="no-js" lang="en">
<head>
    <meta charset="utf-8">
    <title>API - Documentation</title>
    <meta name="description" content="">
    <meta name="author" content="ticlekiwi">

    <meta http-equiv="cleartype" content="on">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.11.1/styles/github-dark.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.11.1/highlight.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,300;0,400;0,500;1,300&family=Source+Code+Pro:wght@300&display=swap" rel="stylesheet"> 
    <link rel="stylesheet" href="css/style.css" media="all">
    <script>hljs.initHighlightingOnLoad();</script>
</head>

<body>
<div class="left-menu">
    <div class="content-logo">
        <div class="logo">
            <img alt="platform by Emily van den Heever from the Noun Project" title="platform by Emily van den Heever from the Noun Project" src="images/robot.gif" height="32" />
            <span>API Documentation</span>
        </div>
        <button class="burger-menu-icon" id="button-menu-mobile">
            <svg width="34" height="34" viewBox="0 0 100 100"><path class="line line1" d="M 20,29.000046 H 80.000231 C 80.000231,29.000046 94.498839,28.817352 94.532987,66.711331 94.543142,77.980673 90.966081,81.670246 85.259173,81.668997 79.552261,81.667751 75.000211,74.999942 75.000211,74.999942 L 25.000021,25.000058"></path><path class="line line2" d="M 20,50 H 80"></path><path class="line line3" d="M 20,70.999954 H 80.000231 C 80.000231,70.999954 94.498839,71.182648 94.532987,33.288669 94.543142,22.019327 90.966081,18.329754 85.259173,18.331003 79.552261,18.332249 75.000211,25.000058 75.000211,25.000058 L 25.000021,74.999942"></path></svg>
        </button>
    </div>
    <div class="mobile-menu-closer"></div>
    <div class="content-menu">
        <div class="content-infos">
            <div class="info"><b>Last Updated:</b> March 30 2025</div>
        </div>
        <ul>
            <li class="scroll-to-link active" data-target="content-get-started">
                <a>Get Started</a>
            </li>
            <li class="scroll-to-link" data-target="content-get-clips">
                <a>Get User Clips</a>
            </li>
            <li class="scroll-to-link" data-target="content-get-stream">
                <a>Get Stream</a>
            </li>
            <li class="scroll-to-link" data-target="content-get-info">
                <a>Get User Info</a>
            </li>
            <li class="scroll-to-link" data-target="content-get-game">
                <a>Get Game</a>
            </li>
            <li class="scroll-to-link" data-target="content-get-status">
                <a>Get User Status</a>
            </li>
            <li class="scroll-to-link" data-target="content-get-emotes">
                <a>Get User Emotes</a>
            </li>
            <li class="scroll-to-link" data-target="content-get-globalemotes">
                <a>Get Global Emotes</a>
            </li>
        </ul>
    </div>
</div>
<div class="content-page">
    <div class="content-code"></div>
    <div class="content">
        <div class="overflow-hidden content-section" id="content-get-started">
        <?php require_once(__DIR__ . '/includes/get_started.php'); ?>
        </div>
        <div class="overflow-hidden content-section" id="content-get-clips">
        <?php require_once(__DIR__ . '/includes/get_user_clips.php'); ?>
        </div>
        <div class="overflow-hidden content-section" id="content-get-stream">
        <?php require_once(__DIR__ . '/includes/get_stream.php'); ?>
        </div>
        <div class="overflow-hidden content-section" id="content-get-info">
        <?php require_once(__DIR__ . '/includes/get_user_info.php'); ?>
        </div>
        <div class="overflow-hidden content-section" id="content-get-game">
        <?php require_once(__DIR__ . '/includes/get_game.php'); ?>
        </div>
        <div class="overflow-hidden content-section" id="content-get-status">
        <?php require_once(__DIR__ . '/includes/get_user_status.php'); ?>
        </div>
        <div class="overflow-hidden content-section" id="content-get-emotes">
        <?php require_once(__DIR__ . '/includes/get_user_emotes.php'); ?>
        </div>
        <div class="overflow-hidden content-section" id="content-get-globalemotes">
        <?php require_once(__DIR__ . '/includes/get_globalemotes.php'); ?>
        </div>
    </div>
    <div class="content-code"></div>
</div>
<script src="js/script.js"></script>
</body>
</html>