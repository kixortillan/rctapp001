<?php

namespace App\Http\Controllers\Auth;

use App\BL\Email\GalileoMailer;
use App\Http\Controllers\Controller;
use App\Mail\ForgotPassword;
use App\Repositories\Core\Contracts\PasswordResetRepositoryInterface;
use App\Repositories\Core\Contracts\UserRepositoryInterface;
use Illuminate\Http\Request;
use Validator;

class ForgotPasswordController extends Controller
{

    /**
     * Persistence for users
     * @var \App\Repositories\Core\Contracts\UserRepositoryInterface
     */
    protected $user;

    protected $password;

    public function __construct(UserRepositoryInterface $user,
        PasswordResetRepositoryInterface $password) {

        $this->middleware('auth', [
            'except' => [
                'forgotPassword',
            ],
        ]);

        $this->user = $user;
        $this->password = $password;

    }

    public function forgotPassword(Request $request)
    {

        $email = $request->input('email');

        $user = $this->user->userByEmail($email);

        if (!is_null($user)) {

            //create reset token
            $token = hash_hmac('sha256', str_random(40), config('app.key'));
            $this->password->saveToken($user->email, $token);

            //email reset token
            $this->sendResetPasswordEmail($user, $token);
        }

        return response()->json(null, 200);

    }

    private function sendResetPasswordEmail($user, string $token)
    {
        $url = url("/reset/password") . '?r=' . $token;
        $message = "Click here to reset your password {$url}";

        GalileoMailer::send($user->email, 'Password Reset', $message);
    }

    public function validateForgotPassword(Request $request)
    {
        return Validator::make($request->input(), [
            'email' => [
                'bail',
                'required',
                'email',
            ],
        ]);
    }

    public function verifyResetToken(Request $request, $token = null)
    {

        return response()->json();
    }

}
