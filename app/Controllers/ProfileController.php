<?php

namespace App\Controllers;

use App\Models\UserModel;

class ProfileController extends BaseController
{
    private UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function index()
    {
        $user = $this->userModel->find((int) current_user_id());

        if ($user === null) {
            $this->session->destroy();

            return redirect()->to('/login')->with('error', 'Sesi tidak valid. Silakan login kembali.');
        }

        return view('profile/index', [
            'title' => 'Profil Saya',
            'user' => $user,
        ]);
    }

    public function update()
    {
        $userId = (int) current_user_id();
        $user = $this->userModel->find($userId);

        if ($user === null) {
            $this->session->destroy();

            return redirect()->to('/login')->with('error', 'Sesi tidak valid. Silakan login kembali.');
        }

        $post = $this->request->getPost();
        $rules = [
            'full_name' => 'required|max_length[150]',
            'username' => 'required|min_length[3]|max_length[50]|alpha_numeric_punct',
        ];

        $photoFile = $this->request->getFile('profile_photo');
        if ($photoFile !== null && $photoFile->isValid() && $photoFile->getName() !== '') {
            $rules['profile_photo'] = 'uploaded[profile_photo]|is_image[profile_photo]|mime_in[profile_photo,image/png,image/jpg,image/jpeg,image/webp]|max_size[profile_photo,2048]';
        }

        if (trim((string) ($post['new_password'] ?? '')) !== '') {
            $rules['current_password'] = 'required';
            $rules['new_password'] = 'required|min_length[8]';
            $rules['confirm_new_password'] = 'required|matches[new_password]';
        }

        if (!$this->validateData($post, $rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        if ($this->userModel->usernameExists(trim((string) $post['username']), $userId)) {
            return redirect()->back()->withInput()->with('error', 'Username sudah digunakan oleh pengguna lain.');
        }

        $payload = [
            'full_name' => trim((string) $post['full_name']),
            'username' => trim((string) $post['username']),
        ];

        if ($photoFile !== null && $photoFile->isValid() && $photoFile->getName() !== '') {
            $uploadDir = FCPATH . 'uploads/profile';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $newName = $photoFile->getRandomName();
            $photoFile->move($uploadDir, $newName, true);
            $payload['profile_photo'] = 'uploads/profile/' . $newName;
        }

        if (trim((string) ($post['new_password'] ?? '')) !== '') {
            if (!password_verify((string) $post['current_password'], $user['password_hash'])) {
                return redirect()->back()->withInput()->with('error', 'Password saat ini tidak sesuai.');
            }

            $payload['password_hash'] = password_hash((string) $post['new_password'], PASSWORD_DEFAULT);
        }

        $this->userModel->update($userId, $payload);
        $this->session->set([
            'full_name' => $payload['full_name'],
            'username' => $payload['username'],
        ]);

        if (isset($payload['profile_photo'])) {
            $this->session->set('profile_photo', $payload['profile_photo']);
        }

        return redirect()->to('/profile')->with('success', 'Profil berhasil diperbarui.');
    }
}
