<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Core\Role;
use App\Models\Core\UserRole;
use App\Repositories\Core\Contracts\RoleRepositoryInterface;
use App\Repositories\Core\Contracts\UserRepositoryInterface;
use App\Repositories\Core\Contracts\UserRoleRepositoryInterface;
use App\Transformers\User\AccountTransformer;
use App\Transformers\User\RoleTransformer;
use App\User;
use App\Utilities\FractalResponse;
use App\Utilities\Pagination\DatabasePager;
use App\Utilities\Rest\JsonResponseFormatter;
use App\Utilities\Rest\ResourceFactory;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Log;

class AccountController extends Controller
{
    /**
     * Repository for users
     * @var \App\Repositories\Core\Contracts\UserRepositoryInterface
     */
    protected $users;

    /**
     * Repository for user_role
     * @var \App\Repositories\Core\Contracts\UserRoleRepositoryInterface
     */
    protected $userRoles;

    /**
     * Repository for roles
     * @var \App\Repositories\Core\Contracts\RoleRepositoryInterface
     */
    protected $roles;

    public function __construct(UserRepositoryInterface $users, UserRoleRepositoryInterface $userRoles, RoleRepositoryInterface $roles)
    {
        $this->users = $users;
        $this->userRoles = $userRoles;
        $this->roles = $roles;
    }

    /**
     * [user description]
     * @param  Request $request [description]
     * @return [type]           [description]
     */
    public function user(Request $request)
    {
        $user = $request->user();

        $roles = $this->users->rolesByUser($user->id);

        $response = new JsonResponseFormatter([
            'user' => ResourceFactory::toArray($user, AccountTransformer::class),
            'roles' => ResourceFactory::toArray($roles->roles, RoleTransformer::class),
        ]);

        return response()->json($response->format());
    }

    /**
     * Shows list of employees only.
     * (admins not included)
     *
     * @param  \Illuminate\Http\Request $reqest
     * @return \Illuminate\Http\Response
     */
    public function employeesOnly(Request $request)
    {
        Log::debug('Parameters:');
        Log::debug(print_r($request->all(), true));

        \DB::enableQueryLog();

        $perPage = $request->query('per_page', 7);
        $page = $request->query('page', 1);
        $orderBy = $request->query('sort_by', 'created_at');
        $order = $request->query('sort', 'desc');
        $search = $request->query('search', null);

        $records = $this->users
            ->paginateUsersWithRoles(
                [3],
                new DatabasePager($page, $perPage, $orderBy, $order, $search, ['name', 'desc'])
            );
        $totalRec = $this->userRoles->countTotalUsers(3);

        $paginator = new LengthAwarePaginator($records, $totalRec, $perPage, $page, ['path' => 'users/employees']);

        Log::debug(\DB::getQueryLog());

        return response()
            ->json((new FractalResponse($paginator, AccountTransformer::class, 'roles'))->output());
    }

    /**
     * Shows list of admins only.
     * (employees and others not included)
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function adminsOnly(Request $request)
    {
        Log::debug('Parameters:');
        Log::debug(print_r($request->all(), true));

        \DB::enableQueryLog();

        $perPage = $request->query('per_page', 7);
        $page = $request->query('page', 1);
        $orderBy = $request->query('sort_by', 'created_at');
        $order = $request->query('sort', 'desc');
        $search = $request->query('search', null);

        $records = $this->users
            ->paginateUsersWithRoles(
                [4],
                new DatabasePager($page, $perPage, $orderBy, $order, $search, ['name', 'desc'])
            );
        $totalRec = $this->userRoles->countTotalUsers(4);

        $paginator = new LengthAwarePaginator($records, $totalRec, $perPage, $page, ['path' => 'users/admins']);

        Log::debug(\DB::getQueryLog());

        return response()
            ->json((new FractalResponse($paginator, AccountTransformer::class, 'roles'))->output());
    }

    /**
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function viewAccountInfo(Request $request, $username)
    {
        Log::debug('Parameters:');
        Log::debug(print_r($request->all(), true));

        $user = $this->users->userByUsername(substr($username, 1));

        if (is_null($user)) {
            return response()->json([
                'error' => 'User not found.',
            ], 404);
        }

        $roles = $this->users->rolesByUser($user->id);

        $response = new JsonResponseFormatter([
            'user' => ResourceFactory::toArray($user, AccountTransformer::class),
            'roles' => ResourceFactory::toArray($roles->roles, RoleTransformer::class),
        ]);

        return response()->json($response->format());
    }

    /**
     * @param  Request
     * @param  [type]
     * @return [type]
     */
    public function viewEmployeeInfo(Request $request, $id)
    {
        $user = $this->users->userById($id);

        $roles = $this->userRoles->rolesByUserId($user->id);
        $role = $roles->first();

        return view('user_accounts.view', compact('user', 'role'));
    }

    /**
     * Assign the new roles to user
     *
     * @param  \Illuminate\Http\Request $request [description]
     * @param  int  $id      user ID of user being updated
     * @return \Illuminate\Http\Response
     */
    public function updateAssignedRoles(Request $request, $id)
    {
        Log::debug('Parameters:');
        Log::debug(print_r($request->input(), true));

        $updatedRoles = $request->input('roles');
        $userId = $request->input('user_id');

        $currentRoleRecords = $this->userRoles->rolesByUserIdWithDeleted($userId);
        Log::debug('Existing role records:');
        Log::debug(print_r($currentRoleRecords->pluck('role_id')->toArray(), true));

        $noRecordYet = array_diff($updatedRoles, $currentRoleRecords->pluck('role_id')->toArray());
        Log::debug('New Roles:');
        Log::debug(print_r($noRecordYet, true));

        //clear current user roles
        $this->userRoles->removeRolesForUser($userId);

        //restore user roles that should be retained
        $this->userRoles->restoreDeletedRolesForUser($userId, $updatedRoles);

        //add new user roles with no record yet
        $this->userRoles->addRolesForUser($userId, $noRecordYet);

        return redirect()->back()->with([
            'message' => [
                'status' => 'success',
                'text' => 'Roles and permission has been updated.',
            ],
        ]);
    }

    /**
     * [updateProfilePic description]
     * @param  Request $request [description]
     * @param  [type]  $id      [description]
     * @return [type]           [description]
     */
    public function updateProfilePic(Request $request)
    {
        Log::debug('Parameters:');
        Log::debug(print_r($request->input(), true));

        //validate data
        $userId = $request->input('user_id');

        //save locally
        $profilePicPath = $request->file('profile_pic')->store('avatar', 'public');
        Log::debug('Profile Pic Path:');
        Log::debug($profilePicPath);

        //update avatar of user
        $user = $this->users->updateProfilePic($userId, $profilePicPath);

        //create audit trail
        //TODO

        return redirect()->back();
    }

    public function updatePasswordShowForm(Request $request)
    {
        return view('user_accounts.passwords.update');
    }

    public function updatePassword(Request $request)
    {
        Log::debug('Parameters:');
        Log::debug(print_r($request->input(), true));

        //validate data
        $this->validate($request, [
            'current_password' => 'bail|required|string|min:8|max:150',
            'new_password' => 'bail|required|string|min:8|max:150|confirmed',
            'new_password_confirmation' => 'bail|required|string',
        ]);

        //update password in database
        $user = Auth::user();
        $user->password = bcrypt($request->input('new_password'));
        $user->save();

        //create audit trail
        //TODO

        //email that a password change is done
        //optional

        return back()->with([
            'message' => [
                'status' => 'success',
                'text' => 'Your password has been changed.',
            ],
        ]);
    }
}
