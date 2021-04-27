<?php

namespace App\Http\Controllers;

use App\Models\Dagang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DagangController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tampilandagang = Dagang::all();
        $title = "Daftar Buah";
        return view('admin.dagang', compact('title', 'tampilandagang'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $title = "Input Buah";
        return view('admin.inputdagang', compact('title'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $message = [
            'required' => 'Kolom : attribute Harus Lengkap',
            'date' => 'Kolom : attribute Harus Tanggal',
            'numeric' => 'Kolom : attribute Harus Angka',
        ];
        $validasi = $request->validate([
            'nama' => 'required|unique:dagangs|max:255',
            'description' => 'required',
            'photo' => 'required|mimes:jpg,bmp,png|max:512'
        ], $message);
        $path = $request->file('photo')->store('photo');
        $validasi['user_id'] = Auth::id();
        $validasi['photo'] = $path;
        Dagang::create($validasi);
        return redirect('dagang')->with('success', 'Data Berhasil Tersimpan');
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
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $tampilandagang = Dagang::find($id);
        $title = "Edit Buah";
        return view('admin.inputdagang', compact('title', 'tampilandagang'));
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
        $message = [
            'required' => 'Kolom : attribute Harus Lengkap',
            'date' => 'Kolom : attribute Harus Tanggal',
            'numeric' => 'Kolom : attribute Harus Angka',
        ];
        $validasi = $request->validate([
            'nama' => 'required|unique:dagangs|max:255',
            'description' => 'required'
        ], $message);
        if ($request->hasFile('photo')) {
            $fileName = time() . $request->file('photo')->getClientOriginalName();
            $path = $request->file('photo')->storeAs('photo', $fileName);
            $validasi['photo'] = $path;
            $tampilandagang = Dagang::find($id);
            Storage::delete($tampilandagang->photo);
        }
        $validasi['user_id'] = Auth::id();
        Dagang::where('id', $id)->update($validasi);
        return redirect('dagang')->with('success', 'Data Berhasil Tersimpan');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $tampilandagang = Dagang::find($id);
        if ($tampilandagang != null) {
            Storage::delete($tampilandagang->photo);
            $tampilandagang = Dagang::find($tampilandagang->id);
            Dagang::where('id', $id)->delete();
        }
        return redirect('dagang')->with('success', 'Data Berhasil Terhapus');
    }
}
