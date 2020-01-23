<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use App\Group;
use Illuminate\Support\Facades\Hash;
use App\Traits\GlobalFunctions;
use App\Traits\NotificationFunctions;
use App\Traits\GroupServices;
use App\Traits\LogServices;

class GroupController extends Controller
{
    use GlobalFunctions, NotificationFunctions, GroupServices, LogServices;
    private $controllerName = '[GroupController]';
     /**
     * @OA\Get(
     *      path="/api/group",
     *      operationId="getGroups",
     *      tags={"GroupControllerService"},
     *      summary="Get list of groups",
     *      description="Returns list of groups",
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
     *          description="Successfully retrieved list of groups"
     *       ),
     *       @OA\Response(
     *          response="default",
     *          description="Unable to retrieve list of groups")
     *    )
     */
    public function index(Request $request)
    {
        error_log('Retrieving list of groups.');
        // api/group (GET)
        $groups = $this->getGroups($request->user());
       
        if ($this->isEmpty($groups)) {
            return $this->errorPaginateResponse('Groups');
        } else {
            return $this->successPaginateResponse('Groups', $groups, $this->toInt($request->pageSize), $this->toInt($request->pageNumber));
        }
    }
    
    /**
     * @OA\Get(
     *      path="/api/filter/group",
     *      operationId="filterGroups",
     *      tags={"GroupControllerService"},
     *      summary="Filter list of groups",
     *      description="Returns list of filtered groups",
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
     *      @OA\Response(
     *          response=200,
     *          description="Successfully retrieved list of filtered groups"
     *       ),
     *       @OA\Response(
     *          response="default",
     *          description="Unable to retrieve list of groups")
     *    )
     */
    public function filter(Request $request)
    {
        error_log('Retrieving list of filtered groups.');
        // api/group/filter (GET)
        $params = collect([
            'keyword' => $request->keyword,
            'fromdate' => $request->fromdate,
            'todate' => $request->todate,
            'status' => $request->status,
            'group_id' => $request->group_id,
        ]);
        //Convert To Json Object
        $params = json_decode(json_encode($params));
        $groups = $this->getGroups($request->user());
        $groups = $this->filterGroups($groups, $params);

        if ($this->isEmpty($groups)) {
            return $this->errorPaginateResponse('Groups');
        } else {
            return $this->successPaginateResponse('Groups', $groups, $this->toInt($request->pageSize), $this->toInt($request->pageNumber));
        }

    }

   
    /**
     * @OA\Get(
     *   tags={"GroupControllerService"},
     *   path="/api/group/{uid}",
     *   summary="Retrieves group by Uid.",
     *     operationId="getGroupByUid",
     *   @OA\Parameter(
     *     name="uid",
     *     in="path",
     *     description="Group_ID, NOT 'ID'.",
     *     required=true,
     *     @OA\Schema(type="string")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Group has been retrieved successfully."
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Unable to retrieve the group."
     *   )
     * )
     */
    public function show(Request $request, $uid)
    {
        // api/group/{groupid} (GET)
        error_log('Retrieving group of uid:' . $uid);
        $group = $this->getGroup($uid);
        if ($this->isEmpty($group)) {
            return $this->notFoundResponse('Group');
        } else {
            return $this->successResponse('Group', $group, 'retrieve');
        }
    }

   
    /**
     * @OA\Post(
     *   tags={"GroupControllerService"},
     *   path="/api/group",
     *   summary="Creates a group.",
     *   operationId="createGroup",
     * @OA\Parameter(
     * name="company_id",
     * in="query",
     * description="Group belongs to which company",
     * required=true,
     * @OA\Schema(
     *              type="integer"
     *          )
     * ),
     * @OA\Parameter(
     * name="name",
     * in="query",
     * description="Group Name",
     * required=true,
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="desc",
     * in="query",
     * description="Group Description",
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     *   @OA\Response(
     *     response=200,
     *     description="Group has been created successfully."
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Unable to create the group."
     *   )
     * )
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        // Can only be used by Authorized personnel
        // api/group (POST)
        
        $this->validate($request, [
            'company_id' => 'required|integer',
            'name' => 'required|string|max:191',
            'desc' => 'nullable',
        ]);
        error_log('Creating group.');
        $params = collect([
            'name' => $request->name,
            'desc' => $request->desc,
            'company_id' => $request->company_id,
        ]);
        //Convert To Json Object
        $params = json_decode(json_encode($params));
        $group = $this->createGroup($params);

        if ($this->isEmpty($group)) {
            DB::rollBack();
            return $this->errorResponse();
        } else {
            DB::commit();
            return $this->successResponse('Group', $group, 'create');
        }
    }


    /**
     * @OA\Put(
     *   tags={"GroupControllerService"},
     *   path="/api/group/{uid}",
     *   summary="Update group by Uid.",
     *     operationId="updateGroupByUid",
     *   @OA\Parameter(
     *     name="uid",
     *     in="path",
     *     description="Group_ID, NOT 'ID'.",
     *     required=true,
     *     @OA\Schema(type="string")
     *   ),
     * @OA\Parameter(
     * name="company_id",
     * in="query",
     * description="Group belongs to which company",
     * required=true,
     * @OA\Schema(
     *              type="integer"
     *          )
     * ),
     * @OA\Parameter(
     * name="name",
     * in="query",
     * description="Groupname",
     * required=true,
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="desc",
     * in="query",
     * description="Group Description",
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     *   @OA\Response(
     *     response=200,
     *     description="Group has been updated successfully."
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Unable to update the group."
     *   )
     * )
     */
    public function update(Request $request, $uid)
    {
        DB::beginTransaction();
        // api/group/{groupid} (PUT) 
        error_log('Updating group of uid: ' . $uid);
        $group = $this->getGroup($uid);
       
       
        $this->validate($request, [
            'company_id' => 'required|integer',
            'name' => 'required|string|max:191',
            'desc' => 'nullable',
        ]);
      
        if ($this->isEmpty($group)) {
            DB::rollBack();
            return $this->notFoundResponse('Group');
        }
        
        $params = collect([
            'name' => $request->name,
            'desc' => $request->desc,
            'company_id' => $request->company_id,
        ]);
        
        //Convert To Json Object
        $params = json_decode(json_encode($params));
        $group = $this->updateGroup($group, $params);
        if ($this->isEmpty($group)) {
            DB::rollBack();
            return $this->errorResponse();
        } else {
            DB::commit();
            return $this->successResponse('Group', $group, 'update');
        }
    }


    /**
     * @OA\Delete(
     *   tags={"GroupControllerService"},
     *   path="/api/group/{uid}",
     *   summary="Set group's 'status' to 0.",
     *     operationId="deleteGroupByUid",
     *   @OA\Parameter(
     *     name="uid",
     *     in="path",
     *     description="Group ID, NOT 'ID'.",
     *     required=true,
     *     @OA\SChema(type="string")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Group has been 'deleted' successfully."
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Unable to 'delete' the group."
     *   )
     * )
     */
    public function destroy(Request $request, $uid)
    {
        DB::beginTransaction();
        // TODO ONLY TOGGLES THE status = 1/0
        // api/group/{groupid} (DELETE)
        error_log('Deleting group of uid: ' . $uid);
        $group = $this->getGroup($uid);
        if ($this->isEmpty($group)) {
            DB::rollBack();
            return $this->notFoundResponse('Group');
        }
        $group = $this->deleteGroup($group);
        $this->createLog($request->user()->id , [$group->id], 'delete', 'group');
        if ($this->isEmpty($group)) {
            DB::rollBack();
            return $this->errorResponse();
        } else {
            DB::commit();
            return $this->successResponse('Group', $group, 'delete');
        }
    }


}
