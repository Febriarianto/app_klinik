<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Dokter;
use Illuminate\Http\Request;
use App\Traits\ResponseStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class DokterController extends Controller
{
    use ResponseStatus;

    function __construct()
    {
        $this->middleware('can:dokter-list', ['only' => ['index', 'show']]);
        $this->middleware('can:dokter-create', ['only' => ['create', 'store']]);
        $this->middleware('can:dokter-edit', ['only' => ['edit', 'update']]);
        $this->middleware('can:dokter-delete', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $config['title'] = "Dokter";
        $config['breadcrumbs'] = [
            ['url' => '#', 'title' => "Dokter"],
        ];
        if ($request->ajax()) {
            $data = Dokter::query();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $actionBtn = '<div class="btn-group dropend">
                            <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                Aksi
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="' . route('dokter.edit', $row->id) . '">Edit</a></li>
                                <li><a class="dropdown-item btn-delete" href="#" data-id ="' . $row->id . '" >Hapus</a></li>
                            </ul>
                          </div>';
                    return $actionBtn;
                })->make();
        }
        return view('pages.backend.dokter.index', compact('config'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $config['title'] = "Tambah Dokter";
        $config['breadcrumbs'] = [
            ['url' => route('dokter.index'), 'title' => "Dokter"],
            ['url' => '#', 'title' => "Tambah Dokter"],
        ];
        $config['form'] = (object)[
            'method' => 'POST',
            'action' => route('dokter.store')
        ];
        return view('pages.backend.dokter.form', compact('config'));
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
            'spesialis' => 'required',
            'alamat' => 'required',
            'no_tlp' => 'required',
        ]);
        if ($validator->passes()) {
            DB::beginTransaction();
            try {

                Dokter::create([
                    'nama' => $request['nama'],
                    'spesialis' => $request['spesialis'],
                    'alamat' => $request['alamat'],
                    'no_tlp' => $request['no_tlp'],
                ]);

                DB::commit();
                $response = response()->json($this->responseStore(true, NULL, route('dokter.index')));
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
     * @param  \App\Models\Doctor  $doctor
     * @return \Illuminate\Http\Response
     */
    public function show(Dokter $dokter)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Doctor  $doctor
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $config['title'] = "Edit Dokter";
        $config['breadcrumbs'] = [
            ['url' => route('dokter.index'), 'title' => "Dokter"],
            ['url' => '#', 'title' => "Edit Dokter"],
        ];
        $data = Dokter::find($id);
        $config['form'] = (object)[
            'method' => 'PUT',
            'action' => route('dokter.update', $id)
        ];
        return view('pages.backend.dokter.form', compact('config', 'data'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Doctor  $doctor
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'nama' => 'required',
            'spesialis' => 'required',
            'alamat' => 'required',
            'no_tlp' => 'required',
        ]);
        if ($validator->passes()) {
            DB::beginTransaction();
            try {

                $dokter = Dokter::findOrFail($id);

                $dokter->update([
                    'nama' => $request['nama'],
                    'spesialis' => $request['spesialis'],
                    'alamat' => $request['alamat'],
                    'no_tlp' => $request['no_tlp'],
                ]);

                DB::commit();
                $response = response()->json($this->responseStore(true, NULL, route('dokter.index')));
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
     * @param  \App\Models\Doctor  $doctor
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $response = response()->json([
            'status' => 'error',
            'message' => 'Data gagal dihapus'
        ]);
        $data = Dokter::find($id);
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
