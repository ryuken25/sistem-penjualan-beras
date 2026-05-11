<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddProfilePhotoToUsersTable extends Migration
{
    public function up()
    {
        if (!$this->db->fieldExists('profile_photo', 'users')) {
            $this->forge->addColumn('users', [
                'profile_photo' => [
                    'type' => 'VARCHAR',
                    'constraint' => 255,
                    'null' => true,
                    'after' => 'password_hash',
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('profile_photo', 'users')) {
            $this->forge->dropColumn('users', 'profile_photo');
        }
    }
}
