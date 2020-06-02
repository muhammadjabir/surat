<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SuratMasuk\SuratMasuk as SuratMasukSuratMasuk;
use App\Models\JenisSurat;
use App\Models\SuratMasuk;
use App\User;
use Illuminate\Http\Request;
use DB;

class SuratController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request,$surat,$jenis)
    {
        $user = \Auth::user();
        $jenis = JenisSurat::where('slug',$jenis)->first();
        if ($surat == 'surat-masuk') {
            if ($request->keyword) {
                if ($user->role->description == 'Kajari') {
                    $data = SuratMasuk::Fillters($request,$jenis)
                    ->where('status',1)
                    ->OrderBy('created_at','desc')
                    ->paginate(15);
                } else {
                    $data = SuratMasuk::Fillters($request,$jenis)
                    ->OrderBy('created_at','desc')
                    ->paginate(15);
                }

            } else {
                if ($user->role->description == 'Kajari') {
                    $data = SuratMasuk::where('jenis_surat',$jenis->id)->where('status',1)->OrderBy('created_at','desc')
                ->paginate(15);
                } else {
                    $data = SuratMasuk::where('jenis_surat',$jenis->id)->OrderBy('created_at','desc')
                ->paginate(15);
                }

            }
        }
        return SuratMasukSuratMasuk::collection($data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($surat,$jenis)
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,$surat,$jenis)
    {
        $jenis = JenisSurat::where('slug',$jenis)->first();
        if ($surat == 'surat-masuk') {
           $surat_masuk = new SuratMasuk;
           $surat_masuk->tgl_terima = $request->tgl_terima;
           $surat_masuk->nomor_surat = $request->nomor_surat;
           $surat_masuk->asal_surat = $request->asal_surat;
           $surat_masuk->tgl_surat = $request->tgl_surat;
           $surat_masuk->perioritas = $request->perioritas;
           $surat_masuk->perihal = $request->perihal;
           $surat_masuk->tindak_lanjut = $request->tindak_lanjut == 'true' ? 1 : 0;
           $surat_masuk->jenis_surat = $jenis->id;
           if ($request->file('file_surat')) {
                $file = $request->file('file_surat')->store('file_surat','public');
                $surat_masuk->file_surat = $file;
           }
           $surat_masuk->save();
        }
        return response()->json([
            'message' => 'berhasil tambah surat masuk'
        ],200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($surat,$jenis,$id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($surat,$jenis,$id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$surat,$jenis, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function teruskan(Request $request, $surat,$jenis)
    {
        $users = User::where('id_role',37)->get();
        $error = 0;
        $message = '';
        DB::beginTransaction();
        try {
            if ($surat == 'surat-masuk') {
                $surat = SuratMasuk::findOrFail($request->id);

            }
            $surat->status = 1;
            $surat->tgl_kirim = \Carbon\Carbon::now();
            $surat->id_sekertaris = \Auth::user()->id;
            $surat->save();
            $data = [];
            foreach($users as $user){
                $disposisi = [
                    'id_user' => $user->id,
                    'id_surat' => $surat->id
                ];
               $data = array_merge($data,$disposisi);
            }
            $datas = DB::table('disposisi')->insert($data);
            if (!$datas) {
                $error++;
                throw new \Exception('Gagal Tambah Disposisi Kejari');
            }

            if ($error === 0) {
                DB::commit();
                $status = 200;
               $message = 'Berhasil Diteruskan';
            }
        } catch (\Exception $e) {
            DB::rollback();
            $status = 500;
            $message = $e->getMessage();
        }

        return response()->json([
            'message'=> $message,
            'surat' => new SuratMasukSuratMasuk($surat)
        ],$status);
    }
}
