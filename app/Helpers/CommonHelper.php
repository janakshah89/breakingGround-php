<?php

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

function getUploadPath(): string
{
    return base_path() . "/uploads/";
}

function getUuid()
{
    return Str::uuid()->toString() . "-" . time();
}

function sendEmail($template, $to, $sub, $data, $attachment = null): string
{
    try {
        if (App::Environment() !== 'production') {
            $to = env('DEVELOPER_EMAIL');
        }
        Mail::send(
            $template,
            $data,
            function ($message) use ($data, $to, $sub, $attachment) {
                $message->to($to, $data['name'])->subject($sub);
                $message->from(env('MAIL_FROM_ADDRESS'), $sub);
                if ($attachment) {
                    foreach ($attachment as $file) {
                        $message->attach($file);
                    }
                }
            }
        );
        return true;
    } catch (Exception $e) {
        Log::error($e->getMessage());
        return $e->getMessage();
    }
}
