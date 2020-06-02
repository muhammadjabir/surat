<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\JenisSurat\JenisSuratCollection;
use App\Models\ChildMenu;
use App\Models\JenisSurat;
use App\Models\RoleMenu;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use DB;

class JenisSuratController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->keyword) {

            $data = JenisSurat::where('nama_surat',"LIKE","%$request->keyword%")
            ->orWhere('inisial',"LIKE","%$request->keyword%")
            ->orWhere('slug',"LIKE","%$request->keyword%")
            ->orderBy('created_at','desc')->paginate(15);
        }else{
            $data = JenisSurat::orderBy('created_at','desc')->paginate(15);
        }
        return new JenisSuratCollection($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->request->add(['slug' => Str::of($request->nama_surat)->slug('-')]);
        $validator = \Validator::make($request->all(), [
            'nama_surat' => 'required',
            'slug'=>'required|unique:jenis_surat,slug',
            'inisial'=>'required'
        ],[
            '*.unique' => 'Jenis Surat Sudah Tersedia'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first()
            ],400);
        }

        DB::beginTransaction();
        $error = 0 ;
        try{
            $jenis_surat = new JenisSurat;
            $jenis_surat->nama_surat = $request->nama_surat;
            $jenis_surat->slug = $request->slug;
            $jenis_surat->inisial = $request->inisial;
            if ($jenis_surat->save()) {
                $data_menu = [
                ];
                $child_menu = new ChildMenu;
                $child_menu->url = "/surat-masuk/$request->slug";
                $child_menu->description = "$request->nama_surat ($request->inisial)";
                $child_menu->id_menu = 18;
                $child_menu->icon = 'far fa-envelope';

                if ($child_menu->save()) {
                    array_push($data_menu,$child_menu->id);
                } else {
                    $error++;
                    throw new \Exception('Gagal Tambah Child Menu 1');
                }
                $child_menu = new ChildMenu;
                $child_menu->url = "/surat-keluar/$request->slug";
                $child_menu->description = "$request->nama_surat ($request->inisial)";
                $child_menu->id_menu = 19;
                $child_menu->icon = 'far fa-envelope';
                if ($child_menu->save()) {
                    array_push($data_menu,$child_menu->id);
                } else {
                    $error++;
                    throw new \Exception('Gagal Tambah Child Menu 2');
                }

                if (count($data_menu) > 1) {
                    $role_menus = RoleMenu::select('id')->pluck('id');
                    for ($i=0; $i < count($role_menus); $i++) {
                        $role_menu = RoleMenu::find($role_menus[$i]);
                        $child=json_decode($role_menu->child_menu);
                        $child = array_merge($child,$data_menu);
                        $role_menu->child_menu= json_encode($child);
                        if (!$role_menu->save()) {
                            $error++;
                            throw new \Exception('Gagal Edit Role Menu');
                            break;
                        }
                    }
                } else {
                    $error++;
                    throw new \Exception('Gagal Tambah Child Menu');
                }
            } else{
                $error++;
                throw new \Exception('Gagal Tambah Jenis Surat');
            }

            if ($error === 0) {
                DB::commit();
                $message = "Berhasil Tambah Jenis Surat";
                $status = 200;
            }
        }
        catch (\Exception $e) {
            DB::rollback();
            $status = 500;
            $message = $e->getMessage();
        }
        return response()->json([
            'message'=> $message
        ],$status);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
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
    public function update(Request $request, $id)
    {
        $request->request->add(['slug' => Str::of($request->nama_surat)->slug('-')]);
        $validator = \Validator::make($request->all(), [
            'nama_surat' => 'required',
            'slug'=>'required|unique:jenis_surat,slug,'.$id,
            'inisial'=>'required'
        ],[
            '*.unique' => 'Jenis Surat Sudah Tersedia'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first()
            ],400);
        }
        $error = 0;
        DB::beginTransaction();
        try {
            $jenis_surat = JenisSurat::findOrFail($id);

            $child_menu = ChildMenu::where('description','LIKE',"%$jenis_surat->nama_surat%")->get();

            $jenis_surat->nama_surat = $request->nama_surat;
            $jenis_surat->slug = $request->slug;
            $jenis_surat->inisial = $request->inisial;
            if($jenis_surat->save()){
                foreach ($child_menu as $value) {
                    $url = explode('/',$value->url);
                    $url = $url[1] == 'surat-masuk' ? "/surat-masuk/$request->slug" : "/surat-keluar/$request->slug" ;
                    $menu = ChildMenu::findOrFail($value->id);
                    $menu->url = $url;
                    $menu->description = "$request->nama_surat ($request->inisial)";
                    if (!$menu->save()) {
                        $error++;
                        throw new \Exception('Gagal Ubah child menu');
                        break;
                    }

                }
            } else {
                $error++;
                throw new \Exception('Gagal Ubah Jenis surat');
            }
            if ($error === 0) {
                DB::commit();
                $message = "Berhasil Ubah Jenis Surat";
                $status = 200;
            }

        } catch (\Exception $e) {
            DB::rollback();
            $status = 500;
            // $message = ['detail' => $e->getMessage(),
            //             'line' => $e->getLine(),
            //             'file' => $e->getFile(),
            //             'code' =>$e->getCode(),
            //             'trace' => $e->getTrace()
            //             ];
            $message = $e->getMessage();
        }

        return response()->json([
            'message'=> $message
        ],$status);

    }

    public function edit($id)
    {
        $data = JenisSurat::findOrFail($id);
        return $data;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
