<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

trait ApiQueryBuilder
{
    /**
     * Menerapkan filter, sorting, dan pagination pada query
     */
    protected function applyQueryBuilder(Request $request, Builder $query)
    {
        // Terapkan filter
        $this->applyFilters($request, $query);
        
        // Terapkan sorting
        $this->applySorting($request, $query);
        
        // Terapkan pagination
        return $this->applyPagination($request, $query);
    }
    
    /**
     * Menerapkan filter berdasarkan parameter request
     */
    protected function applyFilters(Request $request, Builder $query)
    {
        if ($request->has('filter')) {
            $filters = $request->filter;
            
            foreach ($filters as $field => $value) {
                // Jika nilai adalah array, gunakan whereIn
                if (is_array($value)) {
                    $query->whereIn($field, $value);
                } else {
                    // Jika mengandung wildcard, gunakan LIKE
                    if (strpos($value, '*') !== false) {
                        $value = str_replace('*', '%', $value);
                        $query->where($field, 'LIKE', $value);
                    } else {
                        $query->where($field, $value);
                    }
                }
            }
        }
        
        return $query;
    }
    
    /**
     * Menerapkan sorting berdasarkan parameter request
     */
    protected function applySorting(Request $request, Builder $query)
    {
        if ($request->has('sort')) {
            $sortParams = explode(',', $request->sort);
            
            foreach ($sortParams as $param) {
                $direction = 'asc';
                
                if (strpos($param, '-') === 0) {
                    $direction = 'desc';
                    $param = substr($param, 1);
                }
                
                $query->orderBy($param, $direction);
            }
        } else {
            // Default sorting
            $query->orderBy('created_at', 'desc');
        }
        
        return $query;
    }
    
    /**
     * Menerapkan pagination berdasarkan parameter request
     */
    protected function applyPagination(Request $request, Builder $query)
    {
        $perPage = $request->input('per_page', 15);
        
        return $query->paginate($perPage);
    }
}