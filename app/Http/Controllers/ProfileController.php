<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Profile;
use Carbon\Carbon;


class ProfileController extends Controller
{
    public function index(Request $request)
    {
        try {
            $validated = $request->validate([
            'gender' => 'in:male,female',
            'age_group' => 'in:child,teenager,adult,senior',
            'country_id' => 'string|size:2',
            'min_age' => 'integer|min:0',
            'max_age' => 'integer|min:0',
            'min_gender_probability' => 'numeric|between:0,1',
            'min_country_probability' => 'numeric|between:0,1',
            'sort_by' => 'in:age,created_at,gender_probability',
            'order' => 'in:asc,desc',
            'page' => 'integer|min:1',
            'limit' => 'integer|min:1|max:50'
        ]);

        } catch (\Illuminate\Validation\ValidationException $e) 
        { 
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid query parameters'
            ], 422);
        }

        $query = Profile::query();

        // FILTERING
        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        if ($request->filled('age_group')) {
            $query->where('age_group', $request->age_group);
        }

        if ($request->filled('country_id')) {
            $query->where('country_id', strtoupper($request->country_id));
        }

        if ($request->filled('min_age')) {
            $query->where('age', '>=', $request->min_age);
        }

        if ($request->filled('max_age')) {
            $query->where('age', '<=', $request->max_age);
        }

        if ($request->filled('min_gender_probability')) {
            $query->where('gender_probability', '>=', $request->min_gender_probability);
        }

        if ($request->filled('min_country_probability')) {
            $query->where('country_probability', '>=', $request->min_country_probability);
        }

        // SORTING
        $sortBy = $request->sort_by ?? 'created_at';
        $order = $request->order ?? 'desc';

        $query->orderBy($sortBy, $order);

        // PAGINATION
        $limit = min($request->limit ?? 10, 50);
        $page = $request->page ?? 1;

        $result = $query->paginate($limit, ['*'], 'page', $page);

        return response()->json([
            'status' => 'success',
            'page' => $result->currentPage(),
            'limit' => $limit,
            'total' => $result->total(),
            'data' => collect($result->items())->map(function ($item) {
                $item['created_at'] = Carbon::parse($item['created_at'])
                    ->utc()
                    ->format('Y-m-d\TH:i:s\Z');

                return $item;
            })
        ]);
    }

    public function search(Request $request)
    {

        try {
            $validated = $request->validate([
                'q' => 'required|string',
                'page' => 'integer|min:1',
                'limit' => 'integer|min:1|max:50'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid query parameters'
            ], 422);
        }

        $q = strtolower($request->q);

        $query = Profile::query();    
       $interpreted = false;

       // GENDER
        if (str_contains($q, 'male') && str_contains($q, 'female')) {
            $interpreted = true; // no filter applied
        } elseif (str_contains($q, 'female')) {
            $query->where('gender', 'female');
            $interpreted = true;
        } elseif (str_contains($q, 'male')) {
            $query->where('gender', 'male');
            $interpreted = true;
        }

        // AGE KEYWORDS
        if (str_contains($q, 'young')) {
            $query->whereBetween('age', [16, 24]);
            $interpreted = true;
        }

        if (preg_match('/above (\d+)/', $q, $matches)) {
            $query->where('age', '>=', $matches[1]);
            $interpreted = true;
        }

        // AGE GROUP
        if (str_contains($q, 'adult')) {
            $query->where('age_group', 'adult');
            $interpreted = true;
        }

        if (str_contains($q, 'teenager')) {
            $query->where('age_group', 'teenager');
            $interpreted = true;
        }

        // COUNTRY
        if (str_contains($q, 'nigeria')) {
            $query->where('country_id', 'NG');
            $interpreted = true;
        }

        if (str_contains($q, 'kenya')) {
            $query->where('country_id', 'KE');
            $interpreted = true;
        }

        if (str_contains($q, 'angola')) {
            $query->where('country_id', 'AO');
            $interpreted = true;
        }

        if (!$interpreted) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unable to interpret query'
            ]);
        }

        // PAGINATION

        $limit = min($request->limit ?? 10, 50);
        $page = $request->page ?? 1;

        $result = $query->paginate($limit, ['*'], 'page', $page);

        return response()->json([
            'status' => 'success',
            'page' => $result->currentPage(),
            'limit' => $limit,
            'total' => $result->total(),
            'data' => collect($result->items())->map(function ($item) {
                $item['created_at'] = Carbon::parse($item['created_at'])
                    ->utc()
                    ->format('Y-m-d\TH:i:s\Z');

                return $item;
            })
        ]);
    }

}
