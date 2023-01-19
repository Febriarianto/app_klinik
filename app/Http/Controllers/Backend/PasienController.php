<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Pasien;
use Illuminate\Http\Request;
use App\Traits\ResponseStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class PasienController extends Controller
{
    use ResponseStatus;

    function __construct()
    {
        $this->middleware('can:pasien-list', ['only' => ['index', 'show']]);
        $this->middleware('can:pasien-create', ['only' => ['create', 'store']]);
        $this->middleware('can:pasien-edit', ['only' => ['edit', 'update']]);
        $this->middleware('can:pasien-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $config['title'] = "Pasien";
        $config['breadcrumbs'] = [
            ['url' => '#', 'title' => "Pasien"],
        ];
        if ($request->ajax()) {
            $data = Pasien::query();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $actionBtn = '<div class="btn-group dropend">
                            <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                Aksi
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="' . route('pasien.edit', $row->id) . '">Edit</a></li>
                                <li><a class="dropdown-item btn-delete" href="#" data-id ="' . $row->id . '" >Hapus</a></li>
                            </ul>
                          </div>';
                    return $actionBtn;
                })->make();
        }
        return view('pages.backend.pasien.index', compact('config'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $config['title'] = "Tambah Pasien";
        $config['breadcrumbs'] = [
            ['url' => route('pasien.index'), 'title' => "Pasien"],
            ['url' => '#', 'title' => "Tambah Pasien"],
        ];
        $config['form'] = (object)[
            'method' => 'POST',
            'action' => route('pasien.store')
        ];

        $last_id = Pasien::latest()->first();

        if (!empty($last_id->id)) {

            $next_id = $last_id->id + 1;
        } else {
            $next_id = 1;
        }

        $no_rm = 'RM' . '-' . str_repeat(0, (5 - strlen($next_id))) . $next_id;

        return view('pages.backend.pasien.form', compact('config', 'no_rm'));
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
            'nomor_rm' => 'required',
            'nama' => 'required',
            'no_tlp' => 'required',
            'alamat' => 'required',
            'jenis_kelamin' => 'required',
        ]);
        if ($validator->passes()) {
            DB::beginTransaction();
            try {

                Pasien::create([
                    'nomor_rm' => $request['nomor_rm'],
                    'nama' => $request['nama'],
                    'no_tlp' => $request['no_tlp'],
                    'alamat' => $request['alamat'],
                    'jenis_kelamin' => $request['jenis_kelamin'],
                ]);

                DB::commit();
                $response = response()->json($this->responseStore(true, NULL, route('pasien.index')));
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
     * @param  \App\Models\Pasien  $pasien
     * @return \Illuminate\Http\Response
     */
    public function show(Pasien $pasien)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Pasien  $pasien
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $config['title'] = "Edit Pasien";
        $config['breadcrumbs'] = [
            ['url' => route('pasien.index'), 'title' => "Pasien"],
            ['url' => '#', 'title' => "Edit Dokter"],
        ];
        $data = Pasien::find($id);
        $config['form'] = (object)[
            'method' => 'PUT',
            'action' => route('pasien.update', $id)
        ];
        return view('pages.backend.pasien.form', compact('config', 'data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Pasien  $pasien
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'nomor_rm' => 'required',
            'nama' => 'required',
            'no_tlp' => 'required',
            'alamat' => 'required',
            'jenis_kelamin' => 'required',
        ]);
        if ($validator->passes()) {
            DB::beginTransaction();
            try {

                $pasien = Pasien::findOrFail($id);

                $pasien->update([
                    'nomor_rm' => $request['nomor_rm'],
                    'nama' => $request['nama'],
                    'no_tlp' => $request['no_tlp'],
                    'alamat' => $request['alamat'],
                    'jenis_kelamin' => $request['jenis_kelamin'],
                ]);

                DB::commit();
                $response = response()->json($this->responseStore(true, NULL, route('pasien.index')));
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
     * @param  \App\Models\Pasien  $pasien
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $response = response()->json([
            'status' => 'error',
            'message' => 'Data gagal dihapus'
        ]);
        $data = Pasien::find($id);
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
}
