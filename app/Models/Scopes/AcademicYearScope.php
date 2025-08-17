<?php

namespace App\Models\Scopes;

use App\Models\AcademicYear;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class AcademicYearScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model)
    {
        $academic_year_id = AcademicYear::active()->first();
        
        if($academic_year_id){
            return $builder->where('academic_year_id', $academic_year_id->id);
        } else {
            return $builder->where('academic_year_id', -1);
        }
    }
}
