<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ClaudeService
{
    private string $apiKey;
    private string $model = 'claude-sonnet-4-20250514';
    private string $baseUrl = 'https://api.anthropic.com/v1/messages';

    public function __construct()
    {
        $this->apiKey = config('services.claude.api_key');
    }

    public function generate(string $systemPrompt, string $userMessage, int $maxTokens = 300): ?string
    {
        try {
            $response = Http::withHeaders([
                'x-api-key' => $this->apiKey,
                'anthropic-version' => '2023-06-01',
                'content-type' => 'application/json',
                'anthropic-beta' => 'do-not-train',
            ])->post($this->baseUrl, [
                'model' => $this->model,
                'max_tokens' => $maxTokens,
                'system' => $systemPrompt,
                'messages' => [
                    ['role' => 'user', 'content' => $userMessage],
                ],
            ]);

            if ($response->successful()) {
                return $response->json('content.0.text');
            }

            Log::error('Claude API error', ['status' => $response->status()]);
            return null;
        } catch (\Exception $e) {
            Log::error('Claude API exception', ['message' => $e->getMessage()]);
            return null;
        }
    }
}
