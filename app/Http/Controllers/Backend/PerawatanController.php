<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Perawatan;
use Illuminate\Http\Request;
use App\Traits\ResponseStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class PerawatanController extends Controller
{
    use ResponseStatus;

    function __construct()
    {
        $this->middleware('can:perawatan-list', ['only' => ['index', 'show']]);
        $this->middleware('can:perawatan-create', ['only' => ['create', 'store']]);
        $this->middleware('can:perawatan-edit', ['only' => ['edit', 'update']]);
        $this->middleware('can:perawatan-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $config['title'] = "Perawatan";
        $config['breadcrumbs'] = [
            ['url' => '#', 'title' => "Perawatan"],
        ];
        if ($request->ajax()) {
            $data = Perawatan::query();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $actionBtn = '<div class="btn-group dropend">
                            <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                Aksi
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="' . route('perawatan.edit', $row->id) . '">Edit</a></li>
                                <li><a class="dropdown-item btn-delete" href="#" data-id ="' . $row->id . '" >Hapus</a></li>
                            </ul>
                          </div>';
                    return $actionBtn;
                })
                ->addColumn('tharga', function ($row) {
                    $tharga = number_format($row->harga);
                    return $tharga;
                })
                ->make();
        }
        return view('pages.backend.perawatan.index', compact('config'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $config['title'] = "Tambah Perawatan";
        $config['breadcrumbs'] = [
            ['url' => route('perawatan.index'), 'title' => "Perawatan"],
            ['url' => '#', 'title' => "Tambah Perawatan"],
        ];
        $config['form'] = (object)[
            'method' => 'POST',
            'action' => route('perawatan.store')
        ];
        return view('pages.backend.perawatan.form', compact('config'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required',
            'harga' => 'required',
        ]);

        $harga = str_replace(',', '', $request['harga']);

        if ($validator->passes()) {
            DB::beginTransaction();
            try {

                Perawatan::create([
                    'nama' => $request['nama'],
                    'harga' => $harga,
                ]);

                DB::commit();
                $response = response()->json($this->responseStore(true, NULL, route('perawatan.index')));
            } catch (\Throwable $throw) {
                Log::error($throw);
                DB::rollBack();
                $response = response()->json(['error' => $throw->getMessage()]);
            }
        } else {
            $response = response()->json(['error' => $validator->errors()->all()]);
        }
        return $response;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Perawatan  $perawatan
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Perawatan  $perawatan
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $config['title'] = "Edit Perawatan";
        $config['breadcrumbs'] = [
            ['url' => route('perawatan.index'), 'title' => "Perawatan"],
            ['url' => '#', 'title' => "Edit Perawatan"],
        ];
        $data = Perawatan::find($id);
        $config['form'] = (object)[
            'method' => 'PUT',
            'action' => route('perawatan.update', $id)
        ];
        return view('pages.backend.perawatan.form', compact('config', 'data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Perawatan  $perawatan
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required',
            'harga' => 'required',
        ]);

        $harga = str_replace(',', '', $request['harga']);

        if ($validator->passes()) {
            DB::beginTransaction();
            try {

                $perawatan = Perawatan::findOrFail($id);
                $perawatan->update([
                    'nama' => $request['nama'],
                    'harga' => $harga,
                ]);

                DB::commit();
                $response = response()->json($this->responseStore(true, NULL, route('perawatan.index')));
            } catch (\Throwable $throw) {
                Log::error($throw);
                DB::rollBack();
                $response = response()->json(['error' => $throw->getMessage()]);
            }
        } else {
            $response = response()->json(['error' => $validator->errors()->all()]);
        }
        return $response;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Perawatan  $perawatan
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $response = response()->json([
            'status' => 'error',
            'message' => 'Data gagal dihapus'
        ]);
        $data = Perawatan::find($id);
        DB::beginTransaction();
        try {
            $data->delete();
            DB::commit();
            $response = response()->json([
                'status' => 'success',
                'message' => 'Data berhasil dihapus'
            ]);
        } catch (\Throwable $throw) {
            Log::error($throw);
            $response = response()->json(['error' => $throw->getMessage()]);
        }
        return $response;
    }

    public function select2(Request $request)
    {
        $page = $request->page;
        $resultCount = 10;
        $offset = ($page - 1) * $resultCount;
        $data = Perawatan::where('nama', 'LIKE', '%' . $request->q . '%')
            ->orderBy('nama')
            ->skip($offset)
            ->take($resultCount)
            ->selectRaw('id, nama as text')
            ->get();

        $count = Perawatan::where('nama', 'LIKE', '%' . $request->q . '%')
            ->get()
            ->count();

        $endCount = $offset + $resultCount;
        $morePages = $count > $endCount;

        $results = array(
            "results" => $data,
            "pagination" => array(
                "more" => $morePages
            )
        );

        return response()->json($results);
    }
}
