<?php

namespace App\Http\Controllers\Landing;

use App\Http\Controllers\Controller;
use App\Models\JadwalPelayanan;
use App\Models\JenisSurat;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CekStatusPublicController extends Controller
{
    /**
     * Display public Cek Status page.
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        $keyword = $request->query('q', $request->query('keyword', ''));

        $jenisSuratList = JenisSurat::where('is_active', true)->get();
        $jadwalList = JadwalPelayanan::active()->with('kelurahan')->get();

        return view('home.cek-status.index', compact('keyword', 'jenisSuratList', 'jadwalList'));
    }
}
