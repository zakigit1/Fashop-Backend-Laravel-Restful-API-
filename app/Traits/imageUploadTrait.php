<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

trait imageUploadTrait{


    function uploadImage_Trait(Request $request ,$inputName, $folderPath, $folderName){

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



    function deleteImage_Trait($old_image,$folderPath,$folderName):void
    {

        $imgDir = '/storage'.$folderPath.$folderName.'/';

        // dd(public_path($imgDir.$old_image));
        
        if(file_exists(public_path($imgDir.$old_image))){
            // dd(public_path($imgDir.$old_image));
            File::delete(public_path($imgDir.$old_image));   
        }
    }


    function updateImage_Trait(Request $request ,$inputName, $folderPath, $folderName, $old_image = null){

        
        #-------- begin of Delete the old image ---------#
        
            // if(file_exists(public_path($old_image))){
            //     File::delete(public_path($old_image));   
            // }
            // ? instead of the three ligne we use direct the function 
            $this->deleteImage_Trait($old_image,$folderPath,$folderName);
                
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
    







    /** Using in slider controller */
    function uploadImage_Trait2(Request $request ,$inputName, $folderPath, $folderName){

        $folder= $folderPath.$folderName;
        
        // Get the original file name and extension
        $originalName = $request->file($inputName)->getClientOriginalName();

        // dd($originalName);

        // Define the path where the file should be stored
        $destinationPath = public_path('storage/'.$folder);

        // Move the file to the destination path with the original name
        $request->file($inputName)->move($destinationPath, $originalName);

        // Return the stored file path
        // return $folder . '/' . $originalName;

        // Return the stored file name
        return $originalName;
    }


    function updateImage_Trait2(Request $request ,$inputName, $folderPath, $folderName, $old_image = null){

        $this->deleteImage_Trait($old_image,$folderPath,$folderName);
            
        $imageName = $this->uploadImage_Trait2($request ,$inputName, $folderPath, $folderName);
        return $imageName;
    }

    
}
