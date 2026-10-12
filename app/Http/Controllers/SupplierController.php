<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        return $this->page($request);
    }

    public function edit(Request $request, Supplier $supplier)
    {
        return $this->page($request, $supplier);
    }

    public function store(Request $request)
    {
        Supplier::create($this->rules($request));

        return redirect()->route('suppliers.index')->with('success', 'Supplier berhasil disimpan.');
    }

    public function update(Request $request, Supplier $supplier)
    {
        $supplier->update($this->rules($request));

        return redirect()->route('suppliers.index')->with('success', 'Supplier berhasil diperbarui.');
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();

        return redirect()->route('suppliers.index')->with('success', 'Supplier berhasil dihapus.');
    }

    private function rules(Request $request): array
    {
        return $request->validate([
            'name'    => ['required', 'string', 'max:100'],
            'phone'   => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
        ]);
    }

    private function page(Request $request, ?Supplier $editing = null)
    {
        $suppliers = Supplier::query()
            ->when($request->search, fn ($q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        return view('suppliers.index', compact('suppliers', 'editing'));
    }
}
