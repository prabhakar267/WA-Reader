<?php

// Regex to detect a message.
$MESSAGE_REGEX = "/(?<timestamp>(?:(?!: ).)*): (?<username>(?:(?!: ).)*): (?<message>.+)/";

// Regex to detect a status message (especially useful in group chats).
$STATUS_REGEX = "/(?<timestamp>(?:(?!: ).)*): (?<status>(?:(?!: ).)*)/";

// Regex to detect media messages (images, contact cards, audio, etc.).
$MEDIA_REGEX = "/.+omitted>/";
