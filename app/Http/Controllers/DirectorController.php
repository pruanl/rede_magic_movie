<?php

namespace App\Http\Controllers;

use App\Models\Director;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use PhpParser\Node\Scalar\MagicConst\Dir;

class DirectorController extends Controller
{
    public function index()
    {
        $directors = Director::all();
        return $this->jsonResponse($directors);
    }

    public function store(Request $request)
    {

        $params = $request->all();
        $validator = Validator::make($params, [
            'name' => 'required|string|max:255',
            'age' => 'required|numeric',
            'sex' => 'required|string|max:1',
            'movies' => 'array',
            'movies.*' => 'exists:movies,id',
        ]);

        if ($validator->fails()) {
            return $this->jsonResponse($validator->errors(), 400);
        }
        DB::beginTransaction();
        try {
            $director = Director::create($params);
            if (key_exists('movies',$params)){
                $director->movies()->sync($params['movies']);
            }
            $director->save();
            DB::commit();
            return $this->jsonResponse($director);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->jsonResponse(['message' => $e->getMessage(), 400]);
        }
    }

    public function show($id)
    {
        $director = Director::with('movies')->findOrFail($id);

        return $this->jsonResponse($director);
    }

    public function update($id, Request $request)
    {
        $director = Director::findOrFail($id);
        $params = $request->all();
        $validator = Validator::make($params, [
            'name' => 'string|max:255',
            'age' => 'numeric',
            'sex' => 'string|max:1',
            'movies' => 'array',
            'movies.*' => 'exists:movies,id',
        ]);

        if ($validator->fails()) {
            return $this->jsonResponse($validator->errors(), 400);
        }
        DB::beginTransaction();
        try {
            $director->update($params);
            if (key_exists('movies',$params)){
                $director->movies()->sync($params['movies']);
            }
            $director->save();
            DB::commit();
            return $this->jsonResponse($director);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->jsonResponse(['message' => $e->getMessage(), 400]);
        }
    }

    public function destroy($id)
    {
        $director = Director::findOrFail($id);
        return $this->jsonResponse($director->delete());
    }

    protected function jsonResponse($data, $code = 200)
    {
        return response()->json($data, $code,
            ['Content-Type' => 'application/json;charset=UTF-8', 'Charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);
    }
}
