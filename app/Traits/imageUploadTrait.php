<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

trait imageUploadTrait{


    function uploadImage_Trait(Request $request ,$inputName, $folderPath, $folderName){

        // $Folder_name='sliders';

        $folder= $folderPath.$folderName;
        
        // store the image in storage folder (Storage/public/path..)

        $imageStore =  $request->$inputName->store($folder);

        //name of the image : 
        $imageName = basename($imageStore);
        
        // $base_url = env('BASE_URL_API','http://127.0.0.1:8000');
        // dd($base_url);

        // return $base_url.$imageName;
        return $imageName;
    }

    function deleteImage_Trait($old_image):void
    {
        // dd(public_path($old_image));
        if(file_exists(public_path($old_image))){
            File::delete(public_path($old_image));   
        }
    }


    function updateImage_Trait(Request $request ,$inputName, $folderPath, $folderName, $old_image = null){

        
        #-------- begin of Delete the old image ---------#
        
            // if(file_exists(public_path($old_image))){
            //     File::delete(public_path($old_image));   
            // }
            // ? instead of the three ligne we use direct the function 
            $this->deleteImage_Trait($old_image);
                
        #-------- end of Delete the old image ---------#

        #-------- begin of Upload the new image ---------#


            // $folder= $folderPath.$folderName;
            
            ## store the image in storage folder (Storage/public/path..)

            // $imageStore =  $request->$inputName->store($folder);

            ## name of the image : 
            // $imageName = basename($imageStore);
            
            // return $imageName;

            // ? instead of the four ligne we use direct the function 
            $imageName = $this->uploadImage_Trait($request ,$inputName, $folderPath, $folderName);
            return $imageName;

        #-------- end of Upload the new image ---------#
    }
    

    function upload_Multi_Image_Trait(Request $request ,$inputName, $folderPath, $folderName){

        // $Folder_name='sliders';

        $folder= $folderPath.$folderName;
        
    
        $images =$request->$inputName ;//is array of images
        $imagesNames=[];

        foreach($images as $image){

            // store the images in storage folder (Storage/public/path..)
            $imageStore =  $image->store($folder);

            //names of the images : 
            $imagesNames[] = basename($imageStore);
        }

        
        return $imagesNames;
    }
    
    
}
