<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use App\Module;
use Illuminate\Support\Facades\Hash;
use App\Traits\GlobalFunctions;
use App\Traits\NotificationFunctions;
use App\Traits\ModuleServices;
use App\Traits\LogServices;

class ModuleController extends Controller
{
    use GlobalFunctions, NotificationFunctions, ModuleServices, LogServices;
    private $controllerName = '[ModuleController]';
/**
     * @OA\Get(
     *      path="/api/module",
     *      operationId="getModules",
     *      tags={"ModuleControllerService"},
     *      summary="Get list of modules",
     *      description="Returns list of modules",
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
     *          description="Successfully retrieved list of modules"
     *       ),
     *       @OA\Response(
     *          response="default",
     *          description="Unable to retrieve list of modules")
     *    )
     */
    public function index(Request $request)
    {
        error_log('Retrieving list of modules.');
        // api/module (GET)
        $modules = $this->getModules($request->user());
       
        if ($this->isEmpty($modules)) {
            return $this->errorPaginateResponse('Modules');
        } else {
            return $this->successPaginateResponse('Modules', $modules, $this->toInt($request->pageSize), $this->toInt($request->pageNumber));
        }
    }
    
    /**
     * @OA\Get(
     *      path="/api/filter/module",
     *      operationId="filterModules",
     *      tags={"ModuleControllerService"},
     *      summary="Filter list of modules",
     *      description="Returns list of filtered modules",
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
     *          description="Successfully retrieved list of filtered modules"
     *       ),
     *       @OA\Response(
     *          response="default",
     *          description="Unable to retrieve list of modules")
     *    )
     */
    public function filter(Request $request)
    {
        error_log('Retrieving list of filtered modules.');
        // api/module/filter (GET)
        $params = collect([
            'keyword' => $request->keyword,
            'fromdate' => $request->fromdate,
            'todate' => $request->todate,
            'status' => $request->status,
            'module_id' => $request->module_id,
        ]);
        //Convert To Json Object
        $params = json_decode(json_encode($params));
        $modules = $this->getModules($request->user());
        $modules = $this->filterModules($modules, $params);

        if ($this->isEmpty($modules)) {
            return $this->errorPaginateResponse('Modules');
        } else {
            return $this->successPaginateResponse('Modules', $modules, $this->toInt($request->pageSize), $this->toInt($request->pageNumber));
        }

    }

   
    /**
     * @OA\Get(
     *   tags={"ModuleControllerService"},
     *   path="/api/module/{uid}",
     *   summary="Retrieves module by Uid.",
     *     operationId="getModuleByUid",
     *   @OA\Parameter(
     *     name="uid",
     *     in="path",
     *     description="Module_ID, NOT 'ID'.",
     *     required=true,
     *     @OA\Schema(type="string")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Module has been retrieved successfully."
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Unable to retrieve the module."
     *   )
     * )
     */
    public function show(Request $request, $uid)
    {
        // api/module/{moduleid} (GET)
        error_log('Retrieving module of uid:' . $uid);
        $module = $this->getModule($uid);
        if ($this->isEmpty($module)) {
            return $this->notFoundResponse('Module');
        } else {
            return $this->successResponse('Module', $module, 'retrieve');
        }
    }

  
      
    /**
     * @OA\Post(
     *   tags={"ModuleControllerService"},
     *   path="/api/module",
     *   summary="Creates a module.",
     *   operationId="createModule",
     * @OA\Parameter(
     * name="name",
     * in="query",
     * description="Module Name",
     * required=true,
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="desc",
     * in="query",
     * description="Module Description",
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="provider",
     * in="query",
     * description="Provider of Model",
     * required=true,
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     *   @OA\Response(
     *     response=200,
     *     description="Module has been created successfully."
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Unable to create the module."
     *   )
     * )
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        // Can only be used by Authorized personnel
        // api/module (POST)
        
        $this->validate($request, [
            'name' => 'required|string|max:191',
            'desc' => 'nullable',
            'provider' => 'required|string|max:191',
        ]);
        error_log('Creating module.');
        $params = collect([
            'name' => $request->name,
            'desc' => $request->desc,
            'provider' => $request->provider,
        ]);
        //Convert To Json Object
        $params = json_decode(json_encode($params));
        $module = $this->createModule($params);

        if ($this->isEmpty($module)) {
            DB::rollBack();
            return $this->errorResponse();
        } else {
            DB::commit();
            return $this->successResponse('Module', $module, 'create');
        }
    }


    /**
     * @OA\Put(
     *   tags={"ModuleControllerService"},
     *   path="/api/module/{uid}",
     *   summary="Update module by Uid.",
     *     operationId="updateModuleByUid",
     *   @OA\Parameter(
     *     name="uid",
     *     in="path",
     *     description="Module_ID, NOT 'ID'.",
     *     required=true,
     *     @OA\Schema(type="string")
     *   ),
     * @OA\Parameter(
     * name="name",
     * in="query",
     * description="Modulename",
     * required=true,
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="desc",
     * in="query",
     * description="Module Description",
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="provider",
     * in="query",
     * description="Provider of Model",
     * required=true,
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     *   @OA\Response(
     *     response=200,
     *     description="Module has been updated successfully."
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Unable to update the module."
     *   )
     * )
     */
    public function update(Request $request, $uid)
    {
        DB::beginTransaction();
        // api/module/{moduleid} (PUT) 
        error_log('Updating module of uid: ' . $uid);
        $module = $this->getModule($uid);
       
       
        $this->validate($request, [
            'name' => 'required|string|max:191',
            'desc' => 'nullable',
            'provider' => 'required|string|max:191',
        ]);
      
        if ($this->isEmpty($module)) {
            DB::rollBack();
            return $this->notFoundResponse('Module');
        }
        
        $params = collect([
            'name' => $request->name,
            'desc' => $request->desc,
            'provider' => $request->provider,
        ]);
        
        //Convert To Json Object
        $params = json_decode(json_encode($params));
        $module = $this->updateModule($module, $params);
        if ($this->isEmpty($module)) {
            DB::rollBack();
            return $this->errorResponse();
        } else {
            DB::commit();
            return $this->successResponse('Module', $module, 'update');
        }
    }


    /**
     * @OA\Delete(
     *   tags={"ModuleControllerService"},
     *   path="/api/module/{uid}",
     *   summary="Set module's 'status' to 0.",
     *     operationId="deleteModuleByUid",
     *   @OA\Parameter(
     *     name="uid",
     *     in="path",
     *     description="Module ID, NOT 'ID'.",
     *     required=true,
     *     @OA\SChema(type="string")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Module has been 'deleted' successfully."
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Unable to 'delete' the module."
     *   )
     * )
     */
    public function destroy(Request $request, $uid)
    {
        DB::beginTransaction();
        // TODO ONLY TOGGLES THE status = 1/0
        // api/module/{moduleid} (DELETE)
        error_log('Deleting module of uid: ' . $uid);
        $module = $this->getModule($uid);
        if ($this->isEmpty($module)) {
            DB::rollBack();
            return $this->notFoundResponse('Module');
        }
        $module = $this->deleteModule($module);
        $this->createLog($request->user()->id , [$module->id], 'delete', 'module');
        if ($this->isEmpty($module)) {
            DB::rollBack();
            return $this->errorResponse();
        } else {
            DB::commit();
            return $this->successResponse('Module', $module, 'delete');
        }
    }


}
