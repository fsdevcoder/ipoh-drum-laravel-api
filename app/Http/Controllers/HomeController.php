<?php

namespace App\Http\Controllers;
use JD\Cloudder\Facades\Cloudder;
use App\InventoryImage;
use App\Inventory;
use App\Traits\VideoHostingServices;
use Illuminate\Http\Request;
use App\Traits\InventoryServices;
use App\Traits\InventoryImageServices;
use App\Traits\GlobalFunctions;
use App\Traits\NotificationFunctions;
use DB;

class HomeController extends Controller
{
    use VideoHostingServices,InventoryServices,GlobalFunctions, NotificationFunctions ;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $images = InventoryImage::all();
        return view('home', compact('images'));
    }

    public function home()
    {
        // add this
    }


    public function uploadImages(Request $request)
    {
        $proccessingimgids = collect();
        DB::beginTransaction();
        $count = 1;
        $inventory = $this->getInventoryById(10);
        error_log($request->name);
        if($request->file('sliders') != null){
            error_log('Slider Images Is Detected');
            error_log(collect($request->sliders));
            $sliders = $request->file('sliders');
            error_log(collect($sliders));
            foreach($sliders as $slider){
                error_log('Inside slider');
                $count++;
                if($count > 6){
                    break;
                }
                $img = $this->uploadImage($slider , "/Inventory/". $inventory->uid . "/sliders");
                error_log(collect($img));
                if(!$this->isEmpty($img)){
                    $proccessingimgids->push($img->publicid);
                    if(!$this->saveModel($inventory)){
                        error_log('error here2');
                        DB::rollBack();
                        $this->deleteImages($proccessingimgids);
                        return $this->errorResponse();
                    }
                    //Attach Image to InventoryImage
                    $inventoryimage = $this->associateImageWithInventory($inventory , $img);
                    if($this->isEmpty($inventoryimage)){
                        error_log('error here1');
                        DB::rollBack();
                        $this->deleteImages($proccessingimgids);
                        return $this->errorResponse();
                    }
                }else{
                    error_log('error here3');
                    DB::rollBack();
                    $this->deleteImages($proccessingimgids);
                    return $this->errorResponse();
                }
            }
        }
        
        DB::commit();

        return $this->successResponse('Inventory', $inventory, 'create');

    }

    public function saveImages(Request $request, $image_url)
    {
        $image = new InventoryImage();
        $image->uid = InventoryImage::count()+1;
        $image->name = $request->file('sliders')->getClientOriginalName();
        $image->imgpath = $image_url;
        $image->inventory()->associate(Inventory::find(1));
        $image->save();
    }

    
    public function uploadVideo(Request $request)
    {
        
        
        $video = $request->file('sliders');

        $this->uploadVideos($video , "/Video");
        return redirect()->back()->with('status', 'Image Uploaded Successfully');
    }
}
