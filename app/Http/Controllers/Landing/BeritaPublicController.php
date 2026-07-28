<?php

namespace App\Http\Controllers\Landing;

use App\Http\Controllers\Controller;
use App\Models\Berita;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BeritaPublicController extends Controller
{
    /**
     * Display public news catalog with pagination and category search.
     * Supports both full page render & dynamic AJAX JSON/HTML updates.
     *
     * @param Request $request
     * @return View|JsonResponse
     */
    public function index(Request $request)
    {
        $query = Berita::published();

        // Search Filter
        if ($request->filled('q')) {
            $keyword = $request->input('q');
            $query->where(function ($q) use ($keyword) {
                $q->where('judul', 'like', "%{$keyword}%")
                  ->orWhere('ringkasan', 'like', "%{$keyword}%")
                  ->orWhere('isi', 'like', "%{$keyword}%");
            });
        }

        // Category Filter
        if ($request->filled('kategori')) {
            $query->where('kategori', $request->input('kategori'));
        }

        $beritaList = $query->latest('published_at')->paginate(9)->withQueryString();

        // If AJAX request, return rendered grid snippet & metadata JSON
        if ($request->ajax() || $request->has('ajax') || $request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'html'   => view('home.berita.partials.grid', compact('beritaList'))->render(),
                'total'  => $beritaList->total()
            ]);
        }

        // Featured News for Hero Banner
        $featuredBerita = Berita::published()->latest('published_at')->first();

        // Get Available Categories
        $categories = Berita::published()
            ->select('kategori')
            ->distinct()
            ->pluck('kategori')
            ->filter();

        // Recent News for Sidebar
        $recentBerita = Berita::published()->latest('published_at')->take(5)->get();

        return view('home.berita.index', compact(
            'beritaList',
            'featuredBerita',
            'categories',
            'recentBerita'
        ));
    }

    /**
     * Display detailed news article by slug.
     *
     * @param string $slug
     * @return View
     */
    public function show(string $slug): View
    {
        $berita = Berita::where('slug', $slug)->first();

        if (!$berita) {
            // Fallback for sample item if db is empty or ID used
            $berita = Berita::published()->where('id', $slug)->firstOrFail();
        }

        // Increment Views if column exists
        if (\Schema::hasColumn('berita', 'views')) {
            $berita->increment('views');
        }

        // Related News
        $relatedBerita = Berita::published()
            ->where('id', '!=', $berita->id)
            ->latest('published_at')
            ->take(3)
            ->get();

        $categories = Berita::published()
            ->select('kategori')
            ->distinct()
            ->pluck('kategori')
            ->filter();

        return view('home.berita.show', compact('berita', 'relatedBerita', 'categories'));
    }
}
