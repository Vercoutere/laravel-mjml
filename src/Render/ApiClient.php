<?php

namespace Vercoutere\LaravelMjml\Render;

use Illuminate\Support\Facades\Http;

class ApiClient implements MjmlClient
{
    private const API_URL = 'https://api.mjml.io/v1';

    public function __construct(protected string $applicationId, protected string $secretKey)
    {
    }

    public function render(string $mjml): string
    {
        $response = Http::withBasicAuth($this->applicationId, $this->secretKey)
            ->post(self::API_URL . '/render', compact('mjml'));

        $response->throw();

        return $response->json('html');
    }
}
