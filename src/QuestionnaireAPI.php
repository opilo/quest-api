<?php

namespace QuestApi;

use GuzzleHttp\Client;
use GuzzleHttp\Message\ResponseInterface;

class QuestionnaireAPI
{
    /**
     * @var Client
     */
    protected $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_url' => getenv('QUESTIONNAIRE_URL') . '/api-client/',
            'defaults' => ['exceptions' => false],
        ]);
    }

    public function createPrivateAnswerSheet($questionnaire_id, $questionnaire_hash)
    {
        $response = $this->makeRequest('post', 'create-private-answer-sheet', [
            'questionnaire_id'   => $questionnaire_id,
            'questionnaire_hash' => $questionnaire_hash,
        ], true, true);
        return $response;
    }

    public function sayHi()
    {
        return $this->makeRequest('get', 'say-hi');
    }

    /**
     * @param string $method e.g: GET, POST
     * @param string $uri
     * @param array $query
     * @param bool $decode
     * @param bool $assoc
     * @return string | array
     * @throws \Exception
     */
    private function makeRequest($method, $uri, $query = [], $decode = true, $assoc = false)
    {
        /** @var ResponseInterface $response */
        $response = $this->client->$method($uri, [
            'query' => array_merge($query, [
                'username' => getenv('QUESTIONNAIRE_USERNAME'),
                'password' => getenv('QUESTIONNAIRE_PASSWORD'),
            ]),
        ]);

        if ($response->getStatusCode() != 200) {
            throw new \Exception($response->getBody()->getContents());
        }

        if ($decode) {
            return json_decode($response->getBody()->getContents(), $assoc);
        } else {
            return $response->getBody()->getContents();
        }
    }
}