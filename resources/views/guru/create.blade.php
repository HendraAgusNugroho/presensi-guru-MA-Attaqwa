@extends('layouts.app')
@section('title','Tambah Guru')

@section('content')
<div class="page-header">
    <h1>Tambah Guru Baru</h1>
    <p>Isi data guru dan informasi fingerprint/QR Code</p>
</div>

<div class="card" style="max-width:700px">
    <form method="POST" action="{{ route('guru.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="form-row">
            <div class="form-group">
                <label>ID <span style="color:red">*</span></label>
                <input type="text" name="id_pengguna" class="form-control" value="{{ old('id_pengguna') }}" placeholder="GR-001" required>
            </div>
            <div class="form-group">
                <label>Nama Lengkap <span style="color:red">*</span></label>
                <input type="text" name="nama" class="form-control" value="{{ old('nama') }}" placeholder="Fithriyani Shofwa, S.Pd." required>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="guru@sekolah.sch.id">
            </div>
            <div class="form-group">
                <label>No. HP</label>
                <input type="text" name="no_hp" class="form-control" value="{{ old('no_hp') }}" placeholder="08xxxxxxxxxx">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Jabatan</label>
                <input type="text" name="jabatan" class="form-control" value="{{ old('jabatan') }}" placeholder="Guru / Kepala Sekolah">
            </div>
            <div class="form-group">
                <label>Mata Pelajaran</label>
                <input type="text" name="mata_pelajaran" class="form-control" value="{{ old('mata_pelajaran') }}" placeholder="Matematika">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>ID Fingerprint</label>
                <input type="text" name="id_fingerprint" class="form-control" value="{{ old('id_fingerprint') }}" placeholder="FP001">
                <small style="color:#94a3b8">ID yang terdaftar di mesin fingerprint</small>
            </div>
            <div class="form-group">
                <label>Jenis Kelamin</label>
                <select name="jenis_kelamin" class="form-control" required>
                    <option value="L" @selected(old('jenis_kelamin')=='L')>Laki-laki</option>
                    <option value="P" @selected(old('jenis_kelamin')=='P')>Perempuan</option>
                </select>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Status</label>
                <select name="status" class="form-control" required>
                    <option value="aktif">Aktif</option>
                    <option value="nonaktif">Non-Aktif</option>
                </select>
            </div>
            <div class="form-group">
                <label>Foto Guru</label>
                <input type="file" name="foto" class="form-control" accept="image/*">
            </div>
        </div>
        <div style="display:flex;gap:12px;margin-top:8px">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan</button>
            <a href="{{ route('guru.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
        </div>
    </form>
</div>
@endsection
