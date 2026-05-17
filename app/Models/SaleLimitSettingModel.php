<?php

namespace App\Models;

use CodeIgniter\Model;

class SaleLimitSettingModel extends Model
{
    protected $table = 'sale_limit_settings';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = [
        'is_enabled',
        'max_total_kg',
        'updated_by',
    ];

    public function getCurrentSetting(): ?array
    {
        return $this->orderBy('id', 'DESC')->first();
    }

    public function saveSetting(bool $isEnabled, int|float $maxTotalKg, ?int $updatedBy): void
    {
        $current = $this->getCurrentSetting();
        $payload = [
            'is_enabled' => $isEnabled ? 1 : 0,
            'max_total_kg' => (int) $maxTotalKg,
            'updated_by' => $updatedBy,
        ];

        if ($current === null) {
            $this->insert($payload);

            return;
        }

        $this->update((int) $current['id'], $payload);
    }
}
