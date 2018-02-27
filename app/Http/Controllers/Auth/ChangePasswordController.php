<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Repositories\Core\Contracts\PasswordResetRepositoryInterface;
use App\Repositories\Core\Contracts\UserRepositoryInterface;
use Illuminate\Http\Request;
use Log;
use Validator;

class ChangePasswordController extends Controller
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
                'change',
            ],
        ]);

        $this->user = $user;
        $this->password = $password;

    }

    public function change(Request $request, $token)
    {
        Log::debug('Parameters:');
        Log::debug(print_r($request->all(), true));

        $validator = $this->validateChangePassword($request);

        if ($validator->fails()) {

            return response()->json([
                'error' => $validator->errors()->first(),
            ], 422);

        }

        $reset = $this->password->findUsingToken($token);
        $user = $this->user
            ->userByEmailCredentials(
                $reset->email, bcrypt($request->input('old_password', null)));

        //user is not verified
        if (is_null($user)) {

            return response()->json([
                'error' => 'This credentials are invalid.',
            ], 422);

        }

        //continue update password
        $this->user->updateUserById($user->id, [
            'password' => bcrypt($request->input('new_password')),
        ]);

        //login automatically and return oauth token
        try {

            $response = Authenticate::login($user->email, $request->input('new_password'));

        } catch (GuzzleHttp\Exception\ClientException $ex) {

            throw $ex;
            // $response = $ex->getResponse();
            // $body = json_decode((string) $response->getBody(), true);

            // return response()->json([
            //     'error' => $body['message'],
            // ], 422);

        }

        return response()->json($response);
    }

    public function validateChangePassword(Request $request)
    {

        return Validator::make($request->input(), [

            'old_password' => [
                'bail',
                'required',
                'min:8',
                'max:150',
            ],

            'new_password' => [
                'bail',
                'required',
                'min:8',
                'max:150',
                'confirmed',
            ],

            'new_password_confirmation' => [
                'bail',
                'required',
                'min:8',
                'max:150',
            ],

        ]);

    }

}
