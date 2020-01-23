<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use App\Category;
use Illuminate\Support\Facades\Hash;
use App\Traits\GlobalFunctions;
use App\Traits\NotificationFunctions;
use App\Traits\CategoryServices;
use App\Traits\LogServices;

class CategoryController extends Controller
{
    use GlobalFunctions, NotificationFunctions, CategoryServices, LogServices;
    private $controllerName = '[CategoryController]';
    /**
     * @OA\Get(
     *      path="/api/category",
     *      operationId="getCategories",
     *      tags={"CategoryControllerService"},
     *      summary="Get list of categories",
     *      description="Returns list of categories",
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
     *          description="Successfully retrieved list of categories"
     *       ),
     *       @OA\Response(
     *          response="default",
     *          description="Unable to retrieve list of categories")
     *    )
     */
    public function index(Request $request)
    {
        error_log('Retrieving list of categories.');
        // api/category (GET)
        $categories = $this->getCategories($request->user());
       
        if ($this->isEmpty($categories)) {
            return $this->errorPaginateResponse('Categories');
        } else {
            return $this->successPaginateResponse('Categories', $categories, $this->toInt($request->pageSize), $this->toInt($request->pageNumber));
        }
    }
    
    /**
     * @OA\Get(
     *      path="/api/filter/category",
     *      operationId="filterCategories",
     *      tags={"CategoryControllerService"},
     *      summary="Filter list of categories",
     *      description="Returns list of filtered categories",
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
     *          description="Successfully retrieved list of filtered categories"
     *       ),
     *       @OA\Response(
     *          response="default",
     *          description="Unable to retrieve list of categories")
     *    )
     */
    public function filter(Request $request)
    {
        error_log('Retrieving list of filtered categories.');
        // api/category/filter (GET)
        $params = collect([
            'keyword' => $request->keyword,
            'fromdate' => $request->fromdate,
            'todate' => $request->todate,
            'status' => $request->status,
            'category_id' => $request->category_id,
        ]);
        //Convert To Json Object
        $params = json_decode(json_encode($params));
        $categories = $this->getCategories($request->user());
        $categories = $this->filterCategories($categories, $params);

       
        if ($this->isEmpty($categories)) {
            return $this->errorPaginateResponse('Categories');
        } else {
            return $this->successPaginateResponse('Categories', $categories, $this->toInt($request->pageSize), $this->toInt($request->pageNumber));
        }

    }

   
    /**
     * @OA\Get(
     *   tags={"CategoryControllerService"},
     *   path="/api/category/{uid}",
     *   summary="Retrieves category by Uid.",
     *     operationId="getCategoryByUid",
     *   @OA\Parameter(
     *     name="uid",
     *     in="path",
     *     description="Category_ID, NOT 'ID'.",
     *     required=true,
     *     @OA\Schema(type="string")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Category has been retrieved successfully."
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Unable to retrieve the category."
     *   )
     * )
     */
    public function show(Request $request, $uid)
    {
        // api/category/{categoryid} (GET)
        error_log('Retrieving category of uid:' . $uid);
        $category = $this->getCategory($uid);
        if ($this->isEmpty($category)) {
            $data['data'] = null;
            return $this->notFoundResponse('Category');
        } else {
            return $this->successResponse('Category', $category, 'retrieve');
        }
    }

  
    /**
     * @OA\Post(
     *   tags={"CategoryControllerService"},
     *   path="/api/category",
     *   summary="Creates a category.",
     *   operationId="createCategory",
     * @OA\Parameter(
     * name="name",
     * in="query",
     * description="Category name",
     * required=true,
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="desc",
     * in="query",
     * description="Category Description",
     * required=true,
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     *   @OA\Response(
     *     response=200,
     *     description="Category has been created successfully."
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Unable to create the category."
     *   )
     * )
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        // Can only be used by Authorized personnel
        // api/category (POST)
        $this->validate($request, [
            'name' => 'required|string',
            'desc' => 'required|string',
        ]);
        error_log('Creating category.');
        $params = collect([
            'name' => $request->name,
            'desc' => $request->desc,
        ]);
        //Convert To Json Object
        $params = json_decode(json_encode($params));
        $category = $this->createCategory($params);
        $this->createLog($request->user()->id , [$category->id], 'store', 'category');

        if ($this->isEmpty($category)) {
            DB::rollBack();
            return $this->errorResponse();
        } else {
            DB::commit();
            return $this->successResponse('Category', $category, 'create');
        }
    }


    /**
     * @OA\Put(
     *   tags={"CategoryControllerService"},
     *   path="/api/category/{uid}",
     *   summary="Update category by Uid.",
     *     operationId="updateCategoryByUid",
     *   @OA\Parameter(
     *     name="uid",
     *     in="path",
     *     description="Category_ID, NOT 'ID'.",
     *     required=true,
     *     @OA\Schema(type="string")
     *   ), 
     * * @OA\Parameter(
     * name="name",
     * in="query",
     * description="Category name",
     * required=true,
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="desc",
     * in="query",
     * description="Category Description",
     * required=true,
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="ticketids",
     * in="query",
     * description="Ticket Ids",
     * required=true,
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     *   @OA\Response(
     *     response=200,
     *     description="Category has been updated successfully."
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Unable to update the category."
     *   )
     * )
     */
    public function update(Request $request, $uid)
    {
        DB::beginTransaction();
        // api/category/{categoryid} (PUT) 
        error_log('Updating category of uid: ' . $uid);
        $category = $this->getCategory($uid);
        error_log($category);
        $this->validate($request, [
            'name' => 'required|string',
            'desc' => 'required|string',
            'ticketids' => 'required|string',
        ]);

        if ($this->isEmpty($category)) {
            DB::rollBack();
            return $this->notFoundResponse('Category');
        }
        
        $params = collect([
            'name' => $request->name,
            'desc' => $request->desc,
            'ticketids' => $request->ticketids,
        ]);
        //Convert To Json Object
        $params = json_decode(json_encode($params));
        $category = $this->updateCategory($category, $params);
        $this->createLog($request->user()->id , [$category->id], 'update', 'category');
        if ($this->isEmpty($category)) {
            DB::rollBack();
            return $this->errorResponse();
        } else {
            DB::commit();
            return $this->successResponse('Category', $category, 'update');
        }
    }

    /**
     * @OA\Delete(
     *   tags={"CategoryControllerService"},
     *   path="/api/category/{uid}",
     *   summary="Set category's 'status' to 0.",
     *     operationId="deleteCategoryByUid",
     *   @OA\Parameter(
     *     name="uid",
     *     in="path",
     *     description="Category ID, NOT 'ID'.",
     *     required=true,
     *     @OA\SChema(type="string")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Category has been 'deleted' successfully."
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Unable to 'delete' the category."
     *   )
     * )
     */
    public function destroy(Request $request, $uid)
    {
        DB::beginTransaction();
        // TODO ONLY TOGGLES THE status = 1/0
        // api/category/{categoryid} (DELETE)
        error_log('Deleting category of uid: ' . $uid);
        $category = $this->getCategory($uid);
        if ($this->isEmpty($category)) {
            DB::rollBack();
            return $this->notFoundResponse('Category');
        }
        $category = $this->deleteCategory($category);
        $this->createLog($request->user()->id , [$category->id], 'delete', 'category');
        if ($this->isEmpty($category)) {
            DB::rollBack();
            return $this->errorResponse();
        } else {
            DB::commit();
            return $this->successResponse('Category', null, 'delete');
        }
    }

}
