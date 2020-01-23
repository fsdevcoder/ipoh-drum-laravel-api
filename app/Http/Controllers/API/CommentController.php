<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use App\Comment;
use Illuminate\Support\Facades\Hash;
use App\Traits\GlobalFunctions;
use App\Traits\NotificationFunctions;
use App\Traits\CommentServices;
use App\Traits\ArticleServices;
use App\Traits\LogServices;

class CommentController extends Controller
{
    use GlobalFunctions, NotificationFunctions, CommentServices, LogServices, ArticleServices;

    private $controllerName = '[CommentController]';
    /**
     * @OA\Get(
     *      path="/api/comment",
     *      operationId="getComments",
     *      tags={"CommentControllerService"},
     *      summary="Get list of comments",
     *      description="Returns list of comments",
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
     *          description="Successfully retrieved list of comments"
     *       ),
     *       @OA\Response(
     *          response="default",
     *          description="Unable to retrieve list of comments")
     *    )
     */
    public function index(Request $request)
    {
        error_log($this->controllerName.'Retrieving list of comments.');
        // api/comment (GET)
        $comments = $this->getComments($request->user());
        if ($this->isEmpty($comments)) {
            return $this->errorPaginateResponse('Comments');
        } else {
            return $this->successPaginateResponse('Comments', $comments, $this->toInt($request->pageSize), $this->toInt($request->pageNumber));
        }
    }

    /**
     * @OA\Get(
     *      path="/api/filter/comment",
     *      operationId="filterComments",
     *      tags={"CommentControllerService"},
     *      summary="Filter list of comments",
     *      description="Returns list of filtered comments",
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
     *          description="Successfully retrieved list of filtered comments"
     *       ),
     *       @OA\Response(
     *          response="default",
     *          description="Unable to retrieve list of comments")
     *    )
     */
    public function filter(Request $request)
    {
        error_log($this->controllerName.'Retrieving list of filtered comments.');
        // api/comment/filter (GET)
        $params = collect([
            'keyword' => $request->keyword,
            'fromdate' => $request->fromdate,
            'todate' => $request->todate,
            'status' => $request->status,
            'company_id' => $request->company_id,
        ]);
        //Convert To Json Object
        $params = json_decode(json_encode($params));
        $comments = $this->filterComments($request->user(), $params);

        if ($this->isEmpty($comments)) {
            return $this->errorPaginateResponse('Comments');
        } else {
            return $this->successPaginateResponse('Comments', $comments, $this->toInt($request->pageSize), $this->toInt($request->pageNumber));
        }

    }
    /**
     * @OA\Get(
     *   tags={"CommentControllerService"},
     *   path="/api/comment/{uid}",
     *   summary="Retrieves comment by Uid.",
     *     operationId="getCommentByUid",
     *   @OA\Parameter(
     *     name="uid",
     *     in="path",
     *     description="Comment_ID, NOT 'ID'.",
     *     required=true,
     *     @OA\Schema(type="string")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Comment has been retrieved successfully."
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Unable to retrieve the comment."
     *   )
     * )
     */
    public function show(Request $request, $uid)
    {
        // api/comment/{commentid} (GET)
        error_log($this->controllerName.'Retrieving comment of uid:' . $uid);
        $comment = $this->getComment($uid);
        if ($this->isEmpty($comment)) {
            $data['data'] = null;
            return $this->notFoundResponse('Comment');
        } else {
            return $this->successResponse('Comment', $comment, 'retrieve');
        }
    }

    /**
     * @OA\Post(
     *   tags={"CommentControllerService"},
     *   path="/api/comment",
     *   summary="Creates a comment.",
     *   operationId="createComment",
     * @OA\Parameter(
     * name="name",
     * in="query",
     * description="Commentname",
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
     *     description="Comment has been created successfully."
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Unable to create the comment."
     *   )
     * )
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        // Can only be used by Authorized personnel
        // api/comment (POST)
        $this->validate($request, [
            'email' => 'nullable|string|email|max:191|unique:comments',
            'password' => 'required|string|min:6|confirmed',
        ]);
        error_log($this->controllerName.'Creating comment.');
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
        $comment = $this->createComment($request->user(), $params);

        if ($this->isEmpty($comment)) {
            DB::rollBack();
            return $this->errorResponse();
        } else {
            DB::commit();
            return $this->successResponse('Comment', $comment, 'create');
        }
    }

    /**
     * @OA\Put(
     *   tags={"CommentControllerService"},
     *   path="/api/comment/{uid}",
     *   summary="Update comment by Uid.",
     *     operationId="updateCommentByUid",
     *   @OA\Parameter(
     *     name="uid",
     *     in="path",
     *     description="Comment_ID, NOT 'ID'.",
     *     required=true,
     *     @OA\Schema(type="string")
     *   ),
     *   @OA\Parameter(
     *     name="name",
     *     in="query",
     *     description="Commentname.",
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
     *     description="Comment has been updated successfully."
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Unable to update the comment."
     *   )
     * )
     */
    public function update(Request $request, $uid)
    {
        DB::beginTransaction();
        // api/comment/{commentid} (PUT)
        error_log($this->controllerName.'Updating comment of uid: ' . $uid);
        $comment = $this->getComment($request->user(), $uid);
        $this->validate($request, [
            'email' => 'required|string|max:191|unique:comments,email,' . $comment->id,
            'name' => 'required|string|max:191',
        ]);
        if ($this->isEmpty($comment)) {
            DB::rollBack();
            return $this->notFoundResponse('Comment');
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
        $comment = $this->updateComment($request->user(), $comment, $params);
        if ($this->isEmpty($comment)) {
            DB::rollBack();
            return $this->errorResponse();
        } else {
            DB::commit();
            return $this->successResponse('Comment', $comment, 'update');
        }
    }

    /**
     * @OA\Delete(
     *   tags={"CommentControllerService"},
     *   path="/api/comment/{uid}",
     *   summary="Set comment's 'status' to 0.",
     *     operationId="deleteCommentByUid",
     *   @OA\Parameter(
     *     name="uid",
     *     in="path",
     *     description="Comment ID, NOT 'ID'.",
     *     required=true,
     *     @OA\SChema(type="string")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Comment has been 'deleted' successfully."
     *   ),
     *   @OA\Response(
     *     response="default",
     *     description="Unable to 'delete' the comment."
     *   )
     * )
     */
    public function destroy(Request $request, $uid)
    {
        DB::beginTransaction();
        // TODO ONLY TOGGLES THE status = 1/0
        // api/comment/{commentid} (DELETE)
        error_log($this->controllerName.'Deleting comment of uid: ' . $uid);
        $comment = $this->getComment($request->user(), $uid);
        if ($this->isEmpty($comment)) {
            DB::rollBack();
            return $this->notFoundResponse('Comment');
        }
        $comment = $this->deleteComment($comment);
        if ($this->isEmpty($comment)) {
            DB::rollBack();
            return $this->errorResponse();
        } else {
            DB::commit();
            return $this->successResponse('Comment', null, 'delete');
        }
    }

  
}
