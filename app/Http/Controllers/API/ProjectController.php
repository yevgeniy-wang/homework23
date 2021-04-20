<?php


namespace App\Http\Controllers\API;


use App\Http\Resources\ProjectResource;
use App\Models\Project;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ProjectController
{
    public function store(Request $request)
    {
        $requests = $request->all();

        $validator = Validator::make($requests,
            ['*.name' => ['required', 'unique:projects,name', 'min:5']]
        )->validate();

        foreach ($requests as $data) {
            $user_id = Auth::user()->getAuthIdentifier();
            $data['user_id'] = $user_id;
            $project = Project::create($data);

            $project->linkedUsers()->attach($user_id);
        }

        return response(['status:' => 'ok']);
    }

    public function linkUsers(Request $request)
    {
        $requests = $request->all();

        foreach ($requests as $data) {
            $project = Project::find($data['project_id']);
            $project->linkedUsers()->attach($data['users']);
        }

        return response(['status:' => 'ok']);
    }

    public function list(Request $request)
    {
        $query = Project::query()->select('projects.*')
            ->join('project_user', 'projects.id', '=',
                'project_user.project_id')
            ->where('project_user.user_id', auth()->id());


        if ($request->has('email')) {
            $query->join('users', 'projects.user_id', '=', 'users.id')
                ->where('email', '=', $request->get('email'));

        }

        if ($request->has('labels')) {
            $query->join('label_project', 'projects.id', '=',
                'label_project.project_id')
                ->whereIn('label_id', $request->get('labels'));

        }

        if ($request->has('continent')) {
            $query->join('countries', 'users.country_id', '=', 'countries.id')
                ->join('continents',
                    'countries.continent_id', '=', 'continents.id')
                ->where('continents.code', '=', $request->get('continent'));

        }


        return ProjectResource::collection($query->distinct()->get());
    }

    public function destroy(Request $request)
    {
        $requests = $request->all();

        foreach ($requests as $data) {

            $project = Project::find($data);

            if ($request->user()->cannot('delete', $project)) {
                abort(403, "you can not delete this projects");
            }

            $project->delete();
        }

        return response(['status:' => 'ok']);
    }
}
