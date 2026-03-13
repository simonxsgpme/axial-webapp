<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ApiService
{
    protected $baseUrl;
    protected $secret_key;
    protected $mailler_base_url;
    protected $mailler_api_key;

    public function __construct($baseUrl = null)
    {
        $this->baseUrl = $baseUrl ?? env('SIGGA_API_BASE_URL', 'http://127.0.0.1:8000/api');
        $this->secret_key = bcrypt(env('API_SECRET_KEY'));
        $this->mailler_base_url = env('MAIL_SENDER_URL');
        $this->mailler_api_key = env('SGPME_MAILLER_API_KEY');
    }

    /**
     * Gère les requêtes GET.
     *
     * @param string $endpoint
     * @param array $params
     * @return array
     */
    public function get(string $endpoint, array $params = []): array
    {

        $response = Http::withHeaders([
            'Authorization' => $this->secret_key,
            'Accept' => 'application/json',
        ])->get("{$this->baseUrl}/{$endpoint}", $params);

        // Gérez la réponse
        return $this->handleResponse($response);
    }


    /**
     * Gère les requêtes POST.
     *
     * @param string $endpoint
     * @param array $data
     * @return array
     */
    public function post(string $endpoint, array $data = []): array
    {
        $response = Http::withHeaders([
            'Authorization' => $this->secret_key,
        ])->post("{$this->baseUrl}/{$endpoint}", $data);

        return $this->handleResponse($response);
    }

    /**
     * Gère les requêtes POST.
     *
     * @param string $endpoint
     * @param array $data
     * @return array
     */
public function sendMail(array $data = []): array
{
    $response = Http::withToken($this->mailler_api_key)
        ->post("{$this->mailler_base_url}/send-mail", $data);

    return $this->handleResponse($response);
}

    /**
     * Gère la réponse de l'API.
     *
     * @param \Illuminate\Http\Client\Response $response
     * @return array
     */
    private function handleResponse($response): array
    {
        if ($response->successful()) {
            return $response->json();
        }

        // Gestion des erreurs
        return json_decode($response->body(), TRUE);
    }
}
