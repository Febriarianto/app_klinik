<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Dokter;
use App\Models\Pasien;
use App\Models\Perawatan;

class DashboardController extends Controller
{
  function __construct()
  {
    $this->middleware('auth');
    // $this->middleware('can:dashboard-create', ['only' => ['create', 'store']]);
    // $this->middleware('can:dashboard-edit', ['only' => ['edit', 'update']]);
    // $this->middleware('can:dashboard-delete', ['only' => ['destroy']]);
  }

  public function index()
  {
    $config['title'] = "";
    $config['breadcrumbs'] = [
      ['url' => '#', 'title' => ""],
    ];

    $countDokter = Dokter::count();
    $countPasien = Pasien::count();
    $countPerawatan = Perawatan::count();

    $data = [
      'countDokter' => $countDokter,
      'countPasien' => $countPasien,
      'countPerawatan' => $countPerawatan,
    ];

    return view('pages.backend.dashboard.index', compact('config', 'data'));
  }
}
