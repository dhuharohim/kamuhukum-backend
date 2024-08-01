<?php

use Illuminate\Http\JsonResponse;

/**
 * Return String default error message
 *
 * @return String
 */
function defaultErrorMessage(): String
{
    return 'Internal Server Error';
}

/**
 * Return Custom 200 Message JSON
 *
 * @param  string | null $message
 * @param  array | null $data
 * @param  int $status
 * @return \Illuminate\Http\JsonResponse | JSON
 */
function successResponse($data = [],$message = '') : JsonResponse
{
    $response = [
        'error' => false,
        'status' => 200,
        'data' => $data,
        'message' => $message
    ];

    return response()->json($response, 200);
}

/**
 * Return Custom 404 Message JSON
 *
 * @param  string | null $message
 * @return \Illuminate\Http\JsonResponse | JSON
 */
function recordNotFoundResponse($message = null): JsonResponse
{
    return response()->json([
        'error' => true,
        'status' => 404,
        'message' => $message ? $message : 'Sorry we could not find your data here. Please try again Later!',
    ], 404);
}


/**
 * Return Custom 500 Message JSON
 *
 * @param  string | null $message
 * @return \Illuminate\Http\JsonResponse | JSON
 */
function internalErrorResponse($message = null): JsonResponse
{
    $errorMessage = defaultErrorMessage();

    // Send Error Message to Sentry
    // \Sentry\captureMessage($message);
    if ($message && config('app.debug')) {
        $errorMessage = $message;
    }

    return response()->json([
        'error' => true,
        'status' => 500,
        'message' => $errorMessage,
    ], 500);
}

/**
 * Return Custom 400 Message JSON
 *
 * @param  string | null $message
 * @return \Illuminate\Http\JsonResponse | JSON
 */
function resourceAlreadyExistsResponse($message = null): JsonResponse
{
    return response()->json([
        'error' => true,
        'status' => 400,
        'message' => $message ? $message : "Resource already exists",
    ], 400);
}

/**
 * Return Custom 422 Message JSON
 *
 * @param  string | null $message
 * @param  array | null $errors
 * @return \Illuminate\Http\JsonResponse | JSON
 */
function validateErrorResponse($message = null, $errors = null): JsonResponse
{
    $response = [
        'error' => true,
        'status' => 422,
        'message' => $message ? $message : 'Validation errors',
    ];

    if ($errors) {
        $response['errors'] = $errors;
    }

    return response()->json($response, 422);
}

/**
 * Return Custom 401 Message JSON
 *
 * @param  string | null $message
 * @return \Illuminate\Http\JsonResponse | JSON
 */
function badCredentialsResponse($message = null): JsonResponse
{
    return response()->json([
        'error' => true,
        'status' => 401,
        'message' => $message ? $message : 'Invalid credentials',
    ], 401);
}

/**
 * Return Custom 401 Message JSON
 *
 * @param  string | null $message
 * @return \Illuminate\Http\JsonResponse | JSON
 */
function unauthorizedResponse($message = null): JsonResponse
{
    return response()->json([
        'error' => true,
        'status' => 401,
        'message' => $message ? $message : 'You are not permitted to see this resource',
    ], 401);
}

/**
 * Return Access Forbidden Response
 *
 * @param  string | null $message
 * @return \Illuminate\Http\JsonResponse | JSON
 */
function forbiddenResponse($message = null): JsonResponse
{
    return response()->json([
        'error' => true,
        'status' => 401,
        'message' => $message ? $message : 'You are not permitted to see this resource',
    ], 403);
}

/**
 * Return Custom 401 Message JSON
 *
 * @param  string | null $message
 * @return \Illuminate\Http\JsonResponse | JSON
 */
function badRequestResponse($message = null): JsonResponse
{
    return response()->json([
        'error' => true,
        'status' => 400,
        'message' => $message ? $message : 'Bad request',
    ], 400);
}

