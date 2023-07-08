<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Http\Requests\CreateCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CompanyController extends Controller
{
    /**
     * Get all companies or by id and limit
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $companies = \App\Models\Company::whereHas('users', function ($query) {
                $query->where('user_id', Auth::user()->id);
            });
            $limit = $request->input('limit', 10);
            // if request has id, then get company by id
            if ($request->has('id')) {
                $company = \App\Models\Company::whereHas('users', function ($query) {
                    $query->where('user_id', Auth::user()->id);
                })->find($request->id);
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

    /**
     * Create a company
     * @param CreateCompanyRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(CreateCompanyRequest $request)
    {
        try {
            $name = $request->name;
            $path = null;
            if ($request->hasFile('logo')) {
                $path = $request->file('logo')->store('public/images/logos');
            }
            $company = Company::create([
                'name' => $request->name,
                'logo' => $path,
            ]);
            if (!$company) {
                return ResponseFormatter::error(null, 'Data gagal ditambahkan', 500);
            }
            $user = User::find(Auth::user()->id);
            $user->companies()->attach($company->id);
            $company->load('users');
            return ResponseFormatter::success($company, 'Success');
        } catch (\Throwable $th) {
            Log::debug("Error: $th");
            return ResponseFormatter::error(['error' => $th->getMessage()], 'Data gagal ditambahkan', 500);
        }
    }

    /**
     * Update a company by company id
     * @param UpdateCompanyRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateCompanyRequest $request, $id)
    {
        try {
            $company = Company::find($id);
            if (!$company) {
                return ResponseFormatter::error(null, 'Data gagal diupdate', 500);
            }
            if ($request->has('name')) {
                $company->name = $request->name;
            }

            if ($request->hasFile('logo')) {
                $path = $request->file('logo')->store('public/images/logos');
                $company->logo = $path;
            }
            $company->save();
            $company->load('users');
            return ResponseFormatter::success($company, 'Success');
        } catch (\Throwable $th) {
            Log::debug("Error: $th");
            return ResponseFormatter::error(['error' => $th->getMessage()], 'Data gagal diupdate', 500);
        }
    }
}
