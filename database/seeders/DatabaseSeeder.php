<?php

namespace Database\Seeders;

use App\Models\Continent;
use App\Models\Country;
use App\Models\Label;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $continentsRaw = collect(Http::get('http://country.io/continent.json')
            ->json());

        $data = [];

        foreach ($continentsRaw->unique() as $code) {
            $data[] = [
                'code' => $code,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        Continent::insert($data);

        $continents = Continent::all()->pluck('id', 'code');

        $countriesRaw = Http::get('country.io/names.json')->json();

        $data = [];

        foreach ($countriesRaw as $code => $name) {
            $data[] = [
                'continent_id' => $continents[$continentsRaw[$code]],
                'code'         => $code,
                'country_name'         => $name,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        Country::insert($data);

        $countries = Country::all()->pluck('id');

        $users = User::factory(10)->create([
            'country_id' => $countries->random(),
        ]);

        $users->each(function ($user) use ($countries) {
            $project = Project::factory(rand(5, 10))->create([
                'user_id' => $user->id,
            ]);
            $label = Label::factory(rand(5, 10))->create([
                'user_id' => $user->id,
            ]);
            $project->each(fn($project) => $project->linkedUsers()
                ->syncWithoutDetaching($user->id));
            $user->country_id = $countries->random();
        });

        $projects = Project::all();

        $users->each(fn($user) => $user->linkedProjects()
            ->syncWithoutDetaching($projects->random(rand(5, 10))
                ->pluck('id')));

        $labels = Label::all();

        $projects->each(fn($project) => $project->labels()
            ->attach($labels->random(rand(5, 10))->pluck('id')));
    }
}
