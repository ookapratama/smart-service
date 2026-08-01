<?php

namespace App\Http\Controllers\Landing;

use App\Http\Controllers\Controller;
use App\Models\Agenda;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AgendaPublicController extends Controller
{
    /**
     * Display public event agenda catalog with filters and pagination.
     */
    public function index(Request $request)
    {
        $query = Agenda::published();

        // Search Filter
        if ($request->filled('q')) {
            $keyword = $request->input('q');
            $query->where(function ($q) use ($keyword) {
                $q->where('judul', 'like', "%{$keyword}%")
                  ->orWhere('deskripsi', 'like', "%{$keyword}%")
                  ->orWhere('lokasi', 'like', "%{$keyword}%");
            });
        }

        // Category Filter
        if ($request->filled('kategori')) {
            $query->where('kategori', $request->input('kategori'));
        }

        // Status Filter (mendatang / selesai)
        if ($request->filled('status')) {
            $status = $request->input('status');
            if ($status === 'mendatang') {
                $query->where('mulai_at', '>=', now());
            } elseif ($status === 'selesai') {
                $query->where('mulai_at', '<', now());
            }
        }

        $agendaList = $query->orderBy('mulai_at', 'desc')->paginate(9)->withQueryString();

        if ($request->ajax() || $request->has('ajax') || $request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'html'   => view('home.agenda.partials.grid', compact('agendaList'))->render(),
                'total'  => $agendaList->total()
            ]);
        }

        // Upcoming Agenda Highlight
        $upcomingAgenda = Agenda::published()->where('mulai_at', '>=', now())->orderBy('mulai_at', 'asc')->first();

        // Categories list
        $categories = Agenda::published()
            ->select('kategori')
            ->distinct()
            ->pluck('kategori')
            ->filter();

        return view('home.agenda.index', compact('agendaList', 'upcomingAgenda', 'categories'));
    }

    /**
     * Display agenda event details by slug or id.
     */
    public function show(string $slug): View
    {
        $agenda = Agenda::where('slug', $slug)->first();

        if (!$agenda) {
            $agenda = Agenda::published()->where('id', $slug)->firstOrFail();
        }

        $agenda->increment('views');

        $relatedAgenda = Agenda::published()
            ->where('id', '!=', $agenda->id)
            ->orderBy('mulai_at', 'desc')
            ->take(3)
            ->get();

        return view('home.agenda.show', compact('agenda', 'relatedAgenda'));
    }
}
