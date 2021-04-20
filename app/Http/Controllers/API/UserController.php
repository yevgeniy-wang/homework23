<?php


namespace App\Http\Controllers\API;


use App\Http\Resources\UserResource;
use App\Jobs\VerificationMail;
use App\Models\Country;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UserController
{
    public function store(Request $request)
    {
        $requests = $request->all();

        $validator = Validator::make($requests, [
                '*.name'         => ['required', 'min:5'],
                '*.email'        => ['required', 'unique:users,email', 'email:rfc,dns',],
                '*.password'     => ['required', 'min:8',],
                '*.country_code' => ['required', 'exists:countries,code'],
            ]
        )->validate();

        foreach ($requests as $data) {
            $country_id = Country::where('code', $data['country_code'])
                ->pluck('id')->first();
            $data['country_id'] = $country_id;
            $data['password'] = Hash::make($data['password']);
            $data['verification_token'] = Str::random(45);
            $user = User::create($data);

            VerificationMail::dispatch($user)->onQueue('email');
        }

        return response(['status:' => 'ok']);
    }

    public function list(Request $request)
    {
        $query = User::query()->join('countries', 'users.country_id', '=',
            'countries.id')->select('users.id', 'users.name', 'users.email',
            'email_verified_at', 'countries.country_name');

        if ($request->has('name')) {
            $query->where('users.name', $request->get('name'));
        }

        if ($request->has('email')) {
            $query->where('email', $request->get('email'));
        }

        if ($request->has('is_verified')) {
            $query->whereNotNull('email_verified_at');
        }

        if ($request->has('country_code')) {
            $query->where('code', $request->get('country_code'));
        }


        return new UserResource($query->get()->first());
    }

    public function update(Request $request)
    {
        $requests = $request->all();

        $validator = Validator::make($requests, [
                '*.name'         => ['required', 'min:5'],
                '*.email'        => ['required', 'unique:users,email', 'email:rfc,dns',],
                '*.password'     => ['required', 'min:8',],
                '*.country_code' => ['required', 'exists:countries,code'],
            ]
        )->validate();

        foreach ($requests as $data) {

            $user = User::find($data['id']);

            $country_id = Country::where('code', $data['country_code'])
                ->pluck('id')->first();
            $data['country_id'] = $country_id;
            $data['password'] = Hash::make($data['password']);
            $user->update($data);

        }

        return response(['status:' => 'ok']);
    }

    public function destroy(Request $request)
    {
        $requests = $request->all();

        foreach ($requests as $data) {

            $user = User::find($data);
            $user->linkedProjects()->detach();
            $user->delete();
        }

        return response(['status:' => 'ok']);
    }
}
