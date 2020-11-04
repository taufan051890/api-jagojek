<?php

namespace App\Services\Jagomart;

use App\Traits\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class JagomartRequestService implements JagomartRequest {
    use JsonResponse;

    public function validateNewUser(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'owner_name' => 'required'
        ],$this->messages());

        if($validator->fails()){
            return $this->json422(implode(',', $validator->getMessageBag()->all()));
        }

        return true;

    }

    public function validateCategory(Request $request)
    {
        // TODO: Implement validateCategory() method.
    }

    public function validateNewItem(Request $request)
    {
        if($request->isMethod('POST')){
            $validator = Validator::make($request->all(),[
                'category_id' => 'required|exists:App\Models\Jagomart\Category,id',
                'name' => 'required|string|max:100',
                'price' => 'required|numeric',
                'description' => 'required|string|max:200',
                'stock' => 'required|numeric',
                'status' => 'required|boolean',
                'preview' => 'required|image'
            ],$this->messages());

            if($validator->fails()){
                return implode(',', $validator->getMessageBag()->all());
            }
        }

        return true;
    }

    public function validateTimeOperational(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'day_id' => 'required|integer|min:1|max:7',
            'is_24' => 'required|boolean',
            'is_close' => 'required|boolean',
            'is_custom_time' => 'required|boolean',
            'time_slot' => 'required|array',
            'time_slot.*.start' => 'required|date_format:H:i',
            'time_slot.*.end' => 'required|date_format:H:i'
        ],$this->messages());

        if($validator->fails()){
            return implode(',', $validator->getMessageBag()->all());
        }

        return true;
    }

    public function validatePin(Request $request,$new = false)
    {
        if($new){
            $validator = Validator::make($request->all(),[
                'new_pin' => 'required|numeric|digits:6|confirmed',
                'new_pin_confirmation' => 'required|numeric|digits:6'
            ],$this->messages());
        }else{
            $validator = Validator::make($request->all(),[
                'old_pin' => 'required|numeric|digits:6',
                'new_pin' => 'required|numeric|digits:6|confirmed',
                'new_pin_confirmation' => 'required|numeric|digits:6'
            ],$this->messages());
        }


        if($validator->fails()){
            return implode(',', $validator->getMessageBag()->all());
        }

        return true;
    }

    private function messages(){
        return [
            'required' => ':attribute is required.',
            'exists' => ':attribute not found.',
            'digits' => ':attribute must be contains 6 digits.'
        ];
    }
}
