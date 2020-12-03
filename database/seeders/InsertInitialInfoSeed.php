<?php

namespace Database\Seeders;

use App\Models\Actor;
use App\Models\Classification;
use App\Models\Director;
use App\Models\Movie;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InsertInitialInfoSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::beginTransaction();
        try {
            $classification = Classification::create([
                'name' => '16 Anos',
                'age' => 16
            ]);

            $director = Director::create([
                'name' => 'Peter Jackson',
                'age' => 59,
                'sex' => 'M',
            ]);
            $actorOne = Actor::create([
                'name' => 'Ian McKellen',
                'age' => 81,
                'sex' => 'M',
            ]);

            $actorTwo = Actor::create([
                'name' => 'Liv Tyler',
                'age' => 43,
                'sex' => 'F',
            ]);

            $movie = Movie::create([
                'name' => 'O Senhor dos Anéis: A Sociedade do Anel',
                'description' => 'Em uma terra fantástica e única, um hobbit recebe de presente de seu tio um anel mágico e maligno que precisa ser destruído antes que caia nas mãos do mal. Para isso, o hobbit Frodo tem um caminho árduo pela frente, onde encontra perigo, medo e seres bizarros. Ao seu lado para o cumprimento desta jornada, ele aos poucos pode contar com outros hobbits, um elfo, um anão, dois humanos e um mago, totalizando nove seres que formam a Sociedade do Anel.',
                'year' => 2001,
                'duration' => 220,
            ]);

            $movie->classification()->associate($classification);
            $movie->directors()->sync([$director->id]);
            $movie->actors()->sync([$actorOne->id, $actorTwo->id]);
            $movie->save();
            DB::commit();
        } catch (\Exception $e) {
            Log::info($e);
            DB::rollBack();
        }
    }
}
