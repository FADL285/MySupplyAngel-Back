<?php

namespace App\Http\Controllers\Api\WebSite\Contact;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\WebSite\Contact\ContactRequest;
use App\Models\contact;

class ContactController extends Controller
{
    public function getContact()
    {
        $data = [
            'messenger' => (string) setting('messenger'),
            'whatsapp'  => (string) setting('whatsapp'),
            'social'    => [
                'facebook'  => (string) setting('facebook'),
                'twitter'   => (string) setting('twitter'),
                'instagram' => (string) setting('instagram'),
                'linkedin'  => (string) setting('linkedin'),
                'youtube'   => (string) setting('youtube'),
                'tiktok'    => (string) setting('tiktok'),
            ],
        ];

        return response()->json(['status' => true, 'data' => $data, 'message' => '']);
    }

    public function contact(ContactRequest $request)
    {
        $contact = contact::create($request->validated());
        // notification to admins
        return response()->json(['status' => true, 'data' => null, 'message' => trans('website.messages.send_successfully')]);
    }
}
