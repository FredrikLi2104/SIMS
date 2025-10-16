<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatbotController extends Controller
{
    /**
     * Python chatbot service URL
     */
    private $chatbotUrl = 'http://localhost:5001';

    /**
     * Initialize a new chatbot session
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function initializeSession(Request $request)
    {
        $validated = $request->validate([
            'session_id' => 'required|string',
            'organization_id' => 'required|integer'
        ]);

        try {
            $response = Http::timeout(30)->post("{$this->chatbotUrl}/api/chat/init", [
                'session_id' => $validated['session_id'],
                'organization_id' => $validated['organization_id']
            ]);

            if ($response->successful()) {
                return response()->json($response->json());
            }

            throw new \Exception('Chatbot service unavailable');

        } catch (\Exception $e) {
            Log::error('Chatbot initialization error: ' . $e->getMessage());

            return response()->json([
                'error' => 'Unable to connect to chatbot service. Please try again later.'
            ], 503);
        }
    }

    /**
     * Send a message to the chatbot
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendMessage(Request $request)
    {
        $validated = $request->validate([
            'session_id' => 'required|string',
            'message' => 'required|string|max:1000'
        ]);

        try {
            $response = Http::timeout(180)->post("{$this->chatbotUrl}/api/chat/message", [
                'session_id' => $validated['session_id'],
                'message' => $validated['message']
            ]);

            if ($response->successful()) {
                return response()->json($response->json());
            }

            throw new \Exception('Chatbot service error');

        } catch (\Exception $e) {
            Log::error('Chatbot message error: ' . $e->getMessage());

            return response()->json([
                'error' => 'Unable to process your message. Please try again.'
            ], 503);
        }
    }

    /**
     * Get active chatbot sessions
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSessions()
    {
        try {
            $response = Http::timeout(10)->get("{$this->chatbotUrl}/api/chat/sessions");

            if ($response->successful()) {
                return response()->json($response->json());
            }

            throw new \Exception('Chatbot service unavailable');

        } catch (\Exception $e) {
            Log::error('Chatbot sessions error: ' . $e->getMessage());

            return response()->json([
                'error' => 'Unable to retrieve sessions.'
            ], 503);
        }
    }

    /**
     * Health check for chatbot service
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function healthCheck()
    {
        try {
            $response = Http::timeout(5)->get("{$this->chatbotUrl}/health");

            if ($response->successful()) {
                return response()->json([
                    'status' => 'online',
                    'chatbot_service' => $response->json()
                ]);
            }

            throw new \Exception('Chatbot service unavailable');

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'offline',
                'message' => 'Chatbot service is currently unavailable'
            ], 503);
        }
    }
}