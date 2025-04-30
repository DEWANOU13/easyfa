<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\SMSService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SMSVerificationController extends Controller
{
    protected $smsService;

    public function __construct(SMSService $smsService)
    {
        $this->smsService = $smsService;
    }

    public function sendVerificationCode()
    {
        $user = Auth::user();
        $code = rand(100000, 999999);

        $user->sms_verification_code = $code;
        $user->save();

        $this->smsService->sendVerification($user->phone_number, $code);

        return redirect()->route('verification.notice');
    }

    public function verifyCode(Request $request)
    {
        $request->validate(['code' => 'required|numeric']);

        $user = Auth::user();

        if ($user->sms_verification_code == $request->code) {
            $user->sms_verified_at = now();
            $user->save();

            return redirect()->route('dashboard');
        }

        return back()->withErrors(['code' => 'Le code de vérification est incorrect.']);
    }
}