<?php

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

function getUploadPath(): string
{
    return base_path() . "/uploads/";
}


function sendEmail($template, $to, $sub, $data): string
{
    Log::info($template);
    Log::info($to);
    Log::info($sub);
    Log::info($data);
    try {
        if (App::Environment() !== 'production') {
             $to = env('DEVELOPER_EMAIL');
        }
        Mail::send(
            $template,
            $data,
            function ($message) use ($data, $to, $sub) {
                $message->to($to, $data['name'])->subject($sub);
                $message->from(env('MAIL_FROM_ADDRESS'), $sub);
            }
        );
        return true;
    } catch (Exception $e) {
        Log::error($e->getMessage());
        return $e->getMessage();
    }
}
