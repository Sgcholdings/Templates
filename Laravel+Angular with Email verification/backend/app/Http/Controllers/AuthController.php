<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Http\Requests\SignUpRequest;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;
use Illuminate\Mail\Message;
use Validator, DB, Hash, Mail;


class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'signup']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $credentials = request(['email', 'password']);

        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Email or Password does\'t exist'], 401);
        }

        $email = request(['email']);
        $check = DB::table('users')->where('email',$email)->first();
        if(!is_null($check))
        {
            $user = User::find($check->id);

            if($user->is_verified == 0){
                return $this->failedResponse();
            }
            return $this->respondWithToken($token);
        }
    }

    public function signup(SignUpRequest $request) {
        $user = User::create($request->all());

        $verification_code = str_random(30);
        DB::table('user_verifications')->insert(['user_id'=>$user->id,'token'=>$verification_code]);

        $email = $user->email;
        $name = $user->name;

        $subject = "Please verify your email address.";
        Mail::send('email.verify', ['name' => $name, 'verification_code' => $verification_code],
            function($mail) use ($email, $name, $subject){
                $mail->from(getenv('MAIL_FROM_ADDRESS'), "hello@example.com");
                $mail->to($email, $name);
                $mail->subject($subject);
            });

            return response()->json(['success'=> true, 'message'=> 'Thanks for signing up! Please check your email to complete your registration.']);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    public function failedResponse()
    {
        return response()->json([
            'error' => 'Your Account is not verified, please check your email to verify',
        ], Response::HTTP_NOT_FOUND);
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user()->name
        ]);
    }
}
