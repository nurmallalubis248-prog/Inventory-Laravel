@extends('layouts.app')

@section('content')
<div class="card">
    <h3>Edit Produk</h3>
    <form method="POST" action="{{ route('products.update', $product) }}" novalidate>
        @csrf
        @method('PUT')
        @include('products._form')
        <button type="submit" class="btn-pink">Perbarui Produk</button>
        <a href="{{ route('products.index') }}" class="cancel-link">Batal</a>
    </form>
</div>
@endsection
