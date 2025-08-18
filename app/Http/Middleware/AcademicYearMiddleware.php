<?php

namespace App\Http\Middleware;

use App\Models\AcademicYear;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AcademicYearMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            if (Auth::user()->hasRole('admin')) {
                // Jika pengguna adalah admin, kita bisa mengatur tahun akademik aktif berdasarkan input request
                if ($request->has('academic_year_id')) {
                    $academicYearId = $request->input('academic_year_id');
                    $request->merge(['academic_year_id' => $academicYearId]);
                } else {
                    $request->merge(['academic_year_id' => AcademicYear::active()->first()?->id]);
                }
            } else {
                // Jika peran tidak dikenali, tidak melakukan apa-apa.
                $request->merge(['academic_year_id' => AcademicYear::active()->first()?->id]);
            }
        } else {
            // Jika pengguna tidak terautentikasi, kita bisa mengembalikan respons error atau melakukan tindakan lain.
            $request->merge(['academic_year_id' => AcademicYear::active()->first()?->id]);
        }

        return $next($request);
    }
}
