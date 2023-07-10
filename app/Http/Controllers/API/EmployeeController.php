<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class EmployeeController extends Controller
{
    public function fetch(Request $request)
    {
        try {
            $employees = \App\Models\Employee::query()->latest()->with(['role', 'team']);
            // find employee by id
            if ($request->has('id')) {
                $employees = $employees->find($request->id);
                if ($employees) {
                    return ResponseFormatter::success($employees, 'Success');
                }
                return ResponseFormatter::error(null, 'Not found', 404);
            }
            $limit = $request->input('limit', 10);
            // find employee by name
            if ($request->has('name')) {
                $employees = $employees->where('name', 'like', "%$request->name%");
            }
            // find employee by company id
            if ($request->has('company_id')) {
                $employees = $employees->whereHas('role.company', function ($query) use ($request) {
                    $query->where('id', $request->company_id);
                });
                // return response
                if ($employees) {
                    return ResponseFormatter::success($employees->cursorPaginate($limit), 'Success');
                } else {
                    return ResponseFormatter::error(null, 'Not found', 404);
                }
            }
            // find employee by team id
            if ($request->has('team_id')) {
                $employees = $employees->whereHas('team', function ($query) use ($request) {
                    $query->where('id', $request->team_id);
                });
                // return response
                if ($employees) {
                    return ResponseFormatter::success($employees->cursorPaginate($limit), 'Success');
                } else {
                    return ResponseFormatter::error(null, 'Not found', 404);
                }
            }
            // find employee by role id
            if ($request->has('role_id')) {
                $employees = $employees->whereHas('role', function ($query) use ($request) {
                    $query->where('id', $request->role_id);
                });
                // return response
                if ($employees) {
                    return ResponseFormatter::success($employees->cursorPaginate($limit), 'Success');
                } else {
                    return ResponseFormatter::error(null, 'Not found', 404);
                }
            }
            // return response
            if ($employees) {
                return ResponseFormatter::success($employees->cursorPaginate($limit), 'Success');
            } else {
                return ResponseFormatter::error(null, 'Not found', 404);
            }
        } catch (\Throwable $th) {
            Log::alert($th);
            return ResponseFormatter::error($th, 'Data gagal diambil', 500);
        }
    }

    public function create(CreateEmployeeRequest $request)
    {
        try {
            $employee = \App\Models\Employee::create($request->validated());
            if ($employee) {
                $employee->load(['role', 'team']);
                return ResponseFormatter::success($employee, 'Success');
            } else {
                return ResponseFormatter::error(null, 'Failed', 400);
            }
        } catch (\Throwable $th) {
            return ResponseFormatter::error($th, 'Data gagal dibuat', 500);
        }
    }

    public function update(UpdateEmployeeRequest $request, $id)
    {
        try {
            $employee = \App\Models\Employee::find($id);
            if ($employee) {
                $employee->update($request->validated());
                return ResponseFormatter::success($employee, 'Success');
            } else {
                return ResponseFormatter::error(null, 'Not found', 404);
            }
        } catch (\Throwable $th) {
            return ResponseFormatter::error($th, 'Data gagal diupdate', 500);
        }
    }

    public function destroy($id)
    {
        try {
            $employee = \App\Models\Employee::find($id);
            if ($employee) {
                $employee->delete();
                return ResponseFormatter::success(null, 'Success');
            } else {
                return ResponseFormatter::error(null, 'Not found', 404);
            }
        } catch (\Throwable $th) {
            return ResponseFormatter::error($th, 'Data gagal dihapus', 500);
        }
    }
}
