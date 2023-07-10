<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateResponsibilityRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ResponsibilityController extends Controller
{
    public function fetch(Request $request)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id',
        ]);
        try {
            $responsibilities = \App\Models\Responsibility::with('role')->whereHas('role.company.users', function ($query) {
                $query->where('user_id', Auth::user()->id);
            })->where('role_id', $request->role_id);
            $limit = $request->input('limit', 10);
            // if request has id, then get roles by id
            if ($request->has('id')) {
                $responsibilities = $responsibilities->find($request->id);
                if ($responsibilities) {
                    return ResponseFormatter::success($responsibilities, 'Success');
                }
                return ResponseFormatter::error(null, 'Not found', 404);
            }

            // if request has name, then get roles by name
            if ($request->has('name')) {
                $responsibilities->where('name', 'like', '%' . $request->name . '%');
            }
            return ResponseFormatter::success($responsibilities->cursorPaginate($limit), 'Success');
        } catch (\Throwable $th) {
            Log::debug("Error: $th");
            return ResponseFormatter::error($th, 'Data gagal diambil', 500);
        }
    }

    public function create(CreateResponsibilityRequest $request)
    {
        try {
            $role = \App\Models\Role::whereHas('company.users', function ($query) {
                $query->where('user_id', Auth::user()->id);
            })->find($request->role_id);
            // get responsibility from role and create new responsibility
            $create = $role->responsibilities()->create(['name' => $request->name]);
            if ($create) {
                $create->load('role');
                return ResponseFormatter::success($create, 'Success');
            } else {
                return ResponseFormatter::error(null, 'Failed', 400);
            }
        } catch (\Throwable $th) {
            Log::debug("Error: $th");
            return ResponseFormatter::error($th, 'Data gagal dibuat', 500);
        }
    }

    public function destroy($id)
    {
        $responsibility = \App\Models\Responsibility::whereHas('role.company.users', function ($query) {
            $query->where('user_id', Auth::user()->id);
        })->find($id);
        if ($responsibility) {
            $responsibility->delete();
            return ResponseFormatter::success(null, 'Success');
        } else {
            return ResponseFormatter::error(null, 'Not found', 404);
        }
    }
}
