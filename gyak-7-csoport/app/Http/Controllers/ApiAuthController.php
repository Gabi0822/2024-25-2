<?php

 namespace App\Http\Controllers;

 use App\Models\User;
 use Illuminate\Http\Request;

 use Illuminate\Support\Facades\Validator;
  use Illuminate\Validation\Rules\Password;

 class ApiAuthController extends Controller
 {
     function register(Request $request) {
         $validator = Validator::make(
             $request->all(),
             [
                 'name' => 'required|string',
                 'email' => 'required|string|email|unique:users,email',
                 'password' => ['required', 'string', Password::min(8)->letters()->mixedCase()->numbers()],
             ],
             [
                 'required' => ':attribute mező megadása kötelező!',
                 'string' => ':attribute mezo csak szoveges lehet!',
                 'email' => ':attribute mezo csak helyesen formazott email lehet!',
                 'unique' => ':attribute cim mar foglalt!',
             ],
             [
                 'name' => 'A nev',
                 'email' => 'Az email',
                 'password' => 'A jelszo',
             ]
             );
             if($validator->fails()) {
                 return response()->json([
                     'error' => $validator->messages(),
                 ], 400);
             }

             $validated = $validator->validated();

             $user = User::create($validated);

             $token = $user->createToken('auth', $user->admin ? ['ticket:admin'] : ['ticket:user']);

             return response()->json([
                 'token' => $token->plainTextToken,
                 'raw' => $token,
             ], 201);
     }
 }
