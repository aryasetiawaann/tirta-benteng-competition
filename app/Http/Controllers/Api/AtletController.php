<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Atlet;
use Illuminate\Http\Request;

class AtletController extends Controller
{
    /**
     * Fetch atlets with track records and optional filters.
     */
    public function index(Request $request)
    {
        $query = Atlet::with(['trackRecords', 'user']);

        // Filter by user's club name (the relationship)
        if ($request->has('user_name')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->input('user_name') . '%');
            });
        }

        // Filter by atlet name
        if ($request->has('name')) {
            $query->where('name', 'like', '%' . $request->input('name') . '%');
        }

        // Filter by birth year (from `umur` typically structured as a date YYYY-MM-DD or Age)
        // Adjust depending on how 'umur' is stored. Assuming it's date logic
        if ($request->has('birth_year')) {
            $query->whereYear('umur', $request->input('birth_year'));
        }

        // Filter by province
        if ($request->has('province')) {
            $query->where('province', 'like', '%' . $request->input('province') . '%');
        }

        // Filter by regency
        if ($request->has('regency')) {
            $query->where('regency', 'like', '%' . $request->input('regency') . '%');
        }

        $atlets = $query->paginate(20);

        // Transform collection to add full URLs to images
        $atlets->getCollection()->transform(function ($atlet) {
            $data = $atlet->toArray();
            
            // Convert 'dokumen' to full url
            $data['dokumen_url'] = $atlet->dokumen ? asset($atlet->dokumen) : null;
            
            // Convert 'user.foto' to full url if exists
            if (isset($data['user']) && !empty($data['user']['foto'])) {
                $data['user']['foto_url'] = asset($data['user']['foto']);
            }

            return $data;
        });

        return response()->json([
            'success' => true,
            'data' => $atlets->items(),
            'meta' => [
                'current_page' => $atlets->currentPage(),
                'last_page' => $atlets->lastPage(),
                'per_page' => $atlets->perPage(),
                'total' => $atlets->total(),
            ]
        ], 200);
    }
}
