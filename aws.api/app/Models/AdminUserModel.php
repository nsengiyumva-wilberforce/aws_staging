<?php

namespace App\Models;

use CodeIgniter\Model;

class AdminUserModel extends Model
{
    protected $table      = 'admin_user';
    protected $primaryKey = 'user_id';

    // protected $returnType = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = ['first_name', 'last_name', 'region_id', 'email', 'password', 'role_id', 'date_created', 'active'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // protected $validationRules    = [];
    // protected $validationMessages = [];
    // protected $skipValidation     = false;


    protected $beforeInsert = ['beforeInsert', 'hashPassword'];
    protected $beforeUpdate = ['hashPassword'];
    // protected $beforeDelete = ['beforeDelete'];

    protected function beforeInsert(array $data): array
    {
        $data['data']['active'] = 1;
        return $data;
    }

    /**
     * A callback function that is executed before a new record is inserted
     * or an existing one is updated. It automatically hashes the password.
     */
    protected function hashPassword(array $data)
    {
        if (! isset($data['data']['password'])) {
            return $data;
        }

        // Don't re-hash if the password hasn't changed or is already hashed
        if (strlen($data['data']['password']) < 60) {
            $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        }
        
        return $data;
    }

}