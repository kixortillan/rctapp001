<?php

namespace App\Http\Controllers\Auth;

use App\BL\Email\GalileoMailer;
use App\Http\Controllers\Controller;
use App\Repositories\Core\Contracts\UserRepositoryInterface;
use App\Repositories\Core\Contracts\UserRoleRepositoryInterface;
use App\Transformers\User\AccountTransformer;
use App\Utilities\FractalResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Log;
use Validator;

class RegisterController extends Controller
{
    /**
     * Persistence for users
     * @var \App\Repositories\Core\Contracts\UserRepositoryInterface
     */
    protected $user;

    /**
     * Persistence for user_role
     * @var \App\Repositories\Core\Contracts\UserRoleRepositoryInterface
     */
    protected $userRoles;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectPath = '/login';

    public function __construct(UserRepositoryInterface $user,
        UserRoleRepositoryInterface $userRoles) {

        $this->middleware('auth', [
            'except' => [
                'verifyRegistration',
            ],
        ]);

        $this->user = $user;
        $this->userRoles = $userRoles;

    }

    /**
     * [createAccount description]
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function createAccount(Request $request)
    {

        $validator = $this->validateCreate($request);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->first(),
            ]);
        }

        //create user
        $user = $this->create($request->input());

        $this->registered($request, $user);

        return response()
            ->json((new FractalResponse($user,
                AccountTransformer::class))->output());
    }

    /**
     * [verifyRegistration description]
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function verifyRegistration(Request $request)
    {
        $token = $request->query('token');

        $user = $this->user->userByVerifyToken($token);

        if (is_null($user)) {
            //no user found
            abort(404);
        }

        if (Carbon::now()->diffInSeconds(
            Carbon::parse($user->token_expires)) < 0) {
            //verify token already expired
            abort(404);
        }

        //clear data and tag as verified
        $user->verified = true;
        $user->verify_token = null;
        $user->token_expires = null;
        $user->save();

        return redirect($this->redirectPath);
    }

    public function validateCreate(Request $request)
    {
        return Validator::make($request->input(), [

            'username' => [
                'bail',
                'required',
                'string',
                'alpha_dash',
                'min:8',
                'max:150',
                Rule::unique('tbl_wa_users')->where(function ($query) {
                    return $query->where('verified', true);
                }),
            ],
            'password' => [
                'bail',
                'required',
                'min:8',
                'max:150',
                'confirmed',
            ],
            'email' => [
                'bail',
                'required',
                'email',
                Rule::unique('tbl_wa_users')->where(function ($query) {
                    return $query->where('verified', true);
                }),
            ],
            'first_name' => [
                'bail',
                'nullable',
                'string',
                'max:150',
            ],
            'middle_name' => [
                'bail',
                'nullable',
                'nullable',
                'max:150',
            ],
            'last_name' => [
                'bail',
                'nullable',
                'string',
                'max:150',
            ],
            'avatar' => [
                'bail',
                'nullable',
                'image',
                'dimensions:min_width=400,min_height=400',
            ],
            'mobile_number' => [
                'bail',
                'nullable',
                'max:150',
            ],

        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        Log::debug('Parameters:');
        Log::debug(print_r($data, true));

        //create the user

        //find if there is a reusable unverified user
        $user = $this->user->userByUsername($data['username'], ['verified' => false]);

        //generate tokens for verification process
        $token = bcrypt('registration' . $data['email'] . $data['username'] . Carbon::now()->timestamp);
        $tokenExpires = Carbon::now()->addDays(7);

        if ($user) {

            //not verified registration is found
            $user->username = $data['username'];
            $user->password = bcrypt($data['password']);
            $user->avatar = null;
            $user->verify_token = $token;
            $user->token_expires = $tokenExpires;
            $user->save();

            //remove previous role selected
            $this->userRoles->removeRolesForUser($user->id);

        } else {

            //email not existing
            $user = $this->user->createUser([
                'username' => $data['username'],
                'email' => $data['email'],
                'password' => bcrypt($data['password']),
                'verify_token' => $token,
                'token_expires' => $tokenExpires,
            ]);

        }

        //add role to user
        $user->roles()->attach([2]);

        return $user;
    }

    /**
     * The user has been registered.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function registered(Request $request, $user)
    {
        // Mail::to($user->email)
        //     ->send(new UserCreated($user));

        // $template = View::make('emails.users.created', ['user' => $user]);
        // $response = GalileoMailer::send($user->email,
        //     'Registration', htmlentities($template));

        $url = url('register/verify') . '?token=' . $user->verify_token;
        $message = "Verify my registration {$url}";

        $response = GalileoMailer::send($user->email,
            'Registration', $message);
    }

}
