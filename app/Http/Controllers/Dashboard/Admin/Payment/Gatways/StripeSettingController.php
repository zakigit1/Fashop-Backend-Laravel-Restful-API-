<?php

namespace App\Http\Controllers\Dashboard\Admin\Payment\Gatways;

use App\Http\Controllers\Controller;
use App\Http\Requests\StripeSettingRequest;
use App\Models\StripeSetting;
use Illuminate\Http\Request;

class StripeSettingController extends Controller
{
    public function updateStripeSettings(StripeSettingRequest $request){

        // dd($request->all());
        $request->validate([



        ]);
        // dd($request->all());

        try{   
            $paypalSettings = StripeSetting::updateOrCreate(
                ['id'=> 1],
                [
                    'status'=>$request->status,
                    'mode'=>$request->mode,
                    'country_name'=>$request->country_name,
                    'currency_name'=>$request->currency_name,
                    'currency_rate'=>$request->currency_rate,
                    'client_id'=>$request->client_id,
                    'secret_key'=>$request->secret_key
                ]
            );

            // toastr('Stripe Settings Has Been Updated Successfully !','success','Success');
            return redirect()->back();

        }catch(\Exception $ex){

            // toastr($ex->getMessage(),'error');
            // toastr('Paypal Settings Has Not Been Updated Successfully !','error','Error');
            return redirect()->back();
        }
    }}
