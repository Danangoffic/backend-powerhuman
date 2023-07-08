<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateTeamRequest;
use App\Http\Requests\UpdateTeamRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TeamController extends Controller
{
    public function create(CreateTeamRequest $request)
    {
        try {
            $name = $request->name;
            $path = null;
            if ($request->hasFile('icon')) {
                $path = $request->file('icon')->store('public/company/teams');
            }
            $company = \App\Models\Company::find($request->company_id);
            if ($company) {
                $team = $company->teams()->create([
                    'name' => $name,
                    'icon' => $path,
                ]);
                return ResponseFormatter::success($team, 'Success');
            }
            return ResponseFormatter::error(null, 'Company not found', 404);
        } catch (\Throwable $th) {
            Log::debug("Error: $th");
            return ResponseFormatter::error($th, 'Data gagal diambil', 500);
        }
    }

    public function fetch(Request $request)
    {
        try {
            $teams = \App\Models\Team::with('company')->whereHas('company.users', function ($query) {
                $query->where('user_id', Auth::user()->id);
            });
            $limit = $request->input('limit', 10);
            // if request has id, then get teams by id
            if ($request->has('id')) {
                $teams = \App\Models\Team::with('company')->whereHas('company.users', function ($query) {
                    $query->where('user_id', Auth::user()->id);
                })->find($request->id);
                if ($teams) {
                    return ResponseFormatter::success($teams, 'Success');
                }
                return ResponseFormatter::error(null, 'Not found', 404);
            }

            // if request has name, then get teams by name
            if ($request->has('name')) {
                $teams->where('name', 'like', '%' . $request->name . '%');
            }
            return ResponseFormatter::success($teams->paginate($limit), 'Success');
        } catch (\Throwable $th) {
            Log::debug("Error: $th");
            return ResponseFormatter::error($th, 'Data gagal diambil', 500);
        }
    }

    /**
     * Update a team by team id and company id where user id is auth user id
     * @param UpdateTeamRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateTeamRequest $request, $id)
    {
        try {
            // get team by id and company id where user id is auth user id
            $team = \App\Models\Team::with('company')->whereHas('company.users', function ($query) {
                $query->where('user_id', Auth::user()->id);
            })->find($id);
            // if team exists, then update
            if ($team) {
                $name = $request->name;
                $path = null;
                // if request has icon, then update icon
                if ($request->hasFile('icon')) {
                    $path = $request->file('icon')->store('public/company/teams');
                }
                // update team
                $team->update([
                    'name' => $name,
                    'icon' => $path,
                ]);
                return ResponseFormatter::success($team, 'Success');
            } else {
                return ResponseFormatter::error(null, 'Team not found', 404);
            }
        } catch (\Throwable $th) {
            Log::debug("Error: $th");
            return ResponseFormatter::error($th, 'Data gagal diambil', 500);
        }
    }

    /**
     * Delete a team by team id and company id where user id is auth user id
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($id)
    {
        try {
            // get team by id and company id where user id is auth user id on company_users table
            $team = \App\Models\Team::with('company')->whereHas('company.users', function ($query) {
                $query->where('user_id', Auth::user()->id);
            })->find($id);
            // if team exists, then delete
            if ($team) {
                // delete team
                $team->delete();
                return ResponseFormatter::success(null, 'Success');
            } else {
                return ResponseFormatter::error(null, 'Team not found', 404);
            }
        } catch (\Throwable $th) {
            Log::debug("Error: $th");
            return ResponseFormatter::error($th, 'Data gagal diambil', 500);
        }
    }
}
