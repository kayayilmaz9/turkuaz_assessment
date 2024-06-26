<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $rules = array(
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        );
        $messages = array(
            'name.required' => '- Name parametresi gereklidir',
            'name.string' => '- Name parametresi string tipinde olmalıdır',
            'name.max' => '- Name parametresi max. 255 karakter olmalıdır',
            'email.required' => '- Email parametresi gereklidir',
            'email.string' => '- Email parametresi string tipinde olmalıdır',
            'email.email' => '- Email parametresi geçerli bir email adresi olmalıdır',
            'email.max' => '- Email parametresi max. 255 karakter olmalıdır',
            'email.unique' => '- Bu email ile daha önce kayıt olunmuş, farklı bir email adresi kullanınız',
            'password.required' => '- Password parametresi gereklidir',
            'password.string' => '- Password parametresi string tipinde olmalıdır',
            'password.min' => '- Password parametresi min. 8 karakterden oluşmalıdır'
        );

        $validator = Validator::make($request->all(), $rules,$messages);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'api_token' => Str::random(60),
        ]);

        return response()->json(['token' => $user->api_token], 201);
    }

    public function login(Request $request)
    {

        $rules = array(
            'email' => 'required|string|email',
            'password' => 'required|string',
        );
        $messages = array(
            'email.required' => '- Email parametresi gereklidir',
            'email.string' => '- Email parametresi string tipinde olmalıdır',
            'email.email' => '- Email parametresi geçerli bir email adresi olmalıdır',
            'password.required' => '- Password parametresi gereklidir',
            'password.string' => '- Password parametresi string tipinde olmalıdır'
        );

        $validator = Validator::make($request->all(), $rules,$messages);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $user = User::where('email', $request->input('email'))->first();

        if (! $user || ! Hash::check($request->input('password'), $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Girilen bilgiler hatalı.'],
            ]);
        }

        $user->api_token = Str::random(60);
        $user->save();

        return response()->json(['token' => $user->api_token]);
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        $user->api_token = null;
        $user->save();

        return response()->json(['message' => 'Başarıyla çıkış yapıldı.']);
    }
}
