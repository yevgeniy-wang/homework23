<?php


namespace App\Http\Controllers\API;


use App\Http\Resources\LabelResource;
use App\Models\Label;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LabelController
{
    public function store(Request $request)
    {
        $requests = $request->all();

        $validator = Validator::make($requests,
            ['*.name' => ['required', 'unique:labels,name', 'min:5']]
        )->validate();

        foreach ($requests as $data) {
            $user_id = Auth::user()->getAuthIdentifier();
            $data['user_id'] = $user_id;
            $label = Label::create($data);
        }

        return response(['status:' => 'ok']);
    }

    public function linkUsers(Request $request)
    {
        $requests = $request->all();

        foreach ($requests as $data) {
            $label = Label::find($data['label_id']);
            $label->projects()->attach($data['projects']);
        }

        return response(['status:' => 'ok']);
    }

    public function list(Request $request)
    {
        $query = Label::query()->select('labels.*')
            ->join('label_project', 'labels.id', '=',
                'label_project.label_id')
            ->join('project_user', 'label_project.project_id', '=',
                'project_user.project_id')
            ->where('project_user.user_id', auth()->id());

        if ($request->has('email')) {
            $query->join('users', 'labels.user_id', '=', 'users.id')
                ->where('email', '=', $request->get('email'));
        }

        if ($request->has('projects')) {
            $query->whereIn('label_project.project_id', $request->get('projects'));
        }

        return LabelResource::collection($query->distinct()->get());
    }

    public function destroy(Request $request)
    {
        $requests = $request->all();

        foreach ($requests as $data) {

            $label = Label::find($data);

            abort_if($request->user()->cannot('delete', $label), 403, 'you can not delete this labels');

            $label->delete();
        }

        return response(['status:' => 'ok']);
    }
}
