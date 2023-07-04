<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CompanyController extends Controller
{
    // get all companies or by id and limit
    public function index(Request $request)
    {
        try {
            $companies = \App\Models\Company::with(['users', 'teams', 'roles']);
            $limit = $request->input('limit', 10);
            // if request has id, then get company by id
            if ($request->has('id')) {
                $company = \App\Models\Company::with(['users', 'teams', 'roles'])->find($request->id);
                if ($company) {
                    return ResponseFormatter::success($company, 'Success');
                }
                return ResponseFormatter::error(null, 'Not found', 404);
            }

            // if request has name, then get company by name
            if ($request->has('name')) {
                $companies->where('name', 'like', '%' . $request->name . '%');
            }
            return ResponseFormatter::success($companies->paginate($limit), 'Success');
        } catch (\Throwable $th) {
            Log::debug("Error: $th");
            return ResponseFormatter::error($th, 'Data gagal diambil', 500);
        }
    }
}
