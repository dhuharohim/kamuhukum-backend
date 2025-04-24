<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FacebookWebhookController extends Controller
{
    /**
     * Handle the verification request from Facebook.
     * This is called when you first set up the webhook.
     */
    public function verify(Request $request)
    {
        $mode = $request->query('hub_mode');
        $token = $request->query('hub_verify_token');
        $challenge = $request->query('hub_challenge');

        // Your verification token should be stored in .env
        $verify_token = env('FACEBOOK_WEBHOOK_VERIFY_TOKEN');

        // Check if mode and token are correct
        if ($mode === 'subscribe' && $token === $verify_token) {
            Log::info('Facebook webhook verified successfully');
            return response($challenge, 200);
        }

        // Return 403 Forbidden if verification fails
        Log::error('Facebook webhook verification failed', [
            'mode' => $mode,
            'token' => $token,
            'verify_token' => $verify_token
        ]);
        return response('Verification failed', 403);
    }

    /**
     * Handle webhook events from Facebook.
     * This processes actual updates from Instagram/Facebook.
     */
    public function handle(Request $request)
    {
        $payload = $request->all();

        // Log the incoming webhook for debugging
        Log::info('Facebook webhook received', ['payload' => $payload]);

        // Process different types of updates
        if (isset($payload['entry'])) {
            foreach ($payload['entry'] as $entry) {
                // Handle Instagram mentions
                if (isset($entry['changes'])) {
                    $this->processInstagramUpdates($entry);
                }

                // Handle other types of updates
                // Add more handlers as needed based on your webhook subscription
            }
        }

        // Always return a 200 OK to acknowledge receipt
        return response('EVENT_RECEIVED', 200);
    }

    /**
     * Process Instagram-specific updates.
     */
    private function processInstagramUpdates($entry)
    {
        // Add your business logic here
        // e.g., handle comments, mentions, etc.

        Log::info('Processing Instagram update', ['entry' => $entry]);

        // Example: If you're monitoring comments
        if (isset($entry['changes'][0]['field']) && $entry['changes'][0]['field'] === 'comments') {
            $comment = $entry['changes'][0]['value'];
            // Process the comment
        }
    }
}
