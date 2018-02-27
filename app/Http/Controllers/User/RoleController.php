<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;

class RoleController extends Controller
{

    /**
     * Updates the role of user
     * @param  Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateUserRole(Request $request)
    {

        return response()->json();
    }

    public function validateUpdateUserRole(Request $request)
    {
        return Validator::make($request->input(), [
            'user_id' => [
                'bail',
                'required',
                '',
            ],
        ]);
    }

}
