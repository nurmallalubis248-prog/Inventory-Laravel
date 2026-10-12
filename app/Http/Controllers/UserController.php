<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        return $this->page($request);
    }

    public function edit(Request $request, User $user)
    {
        return $this->page($request, $user);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'username' => ['required', 'string', 'max:50', 'unique:users,username'],
            'password' => ['required', 'string', 'min:6'],
            'role'     => ['required', Rule::in(['admin', 'staff'])],
        ]);

        User::create([
            'name'     => $data['username'],
            'username' => $data['username'],
            'password' => $data['password'], // di-hash otomatis oleh cast "hashed"
            'role'     => $data['role'],
        ]);

        return redirect()->route('users.index')->with('success', 'Pengguna berhasil disimpan.');
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'username' => ['required', 'string', 'max:50', Rule::unique('users', 'username')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:6'],
            'role'     => ['required', Rule::in(['admin', 'staff'])],
        ]);

        $payload = [
            'name'     => $data['username'],
            'username' => $data['username'],
            'role'     => $data['role'],
        ];

        if (! empty($data['password'])) {
            $payload['password'] = $data['password'];
        }

        $user->update($payload);

        return redirect()->route('users.index')->with('success', 'Pengguna berhasil diperbarui.');
    }

    public function destroy(Request $request, User $user)
    {
        if ($user->id === $request->user()->id) {
            return redirect()->route('users.index')->with('error', 'Anda tidak bisa menghapus akun sendiri.');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'Pengguna berhasil dihapus.');
    }

    private function page(Request $request, ?User $editing = null)
    {
        $users = User::query()
            ->when($request->search, fn ($q, $s) => $q->where('username', 'like', "%{$s}%"))
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('users.index', compact('users', 'editing'));
    }
}
