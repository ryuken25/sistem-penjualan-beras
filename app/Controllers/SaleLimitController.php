<?php

namespace App\Controllers;

use App\Models\SaleLimitSettingModel;

class SaleLimitController extends BaseController
{
    private SaleLimitSettingModel $saleLimitSettingModel;

    public function __construct()
    {
        $this->saleLimitSettingModel = new SaleLimitSettingModel();
    }

    public function index()
    {
        return view('sale_limit/index', [
            'title' => 'Pengaturan Mode Pembatasan Penjualan',
            'setting' => $this->saleLimitSettingModel->getCurrentSetting(),
        ]);
    }

    public function update()
    {
        $post = $this->request->getPost();
        $rules = [
            'max_total_kg' => 'required|decimal|greater_than_equal_to[0]',
        ];

        if (!$this->validateData($post, $rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $isEnabled = $this->request->getPost('is_enabled') !== null;
        $maxTotal = (float) $post['max_total_kg'];

        if ($isEnabled && $maxTotal <= 0) {
            return redirect()->back()->withInput()->with('error', 'Batas kilogram harus lebih dari 0 saat mode limit aktif.');
        }

        $this->saleLimitSettingModel->saveSetting($isEnabled, $maxTotal, current_user_id());

        return redirect()->to('/admin/sale-limit')->with('success', 'Pengaturan limit penjualan berhasil diperbarui.');
    }
}
