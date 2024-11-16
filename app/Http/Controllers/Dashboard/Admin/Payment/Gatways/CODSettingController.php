<?php

namespace App\Http\Controllers\Dashboard\Admin\Payment\Gatways;

use App\Http\Controllers\Controller;
use App\Http\Requests\CODSettingRequest;
use App\Models\CODSetting;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CODSettingController extends Controller
{
    public function UpdateCODSettings(CODSettingRequest $request){
    
        try{   
            $codSettings = CODSetting::updateOrCreate(
                ['id'=> 1],
                [
                    'status' => $request->status,
                ]
            );

            return $this->success( $codSettings,'Updated Successfully!',SUCCESS_CODE,'CODSetting');
            
        }catch (ValidationException $ex) {  
            return $this->error($ex->getMessage(), VALIDATION_ERROR_CODE);
        }catch(\Exception $ex){  
            return $this->error($ex->getMessage(),ERROR_CODE);
        }

    }
}
