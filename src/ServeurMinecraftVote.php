<?php

namespace ServeurMinecraftVote;

use ServeurMinecraftVote\Exceptions\SignatureVerificationException;

class ServeurMinecraftVote
{

    const DEFAULT_SECONDS_TOLERANCE = 60;

    /**
     * Secret key to interact with the creation and editing of webhooks
     *
     * @var string
     */
    private $secretKey;

    /**
     * @param string|null $secretKey
     */
    public function __construct(string $secretKey = null)
    {
        $this->secretKey = $secretKey;
    }

    /**
     * Returns the secret key that allows to interact with the API
     *
     * @return null|string the secret key
     */
    public function getSecretKey(): string
    {
        return $this->secretKey;
    }

    /**
     * Allows to check the header of a webhook
     * If the header is valid then you can process the request
     *
     * @param string $data the content sent by server minecraft vote
     * @param string $header the content of the header
     * @param string $secretKey the secret key of the webhook
     * @param int $secondTolerance the tolerance in milliseconds between now and the creation of the request
     * @return bool
     * @throws SignatureVerificationException if the verification fails
     */
    public function verifyHeader(string $data, string $header, string $secretKey, int $secondTolerance = self::DEFAULT_SECONDS_TOLERANCE): bool
    {
        $timestamp = $this->getTimestamp($header);
        $signature = $this->getSignatures($header);

        if ($timestamp === -1)
            throw new SignatureVerificationException(
                "Unable to extract the timestamp from the header.",
                $data,
                $header
            );

        if (empty($signature))
            throw new SignatureVerificationException(
                "Unable to extract the signature from the header.",
                $data,
                $header
            );

        $signedPayload = "{$timestamp}.{$data}";
        $expectedSignature = hash_hmac('sha256', $signedPayload, $secretKey);
        $sameSignature = hash_equals($signature, $expectedSignature);

        if (!$sameSignature) {
            throw new SignatureVerificationException(
                "Unable to verify the signature, the request is invalid.",
                $data,
                $header
            );
        }

        if ($secondTolerance > 0 && abs(time() - $timestamp) > $secondTolerance) {
            throw new SignatureVerificationException(
                "Time limit of the request exceeded",
                $data,
                $header
            );
        }

        return true;
    }

    /**
     * Returns the timestamp contained in the header
     *
     * @param $header
     * @return int|mixed|string
     */
    private function getTimestamp($header)
    {
        $informations = explode('.', $header);
        if (count($informations) === 2)
            return $informations[0];
        return -1;
    }

    /**
     * Returns the signature contained in the header
     *
     * @param $header
     * @return mixed|string|null
     */
    private function getSignatures($header)
    {
        $informations = explode('.', $header);
        if (count($informations) === 2)
            return $informations[1];
        return null;
    }

}
