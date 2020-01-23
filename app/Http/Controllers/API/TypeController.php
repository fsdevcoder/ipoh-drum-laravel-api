<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use App\Type;
use Illuminate\Support\Facades\Hash;
use App\Traits\GlobalFunctions;
use App\Traits\NotificationFunctions;
use App\Traits\TypeServices;
use App\Traits\LogServices;

class TypeController extends Controller
{
    use GlobalFunctions, NotificationFunctions, TypeServices, LogServices;
    private $controllerName = '[TypeController]';

    /**
     * @OA\Get(
     *      path="/api/type",
     *      operationId="getTypes",
     *      tags={"TypeControllerService"},
     *      summary="Get list of types",
     *      description="Returns list of types",
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
     *          description="Successfully retrieved list of types"
     *       ),
     *       @OA\Response(
     *          response="default",
     *          description="Unable to retrieve list of types")
     *    )
     */
    public function index(Request $request)
    {
        error_log('Retrieving list of types.');
        // api/type (GET)
        $types = $this->getTypes($request->user());
        if ($this->isEmpty($types)) {
            return $this->errorPaginateResponse('Types');
        } else {
            return $this->successPaginateResponse('Types', $types, $this->toInt($request->pageSize), $this->toInt($request->pageNumber));
        }
    }
    
    /**
     * @OA\Get(
     *      path="/api/filter/type",
     *      operationId="filterTypes",
     *      tags={"TypeControllerService"},
     *      summary="Filter list of types",
     *      description="Returns list of filtered types",
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
     *          description="Successfully retrieved list of filtered types"
     *       ),
     *       @OA\Response(
     *          response="default",
     *          description="Unable to retrieve list of types")
     *    )
     */
    public function filter(Request $request)
    {
        error_log('Retrieving list of filtered types.');
        // api/type/filter (GET)
        $params = collect([
            'keyword' => $request->keyword,
            'fromdate' => $request->fromdate,
            'todate' => $request->todate,
            'status' => $request->status,
            'type_id' => $request->type_id,
        ]);
        //Convert To Json Object
        $params = json_decode(json_encode($params));
        $types = $this->getTypes($request->user());
        $types = $this->filterTypes($types, $params);

        if ($this->isEmpty($types)) {
            return $this->errorPaginateResponse('Types');
        } else {
            return $this->successPaginateResponse('Types', $types, $this->toInt($request->pageSize), $this->toInt($request->pageNumber));
        }

    }

   
    /**
     * @OA\Get(
     *   tags={"TypeControllerService"},
     *   path="/api/type/{uid}",
     *   summary="Retrieves type by Uid.",
     *     operationId="getTypeByUid",
     *   @OA\Parameter(
     *     name="uid",
     *     in="path",
     *     description="Type_ID, NOT 'ID'.",
     *     required=true,
     *     @OA\Schema(type="string")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Type has been retrieved successfully."
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Unable to retrieve the type."
     *   )
     * )
     */
    public function show(Request $request, $uid)
    {
        // api/type/{typeid} (GET)
        error_log('Retrieving type of uid:' . $uid);
        $type = $this->getType($uid);
        if ($this->isEmpty($type)) {
            return $this->notFoundResponse('Type');
        } else {
            return $this->successResponse('Type', $type, 'retrieve');
        }
    }

  
    /**
     * @OA\Post(
     *   tags={"TypeControllerService"},
     *   path="/api/type",
     *   summary="Creates a type.",
     *   operationId="createType",
     * @OA\Parameter(
     * name="name",
     * in="query",
     * description="Type name",
     * required=true,
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="desc",
     * in="query",
     * description="Type Description",
     * required=true,
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="icon",
     * in="query",
     * description="Icon",
     * required=true,
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     *   @OA\Response(
     *     response=200,
     *     description="Type has been created successfully."
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Unable to create the type."
     *   )
     * )
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        // Can only be used by Authorized personnel
        // api/type (POST)
        $this->validate($request, [
            'name' => 'required|string',
            'desc' => 'required|string',
            'icon' => 'required|string',
        ]);
        error_log('Creating type.');
        $params = collect([
            'name' => $request->name,
            'desc' => $request->desc,
            'icon' => $request->icon,
        ]);
        //Convert To Json Object
        $params = json_decode(json_encode($params));
        $type = $this->createType($params);

        if ($this->isEmpty($type)) {
            DB::rollBack();
            return $this->errorResponse();
        } else {
            DB::commit();
            return $this->successResponse('Type', $type, 'create');
        }
    }


    /**
     * @OA\Put(
     *   tags={"TypeControllerService"},
     *   path="/api/type/{uid}",
     *   summary="Update type by Uid.",
     *     operationId="updateTypeByUid",
     *   @OA\Parameter(
     *     name="uid",
     *     in="path",
     *     description="Type_ID, NOT 'ID'.",
     *     required=true,
     *     @OA\Schema(type="string")
     *   ),
     * * @OA\Parameter(
     * name="name",
     * in="query",
     * description="Type name",
     * required=true,
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="desc",
     * in="query",
     * description="Type Description",
     * required=true,
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="icon",
     * in="query",
     * description="Icon",
     * required=true,
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     *   @OA\Response(
     *     response=200,
     *     description="Type has been updated successfully."
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Unable to update the type."
     *   )
     * )
     */
    public function update(Request $request, $uid)
    {
        DB::beginTransaction();
        // api/type/{typeid} (PUT)
        error_log('Updating type of uid: ' . $uid);
        $type = $this->getType($uid);
        error_log($type);
        $this->validate($request, [
            'name' => 'required|string',
            'desc' => 'required|string',
            'icon' => 'required|string',
        ]);

        if ($this->isEmpty($type)) {
            DB::rollBack();
            return $this->notFoundResponse('Type');
        }

        $params = collect([
            'name' => $request->name,
            'desc' => $request->desc,
            'icon' => $request->icon,
        ]);
        //Convert To Json Object
        $params = json_decode(json_encode($params));
        $type = $this->updateType($type, $params);
        if ($this->isEmpty($type)) {
            DB::rollBack();
            $data['data'] = null;
            return $this->errorResponse();
        } else {
            DB::commit();
            return $this->successResponse('Type', $type, 'update');
        }
    }


    /**
     * @OA\Delete(
     *   tags={"TypeControllerService"},
     *   path="/api/type/{uid}",
     *   summary="Set type's 'status' to 0.",
     *     operationId="deleteTypeByUid",
     *   @OA\Parameter(
     *     name="uid",
     *     in="path",
     *     description="Type ID, NOT 'ID'.",
     *     required=true,
     *     @OA\SChema(type="string")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Type has been 'deleted' successfully."
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Unable to 'delete' the type."
     *   )
     * )
     */
    public function destroy(Request $request, $uid)
    {
        DB::beginTransaction();
        // TODO ONLY TOGGLES THE status = 1/0
        // api/type/{typeid} (DELETE)
        error_log('Deleting type of uid: ' . $uid);
        $type = $this->getType($uid);
        if ($this->isEmpty($type)) {
            DB::rollBack();
            return $this->notFoundResponse('Type');
        }
        $type = $this->deleteType($type);
        $this->createLog($request->user()->id , [$type->id], 'delete', 'type');
        if ($this->isEmpty($type)) {
            DB::rollBack();
            return $this->errorResponse();
        } else {
            DB::commit();
            return $this->successResponse('Type', $type, 'delete');
        }
    }

}
