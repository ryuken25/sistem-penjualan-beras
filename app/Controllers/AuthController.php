<?php

namespace App\Controllers;

use App\Models\UserModel;

class AuthController extends BaseController
{
    private ?UserModel $userModel = null;

    public function index()
    {
        if ($this->session->get('isLoggedIn')) {
            return redirect()->to('/dashboard');
        }

        return view('auth/login', [
            'title' => 'Login Sistem Penjualan Beras',
        ]);
    }

    public function attempt()
    {
        $rules = [
            'username' => 'required|max_length[50]',
            'password' => 'required|min_length[8]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $username = trim((string) $this->request->getPost('username'));
        $password = (string) $this->request->getPost('password');
        $user = $this->getUserModel()->where('username', $username)->first();

        if ($user === null || !password_verify($password, $user['password_hash'])) {
            return redirect()->back()->withInput()->with('error', 'Username atau password tidak valid.');
        }

        if ((int) $user['is_active'] !== 1) {
            return redirect()->back()->withInput()->with('error', 'Akun tidak aktif. Hubungi admin.');
        }

        $this->session->set([
            'user_id' => (int) $user['id'],
            'full_name' => $user['full_name'],
            'username' => $user['username'],
            'profile_photo' => $user['profile_photo'] ?? null,
            'role' => $user['role'],
            'isLoggedIn' => true,
        ]);

        return redirect()->to('/dashboard')->with('success', 'Login berhasil.');
    }

    public function logout()
    {
        $this->session->destroy();

        return redirect()->to('/login')->with('success', 'Logout berhasil.');
    }

    private function getUserModel(): UserModel
    {
        if ($this->userModel === null) {
            $this->userModel = new UserModel();
        }

        return $this->userModel;
    }
}
