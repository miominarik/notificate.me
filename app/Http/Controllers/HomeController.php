<?php

namespace App\Http\Controllers;

use App\Http\Middleware\VerifyGoogleRecaptcha;
use App\Http\Requests\ContactMailRequest;
use App\Mail\ContactFormMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class HomeController extends Controller
{

    public function SendContactMail(ContactMailRequest $request)
    {
        $validated = $request->validated();

        if (!empty($validated)) {
            if ($validated['gdpr'] == 1) {
                Mail::to('info@notificate.me', 'Notificate.me')->send(new ContactFormMail($validated['name'], $validated['email'], $validated['subject'], $validated['text_message']));
                return redirect()->back()->with('emai_send', true);
            };
        }
    }
}
