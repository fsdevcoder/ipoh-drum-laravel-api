<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use App\User;
use Illuminate\Support\Facades\Hash;
use App\Traits\GlobalFunctions;
use App\Traits\NotificationFunctions;
use App\Traits\UserServices;
use App\Traits\LogServices;

class UserController extends Controller
{
    use GlobalFunctions, NotificationFunctions, UserServices, LogServices;

    private $controllerName = '[UserController]';
    /**
     * @OA\Get(
     *      path="/api/user",
     *      operationId="getUsers",
     *      tags={"UserControllerService"},
     *      summary="Get list of users",
     *      description="Returns list of users",
     *   @OA\Parameter(
     *     name="pageNumber",
     *     in="query",
     *     description="Page number",
     *     @OA\Schema(type="integer")
     *   ),
     *   @OA\Parameter(
     *     name="pageSize",
     *     in="query",
     *     description="number of pageSize",
     *     @OA\Schema(type="integer")
     *   ),
     *      @OA\Response(
     *          response=200,
     *          description="Successfully retrieved list of users"
     *       ),
     *       @OA\Response(
     *          response="default",
     *          description="Unable to retrieve list of users")
     *    )
     */
    public function index(Request $request)
    {
        error_log($this->controllerName.'Retrieving list of users.');
        // api/user (GET)
        $users = $this->getUsers($request->user());
        if ($this->isEmpty($users)) {
            return $this->errorPaginateResponse('Users');
        } else {
            return $this->successPaginateResponse('Users', $users, $this->toInt($request->pageSize), $this->toInt($request->pageNumber));
        }
    }

    /**
     * @OA\Get(
     *      path="/api/filter/user",
     *      operationId="filterUsers",
     *      tags={"UserControllerService"},
     *      summary="Filter list of users",
     *      description="Returns list of filtered users",
     *   @OA\Parameter(
     *     name="pageNumber",
     *     in="query",
     *     description="Page number",
     *     @OA\Schema(type="integer")
     *   ),
     *   @OA\Parameter(
     *     name="pageSize",
     *     in="query",
     *     description="number of pageSize",
     *     @OA\Schema(type="integer")
     *   ),
     *   @OA\Parameter(
     *     name="keyword",
     *     in="query",
     *     description="Keyword for filter",
     *     @OA\Schema(type="string")
     *   ),
     *   @OA\Parameter(
     *     name="fromdate",
     *     in="query",
     *     description="From Date for filter",
     *     @OA\Schema(type="string")
     *   ),
     *   @OA\Parameter(
     *     name="todate",
     *     in="query",
     *     description="To string for filter",
     *     @OA\Schema(type="string")
     *   ),
     *   @OA\Parameter(
     *     name="status",
     *     in="query",
     *     description="status for filter",
     *     @OA\Schema(type="string")
     *   ),
     *   @OA\Parameter(
     *     name="company_id",
     *     in="query",
     *     description="Company id for filter",
     *     @OA\Schema(type="string")
     *   ),
     *      @OA\Response(
     *          response=200,
     *          description="Successfully retrieved list of filtered users"
     *       ),
     *       @OA\Response(
     *          response="default",
     *          description="Unable to retrieve list of users")
     *    )
     */
    public function filter(Request $request)
    {
        error_log($this->controllerName.'Retrieving list of filtered users.');
        // api/user/filter (GET)
        $params = collect([
            'keyword' => $request->keyword,
            'fromdate' => $request->fromdate,
            'todate' => $request->todate,
            'status' => $request->status,
            'company_id' => $request->company_id,
        ]);
        //Convert To Json Object
        $params = json_decode(json_encode($params));
        $users = $this->filterUsers($request->user(), $params);

        if ($this->isEmpty($users)) {
            return $this->errorPaginateResponse('Users');
        } else {
            return $this->successPaginateResponse('Users', $users, $this->toInt($request->pageSize), $this->toInt($request->pageNumber));
        }

    }
    /**
     * @OA\Get(
     *   tags={"UserControllerService"},
     *   path="/api/user/{uid}",
     *   summary="Retrieves user by Uid.",
     *     operationId="getUserByUid",
     *   @OA\Parameter(
     *     name="uid",
     *     in="path",
     *     description="User_ID, NOT 'ID'.",
     *     required=true,
     *     @OA\Schema(type="string")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="User has been retrieved successfully."
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Unable to retrieve the user."
     *   )
     * )
     */
    public function show(Request $request, $uid)
    {
        // api/user/{userid} (GET)
        error_log($this->controllerName.'Retrieving user of uid:' . $uid);
        $user = $this->getUser($request->user(), $uid);
        if ($this->isEmpty($user)) {
            $data['data'] = null;
            return $this->notFoundResponse('User');
        } else {
            return $this->successResponse('User', $user, 'retrieve');
        }
    }

    /**
     * @OA\Post(
     *   tags={"UserControllerService"},
     *   path="/api/user",
     *   summary="Creates a user.",
     *   operationId="createUser",
     * @OA\Parameter(
     * name="name",
     * in="query",
     * description="Username",
     * required=true,
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="email",
     * in="query",
     * description="Email",
     * required=true,
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="password",
     * in="query",
     * description="Password",
     * required=true,
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="password_confirmation",
     * in="query",
     * description="Password Confirmation",
     * required=true,
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="country",
     * in="query",
     * description="Country",
     * @OA\Schema(
     *  type="string"
     *  )
     * ),
     *   @OA\Response(
     *     response=200,
     *     description="User has been created successfully."
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Unable to create the user."
     *   )
     * )
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        // Can only be used by Authorized personnel
        // api/user (POST)
        $this->validate($request, [
            'email' => 'nullable|string|email|max:191|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);
        error_log($this->controllerName.'Creating user.');
        $params = collect([
            'icno' => $request->icno,
            'name' => $request->name,
            'email' => $request->email,
            'tel1' => $request->tel1,
            'tel2' => $request->tel2,
            'address1' => $request->address1,
            'address2' => $request->address2,
            'postcode' => $request->postcode,
            'state' => $request->state,
            'city' => $request->city,
            'country' => $request->country,
            'password' => $request->password,
        ]);
        //Convert To Json Object
        $params = json_decode(json_encode($params));
        $user = $this->createUser($params);

        if ($this->isEmpty($user)) {
            DB::rollBack();
            return $this->errorResponse();
        } else {
            DB::commit();
            return $this->successResponse('User', $user, 'create');
        }
    }

    /**
     * @OA\Put(
     *   tags={"UserControllerService"},
     *   path="/api/user/{uid}",
     *   summary="Update user by Uid.",
     *     operationId="updateUserByUid",
     *   @OA\Parameter(
     *     name="uid",
     *     in="path",
     *     description="User_ID, NOT 'ID'.",
     *     required=true,
     *     @OA\Schema(type="string")
     *   ),
     *   @OA\Parameter(
     *     name="name",
     *     in="query",
     *     description="Username.",
     *     required=true,
     *     @OA\Schema(type="string")
     *   ),
     *  @OA\Parameter(
     *     name="email",
     *     in="query",
     *     description="Email.",
     *     required=true,
     *     @OA\Schema(type="string")
     *   ),
     *  @OA\Parameter(
     *     name="tel1",
     *     in="query",
     *     description="Telephone Number #1.",
     *     required=false,
     *     @OA\Schema(type="string")
     *   ),
     *  @OA\Parameter(
     *     name="address1",
     *     in="query",
     *     description="Address #1.",
     *     required=false,
     *     @OA\Schema(type="string")
     *   ),
     *  @OA\Parameter(
     *     name="city",
     *     in="query",
     *     description="City.",
     *     required=false,
     *     @OA\Schema(type="string")
     *   ),
     *  @OA\Parameter(
     *     name="postcode",
     *     in="query",
     *     description="PostCode.",
     *     required=false,
     *     @OA\Schema(type="string")
     *   ),
     *  @OA\Parameter(
     *     name="state",
     *     in="query",
     *     description="State.",
     *     required=false,
     *     @OA\Schema(type="string")
     *   ),
     *  @OA\Parameter(
     *     name="country",
     *     in="query",
     *     description="Country.",
     *     required=true,
     *     @OA\Schema(type="string")
     *   ),
     *   @OA\Parameter(
     *     name="icno",
     *     in="query",
     *     description="IC Number.",
     *     required=false,
     *     @OA\Schema(type="string")
     *     ),
     *   @OA\Response(
     *     response=200,
     *     description="User has been updated successfully."
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Unable to update the user."
     *   )
     * )
     */
    public function update(Request $request, $uid)
    {
        DB::beginTransaction();
        // api/user/{userid} (PUT)
        error_log($this->controllerName.'Updating user of uid: ' . $uid);
        $user = $this->getUser($uid);
        $this->validate($request, [
            'email' => 'required|string|max:191|unique:users,email,' . $user->id,
            'name' => 'required|string|max:191',
        ]);
        if ($this->isEmpty($user)) {
            DB::rollBack();
            return $this->notFoundResponse('User');
        }
        $params = collect([
            'icno' => $request->icno,
            'name' => $request->name,
            'email' => $request->email,
            'tel1' => $request->tel1,
            'tel2' => $request->tel2,
            'address1' => $request->address1,
            'address2' => $request->address2,
            'postcode' => $request->postcode,
            'state' => $request->state,
            'city' => $request->city,
            'country' => $request->country,
        ]);
        //Convert To Json Object
        $params = json_decode(json_encode($params));
        $user = $this->updateUser($user, $params);
        if ($this->isEmpty($user)) {
            DB::rollBack();
            return $this->errorResponse();
        } else {
            DB::commit();
            return $this->successResponse('User', $user, 'update');
        }
    }

    /**
     * @OA\Delete(
     *   tags={"UserControllerService"},
     *   path="/api/user/{uid}",
     *   summary="Set user's 'status' to 0.",
     *     operationId="deleteUserByUid",
     *   @OA\Parameter(
     *     name="uid",
     *     in="path",
     *     description="User ID, NOT 'ID'.",
     *     required=true,
     *     @OA\SChema(type="string")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="User has been 'deleted' successfully."
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Unable to 'delete' the user."
     *   )
     * )
     */
    public function destroy(Request $request, $uid)
    {
        DB::beginTransaction();
        // TODO ONLY TOGGLES THE status = 1/0
        // api/user/{userid} (DELETE)
        error_log($this->controllerName.'Deleting user of uid: ' . $uid);
        $user = $this->getUser($uid);
        if ($this->isEmpty($user)) {
            DB::rollBack();
            return $this->notFoundResponse('User');
        }
        $user = $this->deleteUser($user);
        if ($this->isEmpty($user)) {
            DB::rollBack();
            return $this->errorResponse();
        } else {
            DB::commit();
            return $this->successResponse('User', $user, 'delete');
        }
    }

    

    /**
     * @OA\Post(
     *   tags={"UserControllerService"},
     *   summary="Authenticates current request's user.",
     *     operationId="authenticateCurrentRequestsUser",
     * path="/api/authentication",
     *   @OA\Response(
     *     response=200,
     *     description="User is already authenticated."
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="User is not authenticated."
     *   )
     * )
     */
    public function authentication(Request $request)
    {
        // TODO Authenticate currently logged in user
        error_log($this->controllerName.'Authenticating user.');
        return response()->json($request->user(), 200);
    }

    /**
     * @OA\Post(
     *   tags={"UserControllerService"},
     *   summary="Creates a user without needing authorization.",
     *     operationId="createUserWithoutAuthorization",
     * path="/api/register",
     *   @OA\Parameter(
     *     name="name",
     *     in="query",
     *     description="Username.",
     *     required=true,
     *     @OA\Schema(type="string")
     *   ),
     *   @OA\Parameter(
     *     name="email",
     *     in="query",
     *     description="Email.",
     *     required=true,
     *     @OA\Schema(type="string")
     *   ),
     *   @OA\Parameter(
     *     name="password",
     *     in="query",
     *     description="Password.",
     *     required=true,
     *     @OA\Schema(type="string")
     *   ),
     *   @OA\Parameter(
     *     name="password_confirmation",
     *     in="query",
     *     description="Confirm Password.",
     *     required=true,
     *     @OA\Schema(type="string")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="User has been successfully created."
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="User is not created."
     *   )
     * )
     */
    public function register(Request $request)
    {
        // TODO Registers users without needing authorization
        error_log($this->controllerName.'Registering user.');
        // api/register (POST)
        $this->validate($request, [
            'name' => 'required|string|max:191',
            'email' => 'required|string|email|max:191|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);
        DB::beginTransaction();
        $user = new User();
        $user->uid = Carbon::now()->timestamp . User::count();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->status = true;
        try {
            DB::commit();
            $user->save();
            $data['status'] = 'success';
            $data['msg'] = $this->getCreatedSuccessMsg('User Account');
            $data['data'] = $user;
            $data['code'] = 200;
            return response()->json($data, 200);
        } catch (Exception $e) {
            DB::rollBack();
            return $this->errorResponse();
        }
    }
}
