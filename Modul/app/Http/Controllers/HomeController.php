<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Menampilkan halaman katalog buku.
     */
    public function index(Request $request)
    {
        $query = Buku::query();

        // 1. Pencarian buku
        if ($request->filled('search')) {
            $search = $request->input('search');

            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('penulis', 'like', "%{$search}%")
                  ->orWhere('penerbit', 'like', "%{$search}%");
            });
        }

        // 2. Filter penerbit
        if ($request->filled('penerbit')) {
            $query->where('penerbit', $request->input('penerbit'));
        }

        // 3. Filter ketersediaan stok
        if ($request->filled('status')) {
            if ($request->status === 'tersedia') {
                $query->where('stok', '>', 0);
            } elseif ($request->status === 'habis') {
                $query->where('stok', '<=', 0);
            }
        }

        // 4. Sorting atau pengurutan data
        switch ($request->input('sort')) {
            case 'judul_asc':
                $query->orderBy('judul', 'asc');
                break;

            case 'judul_desc':
                $query->orderBy('judul', 'desc');
                break;

            case 'tahun_desc':
                $query->orderBy('tahun_terbit', 'desc');
                break;

            case 'tahun_asc':
                $query->orderBy('tahun_terbit', 'asc');
                break;

            case 'stok_desc':
                $query->orderBy('stok', 'desc');
                break;

            default:
                $query->latest();
                break;
        }

        // 5. Pagination
        $buku = $query->paginate(8)->withQueryString();

        // 6. Daftar penerbit untuk dropdown
        $listPenerbit = Buku::select('penerbit')
            ->distinct()
            ->orderBy('penerbit', 'asc')
            ->pluck('penerbit');

        return view('home', compact('buku', 'listPenerbit'));
    }

    /**
     * Menampilkan detail buku.
     */
    public function show(Buku $buku)
    {
        return view('home_show', compact('buku'));
    }
}
