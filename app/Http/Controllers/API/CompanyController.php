<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use App\Company;
use Illuminate\Support\Facades\Hash;
use App\Traits\GlobalFunctions;
use App\Traits\NotificationFunctions;
use App\Traits\CompanyServices;
use App\Traits\LogServices;

class CompanyController extends Controller
{
    use GlobalFunctions, NotificationFunctions, CompanyServices, LogServices;
    private $controllerName = '[CompanyController]';
     /**
     * @OA\Get(
     *      path="/api/company",
     *      operationId="getCompanies",
     *      tags={"CompanyControllerService"},
     *      summary="Get list of companies",
     *      description="Returns list of companies",
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
     *          description="Successfully retrieved list of companies"
     *       ),
     *       @OA\Response(
     *          response="default",
     *          description="Unable to retrieve list of companies")
     *    )
     */
    public function index(Request $request)
    {
        error_log('Retrieving list of companies.');
        // api/company (GET)
        $companies = $this->getCompanies($request->user());
        
        if ($this->isEmpty($companies)) {
            return $this->errorPaginateResponse('Companies');
        } else {
            return $this->successPaginateResponse('Companies', $companies, $this->toInt($request->pageSize), $this->toInt($request->pageNumber));
        }
    }
    
    /**
     * @OA\Get(
     *      path="/api/filter/company",
     *      operationId="filterCompanies",
     *      tags={"CompanyControllerService"},
     *      summary="Filter list of companies",
     *      description="Returns list of filtered companies",
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
     *          description="Successfully retrieved list of filtered companies"
     *       ),
     *       @OA\Response(
     *          response="default",
     *          description="Unable to retrieve list of companies")
     *    )
     */
    public function filter(Request $request)
    {
        error_log('Retrieving list of filtered companies.');
        // api/company/filter (GET)
        $params = collect([
            'keyword' => $request->keyword,
            'fromdate' => $request->fromdate,
            'todate' => $request->todate,
            'status' => $request->status,
            'company_id' => $request->company_id,
        ]);
        //Convert To Json Object
        $params = json_decode(json_encode($params));
        $companies = $this->getCompanies($request->user());
        $companies = $this->filterCompanies($companies, $params);

       
        if ($this->isEmpty($companies)) {
            return $this->errorPaginateResponse('Companies');
        } else {
            return $this->successPaginateResponse('Companies', $companies, $this->toInt($request->pageSize), $this->toInt($request->pageNumber));
        }

    }


    /**
     * @OA\Get(
     *   tags={"CompanyControllerService"},
     *   path="/api/company/{uid}",
     *   summary="Retrieves company by Uid.",
     *     operationId="getCompanyByUid",
     *   @OA\Parameter(
     *     name="uid",
     *     in="path",
     *     description="Company_ID, NOT 'ID'.",
     *     required=true,
     *     @OA\Schema(type="string")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Company has been retrieved successfully."
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Unable to retrieve the company."
     *   )
     * )
     */
    public function show(Request $request, $uid)
    {
        // api/company/{companyid} (GET)
        error_log('Retrieving company of uid:' . $uid);
        $company = $this->getCompany($uid);
        if ($this->isEmpty($company)) {
            return $this->notFoundResponse('Company');
        } else {
            return $this->successResponse('Company', $company, 'retrieve');
        }
    }
     
    /**
     * @OA\Post(
     *   tags={"CompanyControllerService"},
     *   path="/api/company",
     *   summary="Creates a company.",
     *   operationId="createCompany",
     * @OA\Parameter(
     * name="name",
     * in="query",
     * description="Companyname",
     * required=true,
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="company_type_id",
     * in="query",
     * description="Company Type ID",
     * required=true,
     * @OA\Schema(
     *              type="integer"
     *          )
     * ),
     * @OA\Parameter(
     * name="email1",
     * in="query",
     * description="Email 1",
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="email2",
     * in="query",
     * description="Email 2",
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="regno",
     * in="query",
     * description="Registration No",
     * required=true,
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="tel1",
     * in="query",
     * description="Contact No 1",
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="tel2",
     * in="query",
     * description="Contact No 2",
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="fax1",
     * in="query",
     * description="Fax",
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="fax2",
     * in="query",
     * description="Fax 2",
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="address1",
     * in="query",
     * description="Address",
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="address2",
     * in="query",
     * description="Address 2",
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="postcode",
     * in="query",
     * description="Post Code",
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="state",
     * in="query",
     * description="State",
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="city",
     * in="query",
     * description="City",
     * @OA\Schema(
     *  type="string"
     *  )
     * ),
     * @OA\Parameter(
     * name="Country",
     * in="query",
     * description="Country",
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     *   @OA\Response(
     *     response=200,
     *     description="Company has been created successfully."
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Unable to create the company."
     *   )
     * )
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        // Can only be used by Authorized personnel
        // api/company (POST)
        $this->validate($request, [
            'email1' => 'nullable|email|max:191|unique:companies',
            'email2' => 'nullable|email|max:191|unique:companies',
            'fax1' => 'nullable|string|max:191|unique:companies',
            'fax2' => 'nullable|string|max:191|unique:companies',
            'tel1' => 'nullable|string|max:191|unique:companies',
            'tel2' => 'nullable|string|max:191|unique:companies',
            'company_type_id' => 'required|string',
            'name' => 'required|string',
        ]);
        error_log('Creating company.');
        $params = collect([
            'regno' => $request->regno,
            'name' => $request->name,
            'email1' => $request->email1,
            'email2' => $request->email2,
            'tel1' => $request->tel1,
            'tel2' => $request->tel2,
            'fax1' => $request->fax1,
            'fax2' => $request->fax2,
            'address1' => $request->address1,
            'address2' => $request->address2,
            'postcode' => $request->postcode,
            'state' => $request->state,
            'city' => $request->city,
            'country' => $request->country,
            'company_type_id' => $request->company_type_id,
        ]);
        //Convert To Json Object
        $params = json_decode(json_encode($params));
        $company = $this->createCompany($params);

        if ($this->isEmpty($company)) {
            DB::rollBack();
            return $this->errorResponse();
        } else {
            DB::commit();
            return $this->successResponse('Company', $company, 'create');
        }
    }


    /**
     * @OA\Put(
     *   tags={"CompanyControllerService"},
     *   path="/api/company/{uid}",
     *   summary="Update company by Uid.",
     *     operationId="updateCompanyByUid",
     *   @OA\Parameter(
     *     name="uid",
     *     in="path",
     *     description="Company_ID, NOT 'ID'.",
     *     required=true,
     *     @OA\Schema(type="string")
     *   ),
     * @OA\Parameter(
     * name="name",
     * in="query",
     * description="Companyname",
     * required=true,
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="company_type_id",
     * in="query",
     * description="Company Type ID",
     * required=true,
     * @OA\Schema(
     *              type="integer"
     *          )
     * ),
     * @OA\Parameter(
     * name="email1",
     * in="query",
     * description="Email 1",
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="email2",
     * in="query",
     * description="Email 2",
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="regno",
     * in="query",
     * description="Registration No",
     * required=true,
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="tel1",
     * in="query",
     * description="Contact No 1",
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="tel2",
     * in="query",
     * description="Contact No 2",
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="fax1",
     * in="query",
     * description="Fax",
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="fax2",
     * in="query",
     * description="Fax 2",
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="address1",
     * in="query",
     * description="Address",
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="address2",
     * in="query",
     * description="Address 2",
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="postcode",
     * in="query",
     * description="Post Code",
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="state",
     * in="query",
     * description="State",
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     * @OA\Parameter(
     * name="city",
     * in="query",
     * description="City",
     * @OA\Schema(
     *  type="string"
     *  )
     * ),
     * @OA\Parameter(
     * name="Country",
     * in="query",
     * description="Country",
     * @OA\Schema(
     *              type="string"
     *          )
     * ),
     *   @OA\Response(
     *     response=200,
     *     description="Company has been updated successfully."
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Unable to update the company."
     *   )
     * )
     */
    public function update(Request $request, $uid)
    {
        DB::beginTransaction();
        // api/company/{companyid} (PUT) 
        error_log('Updating company of uid: ' . $uid);
        $company = $this->getCompany($uid);
        error_log($company);
        $this->validate($request, [
            'email1' => 'required|email|max:191|unique:companies,email1,' . $company->id.'|unique:companies,email2,' . $company->id,
            'email2' => 'required|email|max:191|unique:companies,email1,' . $company->id.'|unique:companies,email2,' . $company->id,
            'fax1' => 'required|string|max:191|unique:companies,fax1,' . $company->id.'|unique:companies,fax2,' . $company->id,
            'fax2' => 'required|string|max:191|unique:companies,fax1,' . $company->id.'|unique:companies,fax2,' . $company->id,
            'tel1' => 'required|string|max:191|unique:companies,tel1,' . $company->id.'|unique:companies,tel2,' . $company->id,
            'tel2' => 'required|string|max:191|unique:companies,tel1,' . $company->id.'|unique:companies,tel2,' . $company->id,
            'name' => 'required|string|max:191',
            'companytypeid' => 'required|string',
        ]);
        
        if ($this->isEmpty($company)) {
            DB::rollBack();
            return $this->notFoundResponse('Company');
        }
        $params = collect([
            'regno' => $request->regno,
            'name' => $request->name,
            'email1' => $request->email1,
            'email2' => $request->email2,
            'tel1' => $request->tel1,
            'tel2' => $request->tel2,
            'fax1' => $request->fax1,
            'fax2' => $request->fax2,
            'address1' => $request->address1,
            'address2' => $request->address2,
            'postcode' => $request->postcode,
            'state' => $request->state,
            'city' => $request->city,
            'country' => $request->country,
            'company_type_id' => $request->company_type_id,
        ]);
        //Convert To Json Object
        $params = json_decode(json_encode($params));
        $company = $this->updateCompany($company, $params);
        if ($this->isEmpty($company)) {
            DB::rollBack();
            return $this->errorResponse();
        } else {
            DB::commit();
            return $this->successResponse('Company', $company, 'update');
        }
    }


    /**
     * @OA\Delete(
     *   tags={"CompanyControllerService"},
     *   path="/api/company/{uid}",
     *   summary="Set company's 'status' to 0.",
     *     operationId="deleteCompanyByUid",
     *   @OA\Parameter(
     *     name="uid",
     *     in="path",
     *     description="Company ID, NOT 'ID'.",
     *     required=true,
     *     @OA\SChema(type="string")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Company has been 'deleted' successfully."
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Unable to 'delete' the company."
     *   )
     * )
     */
    public function destroy(Request $request, $uid)
    {
        DB::beginTransaction();
        // TODO ONLY TOGGLES THE status = 1/0
        // api/company/{companyid} (DELETE)
        error_log('Deleting company of uid: ' . $uid);
        $company = $this->getCompany($uid);
        if ($this->isEmpty($company)) {
            DB::rollBack();
            return $this->notFoundResponse('Company');
        }
        $company = $this->deleteCompany($company);
        $this->createLog($request->user()->id , [$company->id], 'delete', 'company');
        if ($this->isEmpty($company)) {
            DB::rollBack();
            return $this->errorResponse();
        } else {
            DB::commit();
            return $this->successResponse('Company', $company, 'delete');
        }
    }


}
