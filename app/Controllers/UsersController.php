<?php

namespace App\Controllers;

use App\Models\UserModel;

class UsersController extends BaseController
{
    private UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function index()
    {
        return view('users/index', [
            'title' => 'Kelola Pengguna',
            'users' => $this->userModel->getAllUsers(),
        ]);
    }

    public function create()
    {
        return view('users/form', [
            'title' => 'Tambah Pengguna',
            'user' => null,
            'formAction' => '/admin/users/store',
        ]);
    }

    public function store()
    {
        $post = $this->request->getPost();

        $errors = $this->validateUser($post);

        if ($this->userModel->usernameExists(trim((string) ($post['username'] ?? '')))) {
            $errors['username'] = 'Username sudah digunakan.';
        }

        if ($errors !== []) {
            return redirect()->back()->withInput()->with('errors', $errors);
        }

        $this->userModel->insert([
            'full_name' => trim((string) $post['full_name']),
            'username' => trim((string) $post['username']),
            'password_hash' => password_hash((string) $post['password'], PASSWORD_DEFAULT),
            'role' => (string) $post['role'],
            'is_active' => (int) ($post['is_active'] ?? 0),
        ]);

        return redirect()->to('/admin/users')->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        $user = $this->userModel->find($id);

        if ($user === null) {
            return redirect()->to('/admin/users')->with('error', 'Data pengguna tidak ditemukan.');
        }

        return view('users/form', [
            'title' => 'Edit Pengguna',
            'user' => $user,
            'formAction' => '/admin/users/update/' . $id,
        ]);
    }

    public function update(int $id)
    {
        $user = $this->userModel->find($id);

        if ($user === null) {
            return redirect()->to('/admin/users')->with('error', 'Data pengguna tidak ditemukan.');
        }

        $post = $this->request->getPost();
        $errors = $this->validateUser($post, false);

        if ($this->userModel->usernameExists(trim((string) ($post['username'] ?? '')), $id)) {
            $errors['username'] = 'Username sudah digunakan.';
        }

        $newRole = (string) ($post['role'] ?? $user['role']);
        $newIsActive = (int) ($post['is_active'] ?? 0);

        if ($user['role'] === 'admin' && ($newRole !== 'admin' || $newIsActive !== 1) && $this->userModel->countActiveAdmins($id) < 1) {
            $errors['role'] = 'Perubahan ini akan membuat sistem tanpa admin aktif.';
        }

        if ($errors !== []) {
            return redirect()->back()->withInput()->with('errors', $errors);
        }

        $payload = [
            'full_name' => trim((string) $post['full_name']),
            'username' => trim((string) $post['username']),
            'role' => $newRole,
            'is_active' => $newIsActive,
        ];

        if (trim((string) ($post['password'] ?? '')) !== '') {
            $payload['password_hash'] = password_hash((string) $post['password'], PASSWORD_DEFAULT);
        }

        $this->userModel->update($id, $payload);

        if ((int) current_user_id() === $id) {
            $this->session->set([
                'full_name' => $payload['full_name'],
                'username' => $payload['username'],
                'role' => $payload['role'],
            ]);
        }

        return redirect()->to('/admin/users')->with('success', 'Pengguna berhasil diperbarui.');
    }

    public function delete(int $id)
    {
        $user = $this->userModel->find($id);

        if ($user === null) {
            return redirect()->to('/admin/users')->with('error', 'Data pengguna tidak ditemukan.');
        }

        $isSelf = current_user_id() === $id;

        if ($user['role'] === 'admin' && $this->userModel->countActiveAdmins($id) < 1) {
            return redirect()->to('/admin/users')->with('error', 'Pengguna admin terakhir tidak dapat dihapus.');
        }

        $this->userModel->delete($id);

        if ($isSelf) {
            $this->session->destroy();

            return redirect()->to('/login')->with('success', 'Akun Anda telah dihapus.');
        }

        return redirect()->to('/admin/users')->with('success', 'Pengguna berhasil dihapus.');
    }

    private function validateUser(array $post, bool $isCreate = true): array
    {
        $rules = [
            'full_name' => 'required|max_length[150]',
            'username' => 'required|min_length[3]|max_length[50]|alpha_numeric_punct',
            'role' => 'required|in_list[admin,pegawai]',
            'is_active' => 'permit_empty|in_list[0,1]',
        ];

        if ($isCreate || trim((string) ($post['password'] ?? '')) !== '') {
            $rules['password'] = 'required|min_length[8]';
            $rules['password_confirmation'] = 'required|matches[password]';
        }

        if (!$this->validateData($post, $rules)) {
            return $this->validator->getErrors();
        }

        return [];
    }
}
