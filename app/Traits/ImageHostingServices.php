<?php

namespace App\Traits;
use Carbon\Carbon;
use JD\Cloudder\Facades\Cloudder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Traits\GlobalFunctions;
use App\Traits\LogServices;

trait ImageHostingServices {

    use GlobalFunctions, LogServices;

    public function uploadImage($img , $folder)
    {
        if($img && $folder){
            try{
    
            $name = $img->getClientOriginalName();
    
            $realpath = $img->getRealPath();;
            error_log($realpath);
            Cloudder::upload($realpath, null, ['folder' => $folder, 'quality' => 100, 
            "width" => 500,"height"=>300, "crop"=>"pad"]);
            error_log('uploaded image');
    
            list($width, $height) = getimagesize($realpath);
    
            $imgurl= Cloudder::show(Cloudder::getPublicId(), ["width" => $width, "height"=>$height]);
            //save to uploads directory
            $data['imgurl'] = $imgurl;
            $data['publicid'] = Cloudder::getPublicId();
            $data['name'] = $name;
            return (object) $data;

            }catch(Exception $e){
                $this->createErrorLog('ImageHostingServices' , 'deleteImages', 'error when uploading image ' , $e->getMessage());
                return null;
            }
        }

    }

    public function deleteImages($ids)
    {
            foreach($ids as $id){
                $this->deleteImage($id);
            }
    } 

    
    public function deleteImage($id)
    {
        
        if($id){
            try{
                Cloudder::destroy($id);
                error_log('deleted image');
                return true;
            }catch(Exxception $e){
                $this->createErrorLog('ImageHostingServices' , 'deleteImage', 'error when deleting image '. $id , $e->getMessage());
                return false;
            }
        }
    } 


}
