<?php

namespace App\Http\Controllers;

use App\Models\Actor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ActorController extends Controller
{
    public function index()
    {
        $actors = Actor::with('movies')->get();
        return $this->jsonResponse($actors);
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
            $actor = Actor::create($params);
            if (key_exists('movies',$params)){
                $actor->movies()->sync($params['movies']);
            }
            $actor->save();
            DB::commit();
            return $this->jsonResponse($actor);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->jsonResponse(['message' => $e->getMessage(), 400]);
        }
    }

    public function show($id)
    {
        $actor = Actor::with('movies')->findOrFail($id);
        return $this->jsonResponse($actor);
    }

    public function update($id, Request $request)
    {
        $actor = Actor::findOrFail($id);
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
            $actor->update($params);
            if (key_exists('movies',$params)){
                $actor->movies()->sync($params['movies']);
            }
            $actor->save();
            DB::commit();
            return $this->jsonResponse($actor);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->jsonResponse(['message' => $e->getMessage(), 400]);
        }
    }

    public function destroy($id)
    {
        $actor = Actor::findOrFail($id);
        return $this->jsonResponse($actor->delete());
    }

    protected function jsonResponse($data, $code = 200)
    {
        return response()->json($data, $code,
            ['Content-Type' => 'application/json;charset=UTF-8', 'Charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);
    }
}
