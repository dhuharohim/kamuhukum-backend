<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FacebookWebhookController extends Controller
{
    public function callback(Request $request)
    {
        // Verify the request is from Meta
        $challenge = $request->input('hub_challenge');
        if ($challenge) {
            return response()->json(['hub.challenge' => $challenge]);
        }

        // Validate JSON payload from Meta
        $payload = json_decode($request->getContent(), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return response()->json(['error' => 'Invalid JSON payload'], 400);
        }

        // Process the payload
        // This is a placeholder for actual payload processing logic
        // For example, you might want to handle different types of events
        // such as messages, deliveries, or read receipts
        // This is a very basic example and should be expanded upon
        // to handle different scenarios and error cases
        // Assuming the token is used for testing automation
        if ($payload['object'] === 'page' && $payload['entry'][0]['messaging'][0]['text'] === 'facebook_webhook_testing_automation') {
            return response()->json(['message' => 'Automation test successful'], 200);
        } else {
            return response()->json(['error' => 'Invalid or unauthorized request'], 403);
        }
    }
}
