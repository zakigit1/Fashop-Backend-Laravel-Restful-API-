<?php

namespace App\Http\Controllers\Dashboard\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\EmailSettingRequest;
use App\Http\Requests\GeneralSettingRequest;
use App\Models\EmailSetting;
use App\Models\GeneralSetting;

use App\Traits\imageUploadTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class SettingController extends Controller
{

    use imageUploadTrait;

    private const FOLDER_PATH = '/uploads/images/';
    private const FOLDER_NAME = 'logoAndfavicon';


    public function getGeneralSetting(): JsonResponse   
    {
        $generalSettings = GeneralSetting::first();
        return $this->success($generalSettings ?? 'You need to add general settings' , 'Get General Settings',SUCCESS_CODE,'generalSettings');
    }
    public function getEmailSetting()   
    {
        // return Config::get('mail');
        $emailSettings = EmailSetting::first();
        return $this->success($emailSettings ?? 'You need to add email configuration settings' , 'Get Email Settings',SUCCESS_CODE,'emailSettings');
    }


    // public function getLogoSetting(): JsonResponse   
    // {
    //     $logoSettings = LogoSetting::first();
    //     return $this->success($logoSettings ?? 'You need to add logo settings' , 'Get Logo Settings',SUCCESS_CODE,'logoSettings');
    // }


    /**  General Setting */
    public function UpdateSettingsGeneral(GeneralSettingRequest $request): JsonResponse
    {
        try{   
            // return $request->all();
            $checkGeneralSetting = GeneralSetting::first();     



            $oldLogo = $checkGeneralSetting?->logo;            
            $imageUpdatedLogo= $oldLogo ;

            $oldFavicon = $checkGeneralSetting?->favicon;
            $imageUpdatedFavicon= $oldFavicon ;
            
            

            if($request->hasFile('logo') || $request->hasFile('favicon')){

                if($request->hasFile('logo')){
                    $imageUpdatedLogo = $this->updateImage_Trait($request,'logo',self::FOLDER_PATH,self::FOLDER_NAME,$imageUpdatedLogo);
                }

                if($request->hasFile('favicon')){
                    $imageUpdatedFavicon = $this->updateImage_Trait($request,'favicon',self::FOLDER_PATH,self::FOLDER_NAME,$imageUpdatedFavicon);
                }
            }



            $generalSettings = GeneralSetting::updateOrCreate(
                ['id'=> 1],
                [
                    'site_name'=>$request->site_name,
                    'layout'=>$request->layout,
                    'contact_email'=>$request->contact_email,

                    'contact_phone'=>$request->contact_phone,
                    'contact_address'=>$request->contact_address,
                    'contact_map'=>$request->contact_map,

                    'currency_name'=>$request->currency_name,
                    'currency_icon'=>$request->currency_icon,
                    'time_zone'=>$request->time_zone,

                    'logo'=>$imageUpdatedLogo ?? null,
                    'favicon'=>$imageUpdatedFavicon ?? null,
                ]
            );


            if($checkGeneralSetting){
                return $this->success($generalSettings,'General Settings Has Been Updated Successfully !',SUCCESS_CODE,'generalSettings');
            }else{
                return $this->success($generalSettings,'General Settings Has Been Created Successfully !',SUCCESS_STORE_CODE,'generalSettings');
            }

        }catch(\Exception $ex){
            return $this->error($ex->getMessage(),ERROR_CODE);
            // return $this->error('General Settings Has Not Been Updated Successfully !' ,ERROR_CODE);
        }

    }

    /** Email Configuration */
    public function UpdateEmailConfiguration(EmailSettingRequest $request): JsonResponse
    {
        // return $request->all();
        try{   

            $checkEmailSetting = EmailSetting::count();

            $emailSettings = EmailSetting::updateOrCreate(
                ['id'=> 1],
                [
                    'name'=>$request->name,
                    'email'=>$request->email,
                    'host'=>$request->host,
                    'username'=>$request->username,
                    'password'=>$request->password,
                    'port'=> $request->port,
                    'encryption'=>$request->encryption,
                ]
            );
 
            if($checkEmailSetting > 0){
                return $this->success($emailSettings,'Email Configuration Has Been Updated Successfully !',SUCCESS_CODE,'emailSettings');
            }else{
                return $this->success($emailSettings,'Email Configuration Has Been Created Successfully !',SUCCESS_STORE_CODE,'emailSettings');
            }
                
        }catch(\Exception $ex){
            return $this->error($ex->getMessage(),ERROR_CODE);
            // return $this->error('Email Settings Has Not Been Updated Successfully !' ,ERROR_CODE);
        }

    }


}