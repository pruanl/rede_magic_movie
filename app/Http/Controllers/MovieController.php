<?php

namespace App\Http\Controllers;

use App\Models\Classification;
use App\Models\Movie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class MovieController extends Controller
{
    public function index()
    {
        $movies = Movie::with('classification')
            ->with('directors')
            ->with('actors')
            ->get();
        return $this->jsonResponse($movies);
    }

    public function store(Request $request)
    {
        $params = $request->all();
        $validator = Validator::make($params,[
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'year' => 'required|numeric',
            'duration' => 'required|numeric',
            'classification_id' => 'required|numeric',
            'directors' => 'array',
            'directors.*' => 'exists:directors,id',
            'actors' => 'array',
            'actors.*' => 'exists:actors,id',
        ]);
        if ($validator->fails()){
            return $this->jsonResponse($validator->errors(),400);
        }
        DB::beginTransaction();
        try{
            $classification = Classification::findOrFail($params['classification_id']);
            $movie = Movie::create($params);
            $movie->classification()->associate($classification);
            if (key_exists('directors',$params)){
                $movie->directors()->sync($params['directors']);
            }
            if (key_exists('actors',$params)){
                $movie->actors()->sync($params['actors']);
            }
            $movie->save();
            DB::commit();
            return $this->jsonResponse($movie);
        }catch (\Exception $e){
            DB::rollBack();
            return $this->jsonResponse(['message' => $e->getMessage(),400]);
        }
    }

    public function show($id)
    {
        $movie = Movie::with('classification')->with('actors')->with('directors')->findOrFail($id);
        return $this->jsonResponse($movie);
    }

    public function update($id, Request $request)
    {
        $movie = Movie::findOrFail($id);
        $params = $request->all();
        $validator = Validator::make($params,[
            'name' => 'string|max:255',
            'description' => 'string',
            'year' => 'numeric',
            'duration' => 'numeric',
            'classification_id' => 'numeric|exists:classifications,id',
            'directors' => 'array',
            'directors.*' => 'exists:directors,id',
            'actors' => 'array',
            'actors.*' => 'exists:actors,id',
        ]);

        if ($validator->fails()){
            return $this->jsonResponse($validator->errors(),400);
        }
        DB::beginTransaction();
        try{
            $movie->update($params);
            if (key_exists('classification_id',$params)){
                $classification = Classification::findOrFail($params['classification_id']);
                $movie->classification()->associate($classification);
            }
            if (key_exists('directors',$params)){
                $movie->directors()->sync($params['directors']);
            }
            if (key_exists('actors',$params)){
                $movie->actors()->sync($params['actors']);
            }
            $movie->save();
            DB::commit();
            return $this->jsonResponse($movie);
        }catch (\Exception $e){
            DB::rollBack();
            return $this->jsonResponse(['message' => $e->getMessage(),400]);
        }
    }

    public function destroy($id){
        $movie = Movie::findOrFail($id);
        return $this->jsonResponse( $movie->delete());
    }

    protected function jsonResponse($data, $code = 200)
    {
        return response()->json($data, $code,
            ['Content-Type' => 'application/json;charset=UTF-8', 'Charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);
    }
}
