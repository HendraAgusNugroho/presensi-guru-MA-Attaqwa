@extends('layouts.app')
@section('title','Edit Guru')

@section('content')
<div class="page-header">
    <h1>Edit Data Guru</h1>
    <p>{{ $guru->nama }}</p>
</div>

<div class="card" style="max-width:700px">
    <form method="POST" action="{{ route('guru.update', $guru) }}" enctype="multipart/form-data">
        @csrf @method('PUT')
        <div class="form-row">
            <div class="form-group">
                <label>ID <span style="color:red">*</span></label>
                <input type="text" name="id_pengguna" class="form-control" value="{{ old('id_pengguna', $guru->id_pengguna) }}" required>
            </div>
            <div class="form-group">
                <label>Nama Lengkap <span style="color:red">*</span></label>
                <input type="text" name="nama" class="form-control" value="{{ old('nama', $guru->nama) }}" required>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control" value="{{ old('email', $guru->email) }}">
            </div>
            <div class="form-group">
                <label>No. HP</label>
                <input type="text" name="no_hp" class="form-control" value="{{ old('no_hp', $guru->no_hp) }}">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Jabatan</label>
                <input type="text" name="jabatan" class="form-control" value="{{ old('jabatan', $guru->jabatan) }}">
            </div>
            <div class="form-group">
                <label>Mata Pelajaran</label>
                <input type="text" name="mata_pelajaran" class="form-control" value="{{ old('mata_pelajaran', $guru->mata_pelajaran) }}">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>ID Fingerprint</label>
                <input type="text" name="id_fingerprint" class="form-control" value="{{ old('id_fingerprint', $guru->id_fingerprint) }}" placeholder="FP001">
            </div>
            <div class="form-group">
                <label>Jenis Kelamin</label>
                <select name="jenis_kelamin" class="form-control">
                    <option value="L" @selected(old('jenis_kelamin',$guru->jenis_kelamin)=='L')>Laki-laki</option>
                    <option value="P" @selected(old('jenis_kelamin',$guru->jenis_kelamin)=='P')>Perempuan</option>
                </select>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Status</label>
                <select name="status" class="form-control">
                    <option value="aktif" @selected($guru->status=='aktif')>Aktif</option>
                    <option value="nonaktif" @selected($guru->status=='nonaktif')>Non-Aktif</option>
                </select>
            </div>
            <div class="form-group">
                <label>Foto Baru (opsional)</label>
                <input type="file" name="foto" class="form-control" accept="image/*">
            </div>
        </div>
        <div style="display:flex;gap:12px;margin-top:8px">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update</button>
            <a href="{{ route('guru.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
        </div>
    </form>
</div>
@endsection
