<?php
namespace LaraBug\Helpers;

use GuzzleHttp\Client;

class Logger
{
    protected $client;
    private $config = [];
    public $additionalData = [];
    public $exception;

    public function __construct(array $exception = [])
    {
        $this->client = new Client;

        $this->config['login_key'] = config('larabug.login_key', []);
        $this->config['project_key'] = config('larabug.project_key', []);
        $this->config['queue_enabled'] = config('larabug.queue.enabled', false);
        $this->config['queue_name'] = config('larabug.queue.name', null);

        $this->exception = $exception;
    }

    public function addAdditionalData(array $additionalData = [])
    {
        $this->additionalData = $additionalData;

        return $this;
    }

    public function send()
    {
        $this->sendError();
    }

    private function sendError()
    {
        $this->client->request('POST', base64_decode('aHR0cHM6Ly93d3cubGFyYWJ1Zy5jb20vYXBpL2xvZw=='), [
            'headers' => [
                'Authorization'      => 'Bearer ' . $this->config['login_key']
            ],
            'form_params' => [
                'project' => $this->config['project_key'],
                'exception' => $this->exception,
                'additional' => $this->additionalData,
                'user' => auth()->check() ? auth()->user()->toArray() : null
            ]
        ]);
    }
}