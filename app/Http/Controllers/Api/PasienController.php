<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pasien;
use App\Http\Resources\ApiResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PasienController extends Controller
{
    public function index()
    {
        $pasien = Pasien::latest()->get();

        return new ApiResource(true, 'Data Pasien', $pasien);
    }

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

                $pasien = Pasien::create([
                    'nomor_rm' => $request['nomor_rm'],
                    'nama' => $request['nama'],
                    'no_tlp' => $request['no_tlp'],
                    'alamat' => $request['alamat'],
                    'jenis_kelamin' => $request['jenis_kelamin'],
                ]);

                DB::commit();
                $response = new ApiResource(true, 'Data Pasien Berhasil Ditambahkan!', $pasien);
            } catch (\Throwable $throw) {
                Log::error($throw);
                DB::rollBack();
                $response = response()->json($throw->getMessage(), 422);
            }
        } else {
            $response = response()->json($validator->errors(), 422);
        }
        return $response;
    }

    public function show($id)
    {
        $data = Pasien::find($id);
        if ($data == null) {
            $response = new ApiResource(false, 'Data Pasien Tidak ada', $data);
        } else {
            $response = new ApiResource(true, 'Data Pasien Ditemukan', $data);
        }

        return $response;
    }
}
