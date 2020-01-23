<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use App\Role;
use Illuminate\Support\Facades\Hash;
use App\Traits\GlobalFunctions;
use App\Traits\NotificationFunctions;
use App\Traits\RoleServices;
use App\Traits\LogServices;

class RoleController extends Controller
{
    use GlobalFunctions, NotificationFunctions, RoleServices, LogServices;
    private $controllerName = '[RoleController]';
    /**
     * @OA\Get(
     *      path="/api/role",
     *      operationId="getRoles",
     *      tags={"RoleControllerService"},
     *      summary="Get list of roles",
     *      description="Returns list of roles",
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
     *          description="Successfully retrieved list of roles"
     *       ),
     *       @OA\Response(
     *          response="default",
     *          description="Unable to retrieve list of roles")
     *    )
     */
    public function index(Request $request)
    {
        error_log('Retrieving list of roles.');
        // api/role (GET)
        $roles = $this->getRoles($request->user());
        if ($this->isEmpty($roles)) {
            return $this->errorPaginateResponse('Roles');
        } else {
            return $this->successPaginateResponse('Roles', $roles, $this->toInt($request->pageSize), $this->toInt($request->pageNumber));
        }
    }
    
    /**
     * @OA\Get(
     *      path="/api/filter/role",
     *      operationId="filterRoles",
     *      tags={"RoleControllerService"},
     *      summary="Filter list of roles",
     *      description="Returns list of filtered roles",
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
     *     description="To date for filter",
     *     @OA\Schema(type="string")
     *   ),
     *   @OA\Parameter(
     *     name="status",
     *     in="query",
     *     description="status for filter",
     *     @OA\Schema(type="string")
     *   ),
     *      @OA\Response(
     *          response=200,
     *          description="Successfully retrieved list of filtered roles"
     *       ),
     *       @OA\Response(
     *          response="default",
     *          description="Unable to retrieve list of roles")
     *    )
     */
    public function filter(Request $request)
    {
        error_log('Retrieving list of filtered roles.');
        // api/role/filter (GET)
        $params = collect([
            'keyword' => $request->keyword,
            'fromdate' => $request->fromdate,
            'todate' => $request->todate,
            'status' => $request->status,
            'role_id' => $request->role_id,
        ]);
        //Convert To Json Object
        $params = json_decode(json_encode($params));
        $roles = $this->getRoles($request->user());
        $roles = $this->filterRoles($roles, $params);

        if ($this->isEmpty($roles)) {
            return $this->errorPaginateResponse('Roles');
        } else {
            return $this->successPaginateResponse('Roles', $roles, $this->toInt($request->pageSize), $this->toInt($request->pageNumber));
        }

    }

   
    /**
     * @OA\Get(
     *   tags={"RoleControllerService"},
     *   path="/api/role/{uid}",
     *   summary="Retrieves role by Uid.",
     *     operationId="getRoleByUid",
     *   @OA\Parameter(
     *     name="uid",
     *     in="path",
     *     description="Role_ID, NOT 'ID'.",
     *     required=true,
     *     @OA\Schema(type="string")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Role has been retrieved successfully."
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Unable to retrieve the role."
     *   )
     * )
     */
    public function show(Request $request, $uid)
    {
        // api/role/{roleid} (GET)
        error_log('Retrieving role of uid:' . $uid);
        $role = $this->getRole($uid);
        if ($this->isEmpty($role)) {
            return $this->notFoundResponse('Role');
        } else {
            return $this->successResponse('Role', $role, 'retrieve');
        }
    }

  
    /**
     * @OA\Post(
     *   tags={"RoleControllerService"},
     *   path="/api/role",
     *   summary="Creates a role.",
     *   operationId="createRole",
     * @OA\Parameter(
     * name="name",
     * in="query",
     * description="Role Name",
     * required=true,
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="desc",
     * in="query",
     * description="Role Description",
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     *   @OA\Response(
     *     response=200,
     *     description="Role has been created successfully."
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Unable to create the role."
     *   )
     * )
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        // Can only be used by Authorized personnel
        // api/role (POST)
        
        $this->validate($request, [
            'name' => 'required|string|max:191',
            'desc' => 'nullable',
        ]);
        error_log('Creating role.');
        $params = collect([
            'name' => $request->name,
            'desc' => $request->desc,
        ]);
        //Convert To Json Object
        $params = json_decode(json_encode($params));
        $role = $this->createRole($params);

        if ($this->isEmpty($role)) {
            DB::rollBack();
            return $this->errorResponse();
        } else {
            DB::commit();
            return $this->successResponse('Role', $role, 'create');
        }
    }


    /**
     * @OA\Put(
     *   tags={"RoleControllerService"},
     *   path="/api/role/{uid}",
     *   summary="Update role by Uid.",
     *     operationId="updateRoleByUid",
     *   @OA\Parameter(
     *     name="uid",
     *     in="path",
     *     description="Role_ID, NOT 'ID'.",
     *     required=true,
     *     @OA\Schema(type="string")
     *   ),
     * @OA\Parameter(
     * name="name",
     * in="query",
     * description="Rolename",
     * required=true,
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="desc",
     * in="query",
     * description="Role Description",
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     *   @OA\Response(
     *     response=200,
     *     description="Role has been updated successfully."
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Unable to update the role."
     *   )
     * )
     */
    public function update(Request $request, $uid)
    {
        DB::beginTransaction();
        // api/role/{roleid} (PUT) 
        error_log('Updating role of uid: ' . $uid);
        $role = $this->getRole($uid);
       
       
        $this->validate($request, [
            'name' => 'required|string|max:191',
            'desc' => 'nullable',
        ]);
      
        if ($this->isEmpty($role)) {
            DB::rollBack();
            return $this->notFoundResponse('Role');
        }
        
        $params = collect([
            'name' => $request->name,
            'desc' => $request->desc,
        ]);
        
        //Convert To Json Object
        $params = json_decode(json_encode($params));
        $role = $this->updateRole($role, $params);
        if ($this->isEmpty($role)) {
            DB::rollBack();
            return $this->errorResponse();
        } else {
            DB::commit();
            return $this->successResponse('Role', $role, 'update');
        }
    }

    /**
     * @OA\Delete(
     *   tags={"RoleControllerService"},
     *   path="/api/role/{uid}",
     *   summary="Set role's 'status' to 0.",
     *     operationId="deleteRoleByUid",
     *   @OA\Parameter(
     *     name="uid",
     *     in="path",
     *     description="Role ID, NOT 'ID'.",
     *     required=true,
     *     @OA\SChema(type="string")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Role has been 'deleted' successfully."
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Unable to 'delete' the role."
     *   )
     * )
     */
    public function destroy(Request $request, $uid)
    {
        DB::beginTransaction();
        // TODO ONLY TOGGLES THE status = 1/0
        // api/role/{roleid} (DELETE)
        error_log('Deleting role of uid: ' . $uid);
        $role = $this->getRole($uid);
        if ($this->isEmpty($role)) {
            DB::rollBack();
            return $this->notFoundResponse('Role');
        }
        $role = $this->deleteRole($role);
        $this->createLog($request->user()->id , [$role->id], 'delete', 'role');
        if ($this->isEmpty($role)) {
            DB::rollBack();
            return $this->errorResponse();
        } else {
            DB::commit();
            return $this->successResponse('Role', $role, 'delete');
        }
    }

}
