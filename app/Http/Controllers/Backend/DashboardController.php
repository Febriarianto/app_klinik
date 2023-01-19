<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Puskesmas;
use App\Models\User;

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

    $countAdmin = User::where('role_id', '1')->count();
    $countOperator = User::where('role_id', '2')->count();

    $data = [
      'countAdmin' => $countAdmin,
      'countOperator' => $countOperator,
    ];

    return view('pages.backend.dashboard.index', compact('config', 'data'));
  }
}
