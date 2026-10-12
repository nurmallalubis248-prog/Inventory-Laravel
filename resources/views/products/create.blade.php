@extends('layouts.app')

@section('content')
<div class="card">
    <h3>Tambah Produk Baru</h3>
   <form method="POST" action="{{ route('products.store') }}" novalidate>
        @csrf
        @include('products._form')
        <button type="submit" class="btn-pink">Simpan Produk</button>
        <a href="{{ route('products.index') }}" class="cancel-link">Batal</a>
    </form>
</div>
@endsection
