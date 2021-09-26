<?php

namespace ServeurMinecraftVote;

use PHPUnit\Framework\TestCase;
use ServeurMinecraftVote\Exceptions\SignatureVerificationException;

class VerifyWebhookTest extends TestCase
{

    const WEBHOOK_SECRET = "smv_secret_key";

    const WEBHOOK_DATA = '{
        "type": "event.test",
        "created_at": 123456789,
    }';

    /**
     * Generate valid header
     *
     * @param array $options
     * @return string
     */
    private function generateHeader(array $options = []): string
    {
        $timestamp = array_key_exists('timestamp', $options) ? $options['timestamp'] : time();
        $data = array_key_exists('data', $options) ? $options['data'] : self::WEBHOOK_DATA;
        $secret = array_key_exists('secret', $options) ? $options['secret'] : self::WEBHOOK_SECRET;
        $signature = array_key_exists('signature', $options) ? $options['signature'] : null;

        if (empty($signature)) {
            $signedPayload = "{$timestamp}.{$data}";
            $signature = hash_hmac('sha256', $signedPayload, $secret);
        }

        return "{$timestamp}.{$signature}";
    }

    /**
     * Allows testing if the request is valid
     *
     * @throws SignatureVerificationException
     */
    public function testValidRequest()
    {
        $header = $this->generateHeader();
        $smv = new ServeurMinecraftVote();
        $this->assertEquals(true, $smv->verifyHeader(self::WEBHOOK_DATA, $header, self::WEBHOOK_SECRET));
    }

    /**
     * @throws SignatureVerificationException
     */
    public function testInvalidRequest()
    {
        $this->expectException(SignatureVerificationException::class);

        $smv = new ServeurMinecraftVote();
        $smv->verifyHeader(self::WEBHOOK_DATA, 'empty.value', self::WEBHOOK_SECRET);
    }

    /**
     * @throws SignatureVerificationException
     */
    public function testExpireTimestamp()
    {
        $this->expectException(SignatureVerificationException::class);

        $header = $this->generateHeader([
            'timestamp' => time() - 1500,
        ]);
        $smv = new ServeurMinecraftVote();
        $smv->verifyHeader(self::WEBHOOK_DATA, $header, self::WEBHOOK_SECRET);
    }

    /**
     * @throws SignatureVerificationException
     */
    public function testValidTimestamp()
    {
        $header = $this->generateHeader([
            'timestamp' => time() - 900,
        ]);
        $smv = new ServeurMinecraftVote();
        $this->assertEquals(true, $smv->verifyHeader(self::WEBHOOK_DATA, $header, self::WEBHOOK_SECRET));
    }

    /**
     * @throws SignatureVerificationException
     */
    public function testInvalidSecret()
    {
        $this->expectException(SignatureVerificationException::class);

        $header = $this->generateHeader();
        $smv = new ServeurMinecraftVote();
        $smv->verifyHeader(self::WEBHOOK_DATA, $header, 'smv_invalid_secret');
    }

    /**
     * @throws SignatureVerificationException
     */
    public function testInvalidData()
    {
        $this->expectException(SignatureVerificationException::class);

        $header = $this->generateHeader();
        $smv = new ServeurMinecraftVote();
        $smv->verifyHeader('invalid data', $header, self::WEBHOOK_SECRET);
    }

    /**
     * @throws SignatureVerificationException
     */
    public function testInvalidDataInHeader()
    {
        $this->expectException(SignatureVerificationException::class);

        $header = $this->generateHeader([
            'data' => 'invalid data',
        ]);
        $smv = new ServeurMinecraftVote();
        $smv->verifyHeader(self::WEBHOOK_DATA, $header, self::WEBHOOK_SECRET);
    }

}
