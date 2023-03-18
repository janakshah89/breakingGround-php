<?php

namespace App\Traits;

trait ResponseTrait
{
    // 200 = HTTP_OK
    public function successResponse($data, $message = null)
    {
        return response()->json(['data' => $data, 'message' => $message]);
    }

    // 500 = HTTP_INTERNAL_SERVER_ERROR
    public function sendErrorResponse(\Exception $exception)
    {
        $data = [
            'file' => __FILE__,
            'line' => __LINE__,
            'code' => HttpStatusCode::HTTP_INTERNAL_SERVER_ERROR,
            'message' => $exception->getMessage(),
            'trace' => env('APP_DEBUG') === true ? $exception->getTrace() : null,
            'response' => [
                __('api_errors.server_error'),
            ],
        ];

        return response()->json($data, HttpStatusCode::HTTP_INTERNAL_SERVER_ERROR);
    }

    // 403 = HTTP_FORBIDDEN
    public function sendAccessDenied(string $message = null)
    {
        if (empty($message)) {
            $message = trans('api_errors.unauthorised');
        }

        return response()->json(['status' => "ERROR", 'messages' => array($message)], HttpStatusCode::HTTP_FORBIDDEN);
    }

    // 401 = HTTP_UNAUTHORIZED
    public function sendUnauthorised(string $message = null)
    {
        if (empty($message)) {
            $message = 'Unauthorised';
        }
        return response()->json(
            [
                'status' => "ERROR",
                'messages' => array($message),
            ],
            HttpStatusCode::HTTP_UNAUTHORIZED
        );
    }

    // 501 = HTTP_NOT_IMPLEMENTED
    public function sendNotImplemented(string $message = null)
    {
        if (empty($message)) {
            $message = 'Method not implemented yet';
        }
        $data = [];

        return response()->json($data, HttpStatusCode::HTTP_NOT_IMPLEMENTED);
    }

    // 400 = HTTP_BAD_REQUEST
    protected function sendBadRequest($msg = 'Bad Request')
    {
        // CHECK STRING ARRAY AND OBJECT CONDITION AND RETURN SAME MESSAGE FORMAT.
        if (is_string($msg)) {
            $msg = json_encode(array('message' => [$msg]));
        } else {
            $msg = (is_array($msg)) ? json_encode($msg) : (string)$msg;
        }
        return response()->json(['status' => "ERROR", 'messages' => array($msg)], HttpStatusCode::HTTP_BAD_REQUEST);
    }

    // 404 = HTTP_NOT_FOUND
    protected function notFoundRequest(string $msg = '')
    {
        if (empty($msg)) {
            $msg = 'Not Found';
        }
        return response()->json(['status' => "ERROR", 'messages' => array($msg)], HttpStatusCode::HTTP_NOT_FOUND);
    }

    // 204 = HTTP_NO_CONTENT
    protected function recordNotFound(string $msg = '')
    {
        if (empty($msg)) {
            $msg = 'Record Not Found';
        }
        return response()->json(['status' => "ERROR", 'messages' => $msg], HttpStatusCode::HTTP_NO_CONTENT);
    }

    // 409 = HTTP_CONFLICT
    protected function sendConflictResponse(string $msg = '')
    {
        if (empty($msg)) {
            $msg = 'Record Exists';
        }
        return response()->json(['status' => "ERROR", 'messages' => array($msg)], HttpStatusCode::HTTP_CONFLICT);
    }

    // 405 = HTTP_NOT_FOUND
    protected function notAllowed(string $msg = '')
    {
        if (empty($msg)) {
            $msg = 'Not Allowed';
        }
        return response()->json(
            ['status' => "ERROR", 'messages' => array($msg)],
            HttpStatusCode::HTTP_METHOD_NOT_ALLOWED
        );
    }
}
