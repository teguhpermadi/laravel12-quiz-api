<?php

namespace App\Http\Controllers;

use App\Http\Requests\LiteratureRequest;
use App\Http\Resources\LiteratureResource;
use App\Models\Literature;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;

class LiteratureController extends Controller
{
    /**
     * Menampilkan daftar semua literatur dengan filter, sorting, dan pagination
     */
    public function index(Request $request)
    {
        $query = QueryBuilder::for(Literature::class)
            ->allowedFilters(Literature::allowedFilters())
            ->allowedSorts(Literature::allowedSorts())
            ->allowedIncludes(Literature::allowedIncludes())
            ->withCount('questions');
            
        // Tambahkan withCount jika diperlukan
        if (in_array('questions', $query->getEagerLoads())) {
            $query->withCount('questions');
        }
            
        $literatures = $query->paginate($request->input('per_page', 15))
            ->appends($request->query());
        
        return response()->json([
            'status' => 'success',
            'data' => LiteratureResource::collection($literatures),
            'meta' => [
                'current_page' => $literatures->currentPage(),
                'from' => $literatures->firstItem(),
                'last_page' => $literatures->lastPage(),
                'per_page' => $literatures->perPage(),
                'to' => $literatures->lastItem(),
                'total' => $literatures->total(),
            ],
            'links' => [
                'first' => $literatures->url(1),
                'last' => $literatures->url($literatures->lastPage()),
                'prev' => $literatures->previousPageUrl(),
                'next' => $literatures->nextPageUrl(),
            ],
        ]);
    }

    /**
     * Menyimpan literatur baru
     */
    public function store(LiteratureRequest $request)
    {
        $literature = Literature::create($request->validated());
        
        if ($request->hasFile('media')) {
            $literature->addMediaFromRequest('media')
                ->toMediaCollection('literature_media');
        }
        
        return response()->json([
            'status' => 'success',
            'message' => 'Literature added successfully',
            'data' => new LiteratureResource($literature)
        ], 201);
    }

    /**
     * Menampilkan detail literatur
     */
    public function show(Literature $literature)
    {
        // Load questions jika diperlukan
        if (request()->has('include') && str_contains(request()->input('include'), 'questions')) {
            $literature->load('questions');
        }
        
        return response()->json([
            'status' => 'success',
            'data' => new LiteratureResource($literature)
        ]);
    }

    /**
     * Memperbarui data literatur
     */
    public function update(LiteratureRequest $request, Literature $literature)
    {
        $literature->update($request->validated());
        
        if ($request->hasFile('media')) {
            $literature->clearMediaCollection('literature_media');
            $literature->addMediaFromRequest('media')
                ->toMediaCollection('literature_media');
        }
        
        return response()->json([
            'status' => 'success',
            'message' => 'Literature updated successfully',
            'data' => new LiteratureResource($literature)
        ]);
    }

    /**
     * Menghapus data literatur
     */
    public function destroy(Literature $literature)
    {
        $literature->delete();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Literature deleted successfully'
        ]);
    }
}