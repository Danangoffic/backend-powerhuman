<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RoleController extends Controller
{
    public function create(CreateRoleRequest $request)
    {
        try {
            $name = $request->name;
            $company = \App\Models\Company::find($request->company_id);
            if ($company) {
                $team = $company->roles()->create([
                    'name' => $name,
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
            $roles = \App\Models\Role::with('company')->whereHas('company.users', function ($query) {
                $query->where('user_id', Auth::user()->id);
            });
            $limit = $request->input('limit', 10);
            // if request has id, then get roles by id
            if ($request->has('id')) {
                $roles = $roles->find($request->id);
                if ($roles) {
                    return ResponseFormatter::success($roles, 'Success');
                }
                return ResponseFormatter::error(null, 'Not found', 404);
            }

            // if request has name, then get roles by name
            if ($request->has('name')) {
                $roles->where('name', 'like', '%' . $request->name . '%');
            }
            return ResponseFormatter::success($roles->paginate($limit), 'Success');
        } catch (\Throwable $th) {
            Log::debug("Error: $th");
            return ResponseFormatter::error($th, 'Data gagal diambil', 500);
        }
    }

    /**
     * Update a role by role id and company id where user id is auth user id
     */
    public function update(UpdateRoleRequest $request, $id)
    {
        try {
            // get role by id and company id where user id is auth user id
            $role = \App\Models\Role::with('company')->whereHas('company.users', function ($query) {
                $query->where('user_id', Auth::user()->id);
            })->find($id);
            // if role exists, then update
            if ($role) {
                $name = $request->name;
                // update role
                $role->update([
                    'name' => $name,
                ]);
                return ResponseFormatter::success($role, 'Success');
            } else {
                return ResponseFormatter::error(null, 'Team not found', 404);
            }
        } catch (\Throwable $th) {
            Log::debug("Error: $th");
            return ResponseFormatter::error($th, 'Data gagal diambil', 500);
        }
    }

    /**
     * Delete a role by role id and company id where user id is auth user id
     */
    public function delete($id)
    {
        try {
            // get role by id and company id where user id is auth user id on company_users table
            $role = \App\Models\Role::with('company')->whereHas('company.users', function ($query) {
                $query->where('user_id', Auth::user()->id);
            })->find($id);
            // if team exists, then delete
            if ($role) {
                // delete role
                $role->delete();
                return ResponseFormatter::success(null, 'Success');
            } else {
                return ResponseFormatter::error(null, 'role not found', 404);
            }
        } catch (\Throwable $th) {
            Log::debug("Error: $th");
            return ResponseFormatter::error($th, 'Data gagal diambil', 500);
        }
    }
}
