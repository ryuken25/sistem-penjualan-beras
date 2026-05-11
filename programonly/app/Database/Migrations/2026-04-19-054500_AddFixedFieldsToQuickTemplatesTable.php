<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFixedFieldsToQuickTemplatesTable extends Migration
{
    public function up()
    {
        $fields = [];

        if (!$this->db->fieldExists('qty_5kg', 'quick_templates')) {
            $fields['qty_5kg'] = ['type' => 'INT', 'constraint' => 11, 'default' => 0, 'after' => 'template_name'];
        }
        if (!$this->db->fieldExists('qty_10kg', 'quick_templates')) {
            $fields['qty_10kg'] = ['type' => 'INT', 'constraint' => 11, 'default' => 0, 'after' => 'qty_5kg'];
        }
        if (!$this->db->fieldExists('qty_25kg', 'quick_templates')) {
            $fields['qty_25kg'] = ['type' => 'INT', 'constraint' => 11, 'default' => 0, 'after' => 'qty_10kg'];
        }

        if ($fields !== []) {
            $this->forge->addColumn('quick_templates', $fields);
        }
    }

    public function down()
    {
        foreach (['qty_5kg', 'qty_10kg', 'qty_25kg'] as $field) {
            if ($this->db->fieldExists($field, 'quick_templates')) {
                $this->forge->dropColumn('quick_templates', $field);
            }
        }
    }
}
