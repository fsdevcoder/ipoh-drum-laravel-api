<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use App\VoucherCode;
use Illuminate\Support\Facades\Hash;
use App\Traits\GlobalFunctions;
use App\Traits\NotificationFunctions;
use App\Traits\VoucherCodeServices;
use App\Traits\LogServices;

class VoucherCodeController extends Controller
{
    use GlobalFunctions, NotificationFunctions, VoucherCodeServices, LogServices;
    private $controllerName = '[VoucherCodeController]';

    /**
     * @OA\Get(
     *      path="/api/vouchercode",
     *      operationId="getVoucherCodeList",
     *      tags={"VoucherCodeControllerService"},
     *      summary="Get list of vouchercodes",
     *      description="Returns list of vouchercodes",
     *   @OA\Parameter(
     *     name="pageNumber",
     *     in="query",
     *     description="Page number.",
     *     @OA\Schema(type="integer")
     *   ),
     *   @OA\Parameter(
     *     name="pageSize",
     *     in="query",
     *     description="Page size.",
     *     @OA\Schema(type="integer")
     *   ),
     *      @OA\Response(
     *          response=200,
     *          description="Successfully retrieved list of vouchercodes"
     *       ),
     *       @OA\Response(
     *          response="default",
     *          description="Unable to retrieve list of vouchercodes")
     *    )
     */
    public function index(Request $request)
    {
        error_log('Retrieving list of vouchercodes.');
        // api/vouchercode (GET)
        $vouchercodes = $this->getVoucherCodes($request->user());
        if ($this->isEmpty($vouchercodes)) {
            return $this->errorPaginateResponse('Verification Codes');
        } else {
            return $this->successPaginateResponse('Verification Codes', $vouchercodes, $this->toInt($request->pageSize), $this->toInt($request->pageNumber));
        }
    }

    /**
     * @OA\Get(
     *      path="/api/filter/vouchercode",
     *      operationId="filterVoucherCodeList",
     *      tags={"VoucherCodeControllerService"},
     *      summary="Filter list of vouchercodes",
     *      description="Returns list of filtered vouchercodes",
     *   @OA\Parameter(
     *     name="pageNumber",
     *     in="query",
     *     description="Page number",
     *     @OA\Schema(type="integer")
     *   ),
     *   @OA\Parameter(
     *     name="pageSize",
     *     in="query",
     *     description="Page size",
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
     *          description="Successfully retrieved list of filtered vouchercodes"
     *       ),
     *       @OA\Response(
     *          response="default",
     *          description="Unable to retrieve list of vouchercodes")
     *    )
     */
    public function filter(Request $request)
    {
        error_log('Retrieving list of filtered vouchercodes.');
        // api/vouchercode/filter (GET)
        $params = collect([
            'keyword' => $request->keyword,
            'fromdate' => $request->fromdate,
            'todate' => $request->todate,
            'status' => $request->status,
        ]);
        //Convert To Json Object
        $params = json_decode(json_encode($params));
        $vouchercodes = $this->getVoucherCodes($request->user());
        $vouchercodes = $this->filterVoucherCodeListing($request->user(), $params);

        if ($this->isEmpty($vouchercodes)) {
            return $this->errorPaginateResponse('Verification Codes');
        } else {
            return $this->successPaginateResponse('Verification Codes', $vouchercodes, $this->toInt($request->pageSize), $this->toInt($request->pageNumber));
        }

    }

    /**
     * @OA\Get(
     *   tags={"VoucherCodeControllerService"},
     *   path="/api/vouchercode/{uid}",
     *   summary="Retrieves vouchercode by Uid.",
     *     operationId="getVoucherCodeByUid",
     *   @OA\Parameter(
     *     name="uid",
     *     in="path",
     *     description="VoucherCode_ID, NOT 'ID'.",
     *     required=true,
     *     @OA\Schema(type="string")
     *   ),
     *   @OA\Response(
     *     response=200,    
     *     description="VoucherCode has been retrieved successfully."
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Unable to retrieve the vouchercode."
     *   )
     * )
     */
    public function show(Request $request, $uid)
    {
        // api/vouchercode/{vouchercodeid} (GET)
        error_log('Retrieving vouchercode of uid:' . $uid);
        $vouchercode = $this->getVoucherCode($uid);
        if ($this->isEmpty($vouchercode)) {
            $data['data'] = null;
            return $this->notFoundResponse('Verification Code');
        } else {
            return $this->successResponse('Verification Code', $vouchercode, 'retrieve');
        }
    }

    /**
     * @OA\Post(
     *   tags={"VoucherCodeControllerService"},
     *   path="/api/vouchercode",
     *   summary="Creates a vouchercode.",
     *   operationId="createVoucherCode",
     * @OA\Parameter(
     * name="voucher_id",
     * in="query",
     * description="VoucherCode belongs to which Voucher",
     * required=true,
     * @OA\Schema(
     *              type="integer"
     *          )
     * ),
     *   @OA\Response(
     *     response=200,
     *     description="VoucherCode has been created successfully."
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Unable to create the vouchercode."
     *   )
     * )
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        // Can only be used by Authorized personnel
        // api/vouchercode (POST)
        
        $this->validate($request, [
            'voucher_id' => 'required|integer',
        ]);
        error_log('Creating vouchercode.');
        $params = collect([
            'voucher_id' => $request->voucher_id,
        ]);
        //Convert To Json Object
        $params = json_decode(json_encode($params));
        $vouchercode = $this->createVoucherCode($params);

        if ($this->isEmpty($vouchercode)) {
            DB::rollBack();
            return $this->errorResponse();
        } else {
            DB::commit();
            return $this->successResponse('VoucherCode', $vouchercode, 'create');
        }
    }


    /**
     * @OA\Put(
     *   tags={"VoucherCodeControllerService"},
     *   path="/api/vouchercode/{uid}",
     *   summary="Update vouchercode by Uid.",
     *     operationId="updateVoucherCodeByUid",
     *   @OA\Parameter(
     *     name="uid",
     *     in="path",
     *     description="VoucherCode_ID, NOT 'ID'.",
     *     required=true,
     *     @OA\Schema(type="string")
     *   ),
     * @OA\Parameter(
     * name="store_id",
     * in="query",
     * description="VoucherCode belongs to which Store",
     * required=true,
     * @OA\Schema(
     *              type="integer"
     *          )
     * ),
     * @OA\Parameter(
     * name="name",
     * in="query",
     * description="VoucherCode Name",
     * required=true,
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="desc",
     * in="query",
     * description="VoucherCode Description",
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="unlimited",
     * in="query",
     * description="Is This VoucherCode Unlimited?",
     * required=true,
     * @OA\Schema(
     *              type="integer"
     *          )
     * ),
     * @OA\Parameter(
     * name="qty",
     * in="query",
     * description="The limited quantity of vouchercode",
     * @OA\Schema(
     *              type="integer"
     *          )
     * ),
     * @OA\Parameter(
     * name="discbyprice",
     * in="query",
     * description="Is This VoucherCode Discount By Price?",
     * required=true,
     * @OA\Schema(
     *              type="integer"
     *          )
     * ),
     * @OA\Parameter(
     * name="disc",
     * in="query",
     * description="Discount price",
     * @OA\Schema(
     *              type="number"
     *          )
     * ),
     * @OA\Parameter(
     * name="discpctg",
     * in="query",
     * description="Discount percentage",
     * @OA\Schema(
     *              type="integer"
     *          )
     * ),
     * @OA\Parameter(
     * name="minpurchase",
     * in="query",
     * description="Minimum Purchase Price To Apply VoucherCode",
     * @OA\Schema(
     *              type="number"
     *          )
     * ),
     * @OA\Parameter(
     * name="minqty",
     * in="query",
     * description="Minimum Purchase Qty To Apply VoucherCode",
     * @OA\Schema(
     *              type="integer"
     *          )
     * ),
     * @OA\Parameter(
     * name="minvariety",
     * in="query",
     * description="Minimum Item Variety To Apply VoucherCode",
     * @OA\Schema(
     *              type="integer"
     *          )
     * ),
     * @OA\Parameter(
     * name="startdate",
     * in="query",
     * description="VoucherCode Start Date",
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="enddate",
     * in="query",
     * description="VoucherCode End Date",
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     *   @OA\Response(
     *     response=200,
     *     description="VoucherCode has been updated successfully."
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Unable to update the vouchercode."
     *   )
     * )
     */
    public function update(Request $request, $uid)
    {
        DB::beginTransaction();
        // api/vouchercode/{vouchercodeid} (PUT) 
        error_log('Updating vouchercode of uid: ' . $uid);
        
        $this->validate($request, [
            'store_id' => 'required|integer',
            'name' => 'required|string|max:191',
            'discbyprice' => 'required|boolean',
            'unlimited' => 'required|boolean',
        ]);

        $vouchercode = $this->getVoucherCode($uid);
        if ($this->isEmpty($vouchercode)) {
            DB::rollBack();
            $data['data'] = null;
            return $this->notFoundResponse('Verification Code');
        }
        
        $params = collect([
            'store_id' => $request->store_id,
            'name' => $request->name,
            'desc' => $request->desc,
            'unlimited' => $request->unlimited,
            'qty' => $request->qty,
            'discbyprice' => $request->discbyprice,
            'disc' => $request->disc,
            'discpctg' => $request->discpctg,
            'minpurchase' => $request->minpurchase,
            'minqty' => $request->minqty,
            'minvariety' => $request->minvariety,
            'startdate' => $request->startdate,
            'enddate' => $request->enddate,
        ]);
        //Convert To Json Object
        $params = json_decode(json_encode($params));
        $vouchercode = $this->updateVoucherCode($vouchercode, $params);
        if ($this->isEmpty($vouchercode)) {
            DB::rollBack();
            return $this->errorResponse();
        } else {
            DB::commit();
            return $this->successResponse('Verification Code', $vouchercode, 'update');
        }
    }

    /**
     * @OA\Delete(
     *   tags={"VoucherCodeControllerService"},
     *   path="/api/vouchercode/{uid}",
     *   summary="Set vouchercode's 'status' to 0.",
     *     operationId="deleteVoucherCodeByUid",
     *   @OA\Parameter(
     *     name="uid",
     *     in="path",
     *     description="VoucherCode ID, NOT 'ID'.",
     *     required=true,
     *     @OA\SChema(type="string")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="VoucherCode has been 'deleted' successfully."
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Unable to 'delete' the vouchercode."
     *   )
     * )
     */
    public function destroy(Request $request, $uid)
    {
        DB::beginTransaction();
        // TODO ONLY TOGGLES THE status = 1/0
        // api/vouchercode/{vouchercodeid} (DELETE)
        error_log('Deleting vouchercode of uid: ' . $uid);
        $vouchercode = $this->getVoucherCode($uid);
        if ($this->isEmpty($vouchercode)) {
            DB::rollBack();
            return $this->notFoundResponse('Verification Code');
        }
        $vouchercode = $this->deleteVoucherCode($request->user(), $vouchercode->id);
        if ($this->isEmpty($vouchercode)) {
            DB::rollBack();
            return $this->errorResponse();
        } else {
            DB::commit();
            return $this->successResponse('Verification Code', $vouchercode, 'delete');
        }
    }

}
