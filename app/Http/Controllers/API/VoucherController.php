<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use App\Voucher;
use Illuminate\Support\Facades\Hash;
use App\Traits\GlobalFunctions;
use App\Traits\NotificationFunctions;
use App\Traits\VoucherServices;
use App\Traits\LogServices;

class VoucherController extends Controller
{
    use GlobalFunctions, NotificationFunctions, VoucherServices, LogServices;
    private $controllerName = '[VoucherController]';

    /**
     * @OA\Get(
     *      path="/api/voucher",
     *      operationId="getVoucherList",
     *      tags={"VoucherControllerService"},
     *      summary="Get list of vouchers",
     *      description="Returns list of vouchers",
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
     *          description="Successfully retrieved list of vouchers"
     *       ),
     *       @OA\Response(
     *          response="default",
     *          description="Unable to retrieve list of vouchers")
     *    )
     */
    public function index(Request $request)
    {
        error_log('Retrieving list of vouchers.');
        // api/voucher (GET)
        $vouchers = $this->getVouchers($request->user());
        if ($this->isEmpty($vouchers)) {
            return $this->errorPaginateResponse('Voucher Codes');
        } else {
            return $this->successPaginateResponse('Voucher Codes', $vouchers, $this->toInt($request->pageSize), $this->toInt($request->pageNumber));
        }
    }

    /**
     * @OA\Get(
     *      path="/api/filter/voucher",
     *      operationId="filterVoucherList",
     *      tags={"VoucherControllerService"},
     *      summary="Filter list of vouchers",
     *      description="Returns list of filtered vouchers",
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
     *          description="Successfully retrieved list of filtered vouchers"
     *       ),
     *       @OA\Response(
     *          response="default",
     *          description="Unable to retrieve list of vouchers")
     *    )
     */
    public function filter(Request $request)
    {
        error_log('Retrieving list of filtered vouchers.');
        // api/voucher/filter (GET)
        $params = collect([
            'keyword' => $request->keyword,
            'fromdate' => $request->fromdate,
            'todate' => $request->todate,
            'status' => $request->status,
        ]);
        //Convert To Json Object
        $params = json_decode(json_encode($params));
        $vouchers = $this->getVouchers($request->user());
        $vouchers = $this->filterVoucherListing($request->user(), $params);

        if ($this->isEmpty($vouchers)) {
            return $this->errorPaginateResponse('Voucher Codes');
        } else {
            return $this->successPaginateResponse('Voucher Codes', $vouchers, $this->toInt($request->pageSize), $this->toInt($request->pageNumber));
        }

    }

    /**
     * @OA\Get(
     *   tags={"VoucherControllerService"},
     *   path="/api/voucher/{uid}",
     *   summary="Retrieves voucher by Uid.",
     *     operationId="getVoucherByUid",
     *   @OA\Parameter(
     *     name="uid",
     *     in="path",
     *     description="Voucher_ID, NOT 'ID'.",
     *     required=true,
     *     @OA\Schema(type="string")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Voucher has been retrieved successfully."
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Unable to retrieve the voucher."
     *   )
     * )
     */
    public function show(Request $request, $uid)
    {
        // api/voucher/{voucherid} (GET)
        error_log('Retrieving voucher of uid:' . $uid);
        $voucher = $this->getVoucher($uid);
        if ($this->isEmpty($voucher)) {
            $data['data'] = null;
            return $this->notFoundResponse('Voucher Code');
        } else {
            return $this->successResponse('Voucher Code', $voucher, 'retrieve');
        }
    }

    /**
     * @OA\Post(
     *   tags={"VoucherControllerService"},
     *   path="/api/voucher",
     *   summary="Creates a voucher.",
     *   operationId="createVoucher",
     * @OA\Parameter(
     * name="store_id",
     * in="query",
     * description="Voucher belongs to which Store",
     * required=true,
     * @OA\Schema(
     *              type="integer"
     *          )
     * ),
     * @OA\Parameter(
     * name="name",
     * in="query",
     * description="Voucher Name",
     * required=true,
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="desc",
     * in="query",
     * description="Voucher Description",
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="unlimited",
     * in="query",
     * description="Is This Voucher Unlimited?",
     * required=true,
     * @OA\Schema(
     *              type="integer"
     *          )
     * ),
     * @OA\Parameter(
     * name="qty",
     * in="query",
     * description="The limited quantity of voucher",
     * @OA\Schema(
     *              type="integer"
     *          )
     * ),
     * @OA\Parameter(
     * name="discbyprice",
     * in="query",
     * description="Is This Voucher Discount By Price?",
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
     * description="Minimum Purchase Price To Apply Voucher",
     * @OA\Schema(
     *              type="number"
     *          )
     * ),
     * @OA\Parameter(
     * name="minqty",
     * in="query",
     * description="Minimum Purchase Qty To Apply Voucher",
     * @OA\Schema(
     *              type="integer"
     *          )
     * ),
     * @OA\Parameter(
     * name="minvariety",
     * in="query",
     * description="Minimum Item Variety To Apply Voucher",
     * @OA\Schema(
     *              type="integer"
     *          )
     * ),
     * @OA\Parameter(
     * name="startdate",
     * in="query",
     * description="Voucher Start Date",
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="enddate",
     * in="query",
     * description="Voucher End Date",
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     *   @OA\Response(
     *     response=200,
     *     description="Voucher has been created successfully."
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Unable to create the voucher."
     *   )
     * )
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        // Can only be used by Authorized personnel
        // api/voucher (POST)
        
        $this->validate($request, [
            'store_id' => 'required|integer',
            'name' => 'required|string|max:191',
            'discbyprice' => 'required|boolean',
            'unlimited' => 'required|boolean',
        ]);
        error_log('Creating voucher.');
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
        $voucher = $this->createVoucher($params);

        if ($this->isEmpty($voucher)) {
            DB::rollBack();
            return $this->errorResponse();
        } else {
            DB::commit();
            return $this->successResponse('Voucher', $voucher, 'create');
        }
    }


    /**
     * @OA\Put(
     *   tags={"VoucherControllerService"},
     *   path="/api/voucher/{uid}",
     *   summary="Update voucher by Uid.",
     *     operationId="updateVoucherByUid",
     *   @OA\Parameter(
     *     name="uid",
     *     in="path",
     *     description="Voucher_ID, NOT 'ID'.",
     *     required=true,
     *     @OA\Schema(type="string")
     *   ),
     * @OA\Parameter(
     * name="store_id",
     * in="query",
     * description="Voucher belongs to which Store",
     * required=true,
     * @OA\Schema(
     *              type="integer"
     *          )
     * ),
     * @OA\Parameter(
     * name="name",
     * in="query",
     * description="Voucher Name",
     * required=true,
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="desc",
     * in="query",
     * description="Voucher Description",
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="unlimited",
     * in="query",
     * description="Is This Voucher Unlimited?",
     * required=true,
     * @OA\Schema(
     *              type="integer"
     *          )
     * ),
     * @OA\Parameter(
     * name="qty",
     * in="query",
     * description="The limited quantity of voucher",
     * @OA\Schema(
     *              type="integer"
     *          )
     * ),
     * @OA\Parameter(
     * name="discbyprice",
     * in="query",
     * description="Is This Voucher Discount By Price?",
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
     * description="Minimum Purchase Price To Apply Voucher",
     * @OA\Schema(
     *              type="number"
     *          )
     * ),
     * @OA\Parameter(
     * name="minqty",
     * in="query",
     * description="Minimum Purchase Qty To Apply Voucher",
     * @OA\Schema(
     *              type="integer"
     *          )
     * ),
     * @OA\Parameter(
     * name="minvariety",
     * in="query",
     * description="Minimum Item Variety To Apply Voucher",
     * @OA\Schema(
     *              type="integer"
     *          )
     * ),
     * @OA\Parameter(
     * name="startdate",
     * in="query",
     * description="Voucher Start Date",
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="enddate",
     * in="query",
     * description="Voucher End Date",
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     *   @OA\Response(
     *     response=200,
     *     description="Voucher has been updated successfully."
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Unable to update the voucher."
     *   )
     * )
     */
    public function update(Request $request, $uid)
    {
        DB::beginTransaction();
        // api/voucher/{voucherid} (PUT) 
        error_log('Updating voucher of uid: ' . $uid);
        
        $this->validate($request, [
            'store_id' => 'required|integer',
            'name' => 'required|string|max:191',
            'discbyprice' => 'required|boolean',
            'unlimited' => 'required|boolean',
        ]);

        $voucher = $this->getVoucher($uid);
        if ($this->isEmpty($voucher)) {
            DB::rollBack();
            $data['data'] = null;
            return $this->notFoundResponse('Voucher Code');
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
        $voucher = $this->updateVoucher($voucher, $params);
        if ($this->isEmpty($voucher)) {
            DB::rollBack();
            return $this->errorResponse();
        } else {
            DB::commit();
            return $this->successResponse('Voucher Code', $voucher, 'update');
        }
    }

    /**
     * @OA\Delete(
     *   tags={"VoucherControllerService"},
     *   path="/api/voucher/{uid}",
     *   summary="Set voucher's 'status' to 0.",
     *     operationId="deleteVoucherByUid",
     *   @OA\Parameter(
     *     name="uid",
     *     in="path",
     *     description="Voucher ID, NOT 'ID'.",
     *     required=true,
     *     @OA\SChema(type="string")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Voucher has been 'deleted' successfully."
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Unable to 'delete' the voucher."
     *   )
     * )
     */
    public function destroy(Request $request, $uid)
    {
        DB::beginTransaction();
        // TODO ONLY TOGGLES THE status = 1/0
        // api/voucher/{voucherid} (DELETE)
        error_log('Deleting voucher of uid: ' . $uid);
        $voucher = $this->getVoucher($uid);
        if ($this->isEmpty($voucher)) {
            DB::rollBack();
            return $this->notFoundResponse('Voucher Code');
        }
        $voucher = $this->deleteVoucher($request->user(), $voucher->id);
        if ($this->isEmpty($voucher)) {
            DB::rollBack();
            return $this->errorResponse();
        } else {
            DB::commit();
            return $this->successResponse('Voucher Code', $voucher, 'delete');
        }
    }

}
