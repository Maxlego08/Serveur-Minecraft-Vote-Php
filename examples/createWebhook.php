<?php

use GuzzleHttp\Exception\GuzzleException;
use ServeurMinecraftVote\Exceptions\WebhookCreateException;
use ServeurMinecraftVote\ServeurMinecraftVote;

$secretKey = 'smv_sk_...';

$smv = new ServeurMinecraftVote($secretKey);
$events = [
    'user.vote',
    'user.follow',
    'user.unfollow'
];
try {
    $webhook = $smv->createWebhook('https://test.fr/api/example', $events, 'Test webhook');
    echo $webhook->secretKey;
} catch (GuzzleException $e) {
} catch (WebhookCreateException $e) {
}
