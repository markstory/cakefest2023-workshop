<?php
declare(strict_types=1);

namespace App\Webauthn\Model;

use stdClass;

class CreateData
{
    private $payload;

    public function __construct(stdClass $payload)
    {
        $this->payload = $payload;
    }

    public function getCredentialId()
    {
        return base64_encode($this->payload->credentialId);
    }

    public function getPayload()
    {
        $data = (array)$this->payload;
        $output = [
            'credentialId' => base64_encode($data['credentialId']),
            'credentialPublicKey' => $data['credentialPublicKey'],
            'attestationFormat' => $data['attestationFormat'],
            'certificateIssuer' => $data['certificateIssuer'],
            'certificateSubject' => $data['certificateSubject'],
            'rootValid' => $data['rootValid'],
            'userPresent' => $data['userPresent'],
            'userVerified' => $data['userVerified'],
        ];

        return $output;
    }
}
