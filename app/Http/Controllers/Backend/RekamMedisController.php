<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\RekamMedis;
use Illuminate\Http\Request;
use App\Traits\ResponseStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Carbon;
use App\Models\Perawatan;

class RekamMedisController extends Controller
{
    use ResponseStatus;

    function __construct()
    {
        $this->middleware('can:rekam-medis-list', ['only' => ['index', 'show']]);
        $this->middleware('can:rekam-medis-create', ['only' => ['create', 'store']]);
        $this->middleware('can:rekam-medis-edit', ['only' => ['edit', 'update']]);
        $this->middleware('can:rekam-medis-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $config['title'] = "Rekam Medis";
        $config['breadcrumbs'] = [
            ['url' => '#', 'title' => "Rekam Medis"],
        ];
        if ($request->ajax()) {
            $data = RekamMedis::with(['pasien', 'dokter'])->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('tgl', function ($row) {
                    $tgl = Carbon::parse($row->created_at)->format('d-m-Y');
                    return $tgl;
                })
                ->addColumn('action', function ($row) {
                    $actionBtn = '<div class="btn-group dropend">
                            <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                Aksi
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="' . route('rekam-medis.edit', $row->id) . '">Edit</a></li>
                                <li><a class="dropdown-item btn-delete" href="#" data-id ="' . $row->id . '" >Hapus</a></li>
                            </ul>
                          </div>';
                    return $actionBtn;
                })->make();
        }
        return view('pages.backend.rekammedis.index', compact('config'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $config['title'] = "Tambah Rekam Medis";
        $config['breadcrumbs'] = [
            ['url' => route('rekam-medis.index'), 'title' => "Rekam Medis"],
            ['url' => '#', 'title' => "Tambah Rekam Medis"],
        ];
        $config['form'] = (object)[
            'method' => 'POST',
            'action' => route('rekam-medis.store')
        ];
        return view('pages.backend.rekammedis.form', compact('config'));
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
            'pasien_id' => 'required',
            'keluhan' => 'required',
            'dokter_id' => 'required',
            'diagnosa' => 'required',
            'perawatan' => 'required',
        ]);

        $perawatan = json_encode($request['perawatan']);

        if ($validator->passes()) {
            DB::beginTransaction();
            try {

                RekamMedis::create([
                    'pasien_id' => $request['pasien_id'],
                    'keluhan' => $request['keluhan'],
                    'dokter_id' => $request['dokter_id'],
                    'diagnosa' => $request['diagnosa'],
                    'perawatan' => $perawatan,
                ]);

                DB::commit();
                $response = response()->json($this->responseStore(true, NULL, route('rekam-medis.index')));
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
     * @param  \App\Models\RekamMedis  $rekamMedis
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\RekamMedis  $rekamMedis
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $config['title'] = "Edit Rekam Medis";
        $config['breadcrumbs'] = [
            ['url' => route('rekam-medis.index'), 'title' => "Rekam Medis"],
            ['url' => '#', 'title' => "Edit Rekam Medis"],
        ];
        $data = RekamMedis::with(['pasien', 'dokter'])->where('id', $id)->first();

        $json = json_decode($data->perawatan);


        foreach ($json as $p) {
            $query = Perawatan::findOrFail($p);
            $perawatan[] = [
                'id' => $query->id,
                'nama' => $query->nama,
            ];
        }
        // dd($perawatan['1']['nama']);
        $config['form'] = (object)[
            'method' => 'PUT',
            'action' => route('rekam-medis.update', $id)
        ];
        return view('pages.backend.rekammedis.form', compact('config', 'data', 'perawatan'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\RekamMedis  $rekamMedis
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'pasien_id' => 'required',
            'keluhan' => 'required',
            'dokter_id' => 'required',
            'diagnosa' => 'required',
            'perawatan' => 'required',
        ]);

        $perawatan = json_encode($request['perawatan']);

        if ($validator->passes()) {
            DB::beginTransaction();
            try {

                $rekam = RekamMedis::findOrFail($id);
                $rekam->update([
                    'pasien_id' => $request['pasien_id'],
                    'keluhan' => $request['keluhan'],
                    'dokter_id' => $request['dokter_id'],
                    'diagnosa' => $request['diagnosa'],
                    'perawatan' => $perawatan,
                ]);

                DB::commit();
                $response = response()->json($this->responseStore(true, NULL, route('rekam-medis.index')));
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
     * @param  \App\Models\RekamMedis  $rekamMedis
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $response = response()->json([
            'status' => 'error',
            'message' => 'Data gagal dihapus'
        ]);
        $data = RekamMedis::find($id);
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
