<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SuratMasuk\SuratMasuk as SuratMasukSuratMasuk;
use App\Models\Disposisi;
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
                if ($user->id_role == 37 ) {
                    $data = SuratMasuk::Fillters($request,$jenis)
                    ->where('status',1)
                    ->OrderBy('created_at','desc')
                    ->paginate(15);
                } else if ($user->id_role == 38 || $user->id_role == 39 ) {
                    $id_surat = \Auth::user()->disposisi()->pluck('id_surat');
                    $data = SuratMasuk::Fillters($request,$jenis)
                    ->whereIn('id',$id_surat)
                    ->OrderBy('created_at','desc')
                    ->paginate(15);
                }else {
                    $data = SuratMasuk::Fillters($request,$jenis)
                    ->OrderBy('created_at','desc')
                    ->paginate(15);
                }

            } else {
                if ($user->id_role == 37) {
                    $data = SuratMasuk::where('jenis_surat',$jenis->id)->where('status',1)->OrderBy('created_at','desc')
                ->paginate(15);
                }
                else if ($user->id_role == 38 || $user->id_role == 39 ) {
                    $id_surat = \Auth::user()->disposisi()->pluck('id_surat');
                    $data = SuratMasuk::where('jenis_surat',$jenis->id)->whereIn('id',$id_surat)
                    ->OrderBy('created_at','desc')
                    ->paginate(15);
                }
                 else {
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

    public function disposisi(Request $request,$surat,$jenis){
        $user = \Auth::user();

        if ($request->isMethod('post'))
        {
            $id_role = $user->id_role == 38 ? 39 : 37;
            Disposisi::with('user')->where('id_surat',$request->id_surat)
            ->whereHas('user' , function($q) use($id_role,$user){
                if ($id_role == 39) {
                    $q->where('id_role',$id_role)->where('id_kasi',$user->id);
                }else {
                    $q->where('id_role','!=',$id_role);
                }
            })->delete();
            $value_disposisi = json_decode($request->value_disposisi);
            for ($i=0; $i < count($value_disposisi) ; $i++) {
               $data = new Disposisi;
               $data->isi_disposisi = $request->isi_disposisi;
               $data->tgl_disposisi = $request->tgl_disposisi;
               $data->id_user = $value_disposisi[$i];
               $data->id_surat = $request->id_surat;
               $data->save();
            }
        } else {
            if ($user->id_role == 38) {
                $users = User::where('id_role',39)->where('id_kasi',$user->id)->get();
            } else {
                $users = User::where('id_role',38)->get();
            }

            $disposisi = Disposisi::with('user')->where('id_surat',$request->id_surat)
                        ->whereHas('user' , function($q) use($user){
                            if ($user->id_role == 38) {
                                $q->where('id_role',39)->where('id_kasi',$user->id);
                            } else {
                                $q->where('id_role','!=',37);
                            }

                        })->get();
            return $data = [
                'users' => $users,
                'disposisi' => $disposisi,
                'user_disposisi' => $disposisi->pluck('id_user')
            ];
        }
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
           $surat_masuk->id_sekertaris = \Auth::user()->id;
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
        $user = \Auth::user();
        $surat = SuratMasuk::findOrFail($id);
        $disposisi =Disposisi::with('user')->where('id_surat',$id)
        ->whereHas('user' , function($q) use($user){
            if ($user->id_role == 38 ) {
                $q->where('id_role',39)->where('id_kasi',$user->id);
            } else if($user->id_role == 39){
                $q->where('id_role',39)->where('id_kasi',$user->id_kasi);
            } else {
                $q->where('id_role','!=',37);
            }

        })->get();
        $isi_disposisi = $disposisi !== [] ? $disposisi->first()->isi_disposisi : '';
        $tgl_disposisi = $disposisi !== [] ? $disposisi->first()->tgl_disposisi->format('Y-m-d') : '';

        return [
            'surat' => new SuratMasukSuratMasuk($surat),
            'disposisi' => $disposisi,
            'isi_disposisi' => $isi_disposisi,
            'tgl_disposisi' => $tgl_disposisi,
        ];
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($surat,$jenis,$id)
    {
        if ($surat == 'surat-masuk') {
            $data = SuratMasuk::findOrFail($id);
            return new SuratMasukSuratMasuk($data);
        }
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
        if ($surat == 'surat-masuk') {
           $surat_masuk = SuratMasuk::findOrFail($id);
           $surat_masuk->tgl_terima = $request->tgl_terima;
           $surat_masuk->nomor_surat = $request->nomor_surat;
           $surat_masuk->asal_surat = $request->asal_surat;
           $surat_masuk->tgl_surat = $request->tgl_surat;
           $surat_masuk->perioritas = $request->perioritas;
           $surat_masuk->perihal = $request->perihal;
           $surat_masuk->tindak_lanjut = $request->tindak_lanjut == 'true' ? 1 : 0;
           if ($request->file('file_surat')) {
                $file = $request->file('file_surat')->store('file_surat','public');
                $surat_masuk->file_surat = $file;
           }
           $surat_masuk->save();
        }
        return response()->json([
            'message' => 'berhasil Edit surat masuk'
        ],200);
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
