<?php

namespace App\Http\Controllers;

use App\Models\Classification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;


class ClassificationController extends Controller
{

    public function index()
    {
        $classifications = Classification::with('movies')->get();
        return $this->jsonResponse($classifications);
    }

    public function store(Request $request)
    {

        $params = $request->all();
        $validator = Validator::make($params,[
            'name' => 'required|string|max:255',
            'age' => 'required|numeric'
        ]);

        if ($validator->fails()){
            return $this->jsonResponse($validator->errors(),400);
        }
        DB::beginTransaction();
        try{
            $classification = Classification::create($params);
            DB::commit();
            return $this->jsonResponse($classification);
        }catch (\Exception $e){
            DB::rollBack();
            return $this->jsonResponse(['message' => $e->getMessage(),400]);
        }
    }

    public function show($id)
    {
        $classification = Classification::findOrFail($id);
        return $this->jsonResponse($classification);
    }

    public function update($id, Request $request)
    {
        $classification = Classification::findOrFail($id);
        $params = $request->all();
        $validator = Validator::make($params,[
            'name' => 'string|max:255',
            'age' => 'numeric'
        ]);

        if ($validator->fails()){
            return $this->jsonResponse($validator->errors(),400);
        }
        DB::beginTransaction();
        try{
            $classification->update($params);
            DB::commit();
            return $this->jsonResponse($classification);
        }catch (\Exception $e){
            DB::rollBack();
            return $this->jsonResponse(['message' => $e->getMessage(),400]);
        }
    }

    public function destroy($id){
        $classification = Classification::findOrFail($id);
        return $this->jsonResponse( $classification->delete());
    }

    protected function jsonResponse($data, $code = 200)
    {
        return response()->json($data, $code,
            ['Content-Type' => 'application/json;charset=UTF-8', 'Charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);
    }
}
