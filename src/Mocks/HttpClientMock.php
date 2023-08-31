<?php

namespace App\Mocks;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Symfony\Contracts\HttpClient\ResponseStreamInterface;

final class HttpClientMock implements HttpClientInterface
{
    public array $responses = [];
    public array $options = [];

    public function request(string $method, string $url, array $body = []): ResponseInterface
    {
        if (!array_key_exists($url, $this->responses)) {
            (new LoggerMock())->warning('Passthrough HTTP Call to ' . $url);
            return ((new HttpClient())::create()->request($method, $url, $body));
        }
        $response = $this->responses[$url];
        return (new MockHttpClient($response))->request($method, $url);
    }

    public function whenCalling(string $url): self
    {
        unset($this->responses[$url]);
        $this->responses[$url] = new MockResponse();
        return $this;
    }

    public function willReturn(int $httpCode, mixed $body, array $headers = []): self
    {
        $this->responses[array_key_last($this->responses)] = new MockResponse(json_encode($body), ['http_code' => $httpCode, 'response_headers' => $headers]);
        return $this;
    }

    public function stream($responses, float $timeout = null): ResponseStreamInterface
    {
        throw new \LogicException(sprintf('%s() is not implemented', __METHOD__));
    }

    public function withOptions(array $options): static
    {
        $this->options = $options;
        return $this;
    }
}
