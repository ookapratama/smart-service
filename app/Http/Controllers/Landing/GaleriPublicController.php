<?php

namespace App\Http\Controllers\Landing;

use App\Http\Controllers\Controller;
use App\Models\Galeri;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GaleriPublicController extends Controller
{
    /**
     * Display public gallery catalog with category search and AJAX pagination.
     */
    public function index(Request $request)
    {
        $query = Galeri::published();

        // Search Filter
        if ($request->filled('q')) {
            $keyword = $request->input('q');
            $query->where(function ($q) use ($keyword) {
                $q->where('judul', 'like', "%{$keyword}%")
                  ->orWhere('keterangan', 'like', "%{$keyword}%");
            });
        }

        // Category Filter
        if ($request->filled('kategori')) {
            $query->where('kategori', $request->input('kategori'));
        }

        // Type Filter (foto / video)
        if ($request->filled('tipe')) {
            $query->where('tipe', $request->input('tipe'));
        }

        $galeriList = $query->latest('id')->paginate(9)->withQueryString();

        if ($request->ajax() || $request->has('ajax') || $request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'html'   => view('home.galeri.partials.grid', compact('galeriList'))->render(),
                'total'  => $galeriList->total()
            ]);
        }

        // Categories list
        $categories = Galeri::published()
            ->select('kategori')
            ->distinct()
            ->pluck('kategori')
            ->filter();

        $recentGaleri = Galeri::published()->latest('id')->take(6)->get();

        return view('home.galeri.index', compact('galeriList', 'categories', 'recentGaleri'));
    }

    /**
     * Display gallery detail item by slug or id.
     */
    public function show(string $slug): View
    {
        $galeri = Galeri::where('slug', $slug)->first();

        if (!$galeri) {
            $galeri = Galeri::published()->where('id', $slug)->firstOrFail();
        }

        $galeri->increment('views');

        $relatedGaleri = Galeri::published()
            ->where('id', '!=', $galeri->id)
            ->latest('id')
            ->take(3)
            ->get();

        return view('home.galeri.show', compact('galeri', 'relatedGaleri'));
    }
}
