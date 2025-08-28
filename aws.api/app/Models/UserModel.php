<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    /**
     * The table associated with this model.
     * This is the most important change to ensure you are reading from the correct table.
     */
    protected $table            = 'user';

    protected $primaryKey       = 'user_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;

    /**
     * An array of field names that are allowed to be saved to the database.
     * This is a security feature to prevent mass assignment vulnerabilities.
     */
    protected $allowedFields    = [
        'first_name',
        'last_name',
        'email',
        'password',
        'role_id',
        'region_id',
        'active',
        'date_created'
    ];

    // Callbacks
    protected $beforeInsert   = ['hashPassword'];
    protected $beforeUpdate   = ['hashPassword'];

    /**
     * A callback function that is executed before a new record is inserted
     * or an existing one is updated. It automatically hashes the password.
     */
    protected function hashPassword(array $data)
    {
        if (! isset($data['data']['password'])) {
            return $data;
        }

        // Don't re-hash if the password hasn't changed
        if (strlen($data['data']['password']) < 60) {
            $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        }
        
        return $data;
    }
}