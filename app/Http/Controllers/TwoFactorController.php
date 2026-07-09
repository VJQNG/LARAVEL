<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TwoFactorController extends Controller
{   
    public function enable(Request $request)
    {
        $request->user()->enableTwoFactorAuthentication(); 
        return redirect('/perfil')->with('status', '2FA activado');
    }
    
    public function qr(Request $request)
    {
        return view('auth.two-factor-qr', [
            'qrCode' => $request->user()->twoFactorQrCodeSvg(), 
            'secretKey' => decrypt($request->user()->two_factor_secret), 
        ]); 
    }
}
