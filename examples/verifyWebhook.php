<?php

use ServeurMinecraftVote\Exceptions\SignatureVerificationException;
use ServeurMinecraftVote\ServeurMinecraftVote;

$secret = "wh_......";

$data = @file_get_contents('php://input');
$header = $_SERVER['HTTP_X-SMV-Signature'];

$smv = new ServeurMinecraftVote('smv_sk_....');

try {
    $smv->verifyHeader($data, $header, $secret);
} catch (SignatureVerificationException $exception) {
    http_response_code(500);
    exit();
}

$eventData = json_decode($data['data']);
http_response_code(200);
