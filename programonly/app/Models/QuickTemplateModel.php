<?php

namespace App\Models;

use CodeIgniter\Model;

class QuickTemplateModel extends Model
{
    protected $table = 'quick_templates';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $useTimestamps = true;
    protected $allowedFields = [
        'template_code',
        'template_name',
        'qty_5kg',
        'qty_10kg',
        'qty_25kg',
        'is_active',
        'created_by',
    ];

    public function getAllTemplates(): array
    {
        return $this->select('quick_templates.*, users.full_name AS created_by_name')
            ->join('users', 'users.id = quick_templates.created_by', 'left')
            ->orderBy('quick_templates.created_at', 'DESC')
            ->findAll();
    }

    public function getActiveTemplates(): array
    {
        return $this->where('is_active', 1)
            ->orderBy('template_name', 'ASC')
            ->findAll();
    }

    public function codeExists(string $templateCode, ?int $excludeId = null): bool
    {
        $builder = $this->builder();
        $builder->where('template_code', $templateCode);

        if ($excludeId !== null) {
            $builder->where('id !=', $excludeId);
        }

        return $builder->countAllResults() > 0;
    }
}
