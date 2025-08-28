<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RemoveGoogleAuthFromUsers extends Migration
{
    public function up()
    {
        // Remove Google-related columns from the user table
        $this->forge->dropColumn('user', [
            'google_id',
            'google_access_token', 
            'google_refresh_token',
            'google_photo_url',
            'auth_provider',
            'google_verified'
        ]);
    }

    public function down()
    {
        // Add the columns back if we need to rollback this migration
        $this->forge->addColumn('user', [
            'google_id' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'unique' => true,
            ],
            'google_access_token' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'google_refresh_token' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'google_photo_url' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'auth_provider' => [
                'type' => 'ENUM',
                'constraint' => ['email', 'google'],
                'default' => 'email',
                'null' => false,
            ],
            'google_verified' => [
                'type' => 'BOOLEAN',
                'default' => false,
                'null' => false,
            ],
        ]);
    }
}
