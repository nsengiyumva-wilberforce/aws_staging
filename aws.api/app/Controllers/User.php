<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Models\UserModel;
use MongoDB\Client as MongoDB;
use App\Libraries\Utility;
class User extends BaseController
{

	use ResponseTrait;

	public function index()
	{
		$params = $this->request->getGet();
		$model = new UserModel();
		
		// Use user_view to get region_name for frontend compatibility
		if (isset($params['user_id'])) {
			$data = $model->db->table('user_view')
				->where('user_id', $params['user_id'])
				->get()
				->getRow();
			if ($data) {
				unset($data->password);
			}
		} else {
			$data = $model->db->table('user_view')
				->get()
				->getResult();
			foreach ($data as $user) {
				unset($user->password);
			}
		}

		if($data){
			$response = [
				'status'   => 200,
				'data' => $data
			];
			return $this->respond($response);
		}else{
			return $this->failNotFound('No Data Found with id ' . ($params['user_id'] ?? ''));
		}
	}





	public function data_submission()
	{
		// ini_set('memory_limit','256M');
		$params = $this->request->getGet();

		$utility = new Utility();
		$user_map = $utility->mobile_user_mapper();

        $client = new MongoDB();
        $collection = $client->staging->entries;

		$submissions = $collection->aggregate(
			[
				['$unwind' => "$"."responses"],
				['$match' => ["responses.created_at" => ['$gte' => $params['startdate'], '$lte' => $params['enddate']]]], 
				['$group' => ['_id' => "$"."responses.creator_id", 'commits' =>  [ '$sum' => 1 ]]]
			]
		)->toArray();

		foreach ($submissions as $submission) {
			$submission['name'] = $user_map[$submission->_id];
		}

		$response = [
			'status'   => 201,
			'data' => $submissions
		];

		return $this->respond($response);
	}




	// create

	public function create()
	{
        $model = new UserModel();

        $rules = [
            'first_name' => 'required|min_length[2]',
            'last_name'  => 'required|min_length[2]',
            'email'      => 'required|valid_email|is_unique[user.email]',
        ];

        if (! $this->validate($rules)) {
            return $this->fail($this->validator->getErrors(), 400);
        }

        // Use getVar() to be able to handle both `application/json` and
        // `application/x-www-form-urlencoded` content types.
        $params = $this->request->getVar();
        // If it's a JSON object, convert it to an array.
        if (is_object($params)) {
            $params = (array) $params;
        }

        // Set default values
        $params['password'] = $params['password'] ?? 'password123';
        $params['role_id'] = $params['role_id'] ?? 1;
        $params['region_id'] = $params['region_id'] ?? 1;
        $params['active'] = 1;
        $params['date_created'] = date('Y-m-d H:i:s');

        // The model's `save` method will trigger the `beforeInsert` callback to hash the password.
        if ($model->save($params) === false) {
            return $this->fail($model->errors(), 400);
        }

        $id = $model->getInsertID();
        $data = $model->find($id);
        unset($data->password);

        return $this->respondCreated([
            'status'   => 201,
            'messages' => ['success' => 'User created successfully'],
            'data'     => $data
        ]);
	}



	// update

	public function update()
	{
		$params = $this->request->getPost();
		$model = new UserModel();

		if($model->update($params['user_id'], $params)){
			$data = $model->find($params['user_id']);
			$response = [
				'status'   => 200,
				'messages' => [
					'success' => 'Data Updated'
				],
				'data' => $data
			];

			return $this->respond($response);

		} else {
			return $this->failNotFound('No Data Found with id '.$params['user_id']);
		}
	}



	// delete

	public function delete($user_id = null)
	{
		// Handle user_id from URL parameter or POST data
		if ($user_id === null) {
			$params = $this->request->getPost();
			$user_id = $params['user_id'] ?? null;
		}
		
		if (!$user_id) {
			return $this->fail('User ID is required', 400);
		}
		
		$model = new UserModel();
		$data = $model->find($user_id);
		
		if($data){
			$model->delete($user_id);
			$response = [
				'status'   => 200,
				'messages' => [
					'success' => 'User deleted successfully'
				]
			];

			return $this->respondDeleted($response);

		} else {
			return $this->failNotFound('No Data Found with id '.$user_id);
		}
	}





	public function authenticate()
	{
		$params = $this->request->getPost();
		//$model = $this->db->table('user_view');
		$model = new UserModel();
		$user = $model->where('email', $params['username'])->first();

		if ($user) {
			if (password_verify($params['password'], $user->password)) {
				unset($user->password);
				$response = [
					'status'   => 200,
					'data' => $user
				];
				return $this->respond($response);
			} else {
				return $this->failNotFound('Wrong username and password combination');
			}			
		} else {
			return $this->failNotFound('Account does not exist');
		}
	}












	public function change_password()
	{
		$params = $this->request->getPost();
		$model = new UserModel();

		// Validate required fields
		if (empty($params['user_id'])) {
			return $this->fail('User ID is required', 400);
		}
		if (empty($params['old_password'])) {
			return $this->fail('Old password is required', 400);
		}
		if (empty($params['new_password'])) {
			return $this->fail('New password is required', 400);
		}
		if (empty($params['confirm_password'])) {
			return $this->fail('Confirm password is required', 400);
		}

		// Check if new passwords match
		if ($params['new_password'] !== $params['confirm_password']) {
			return $this->fail('New password and confirm password do not match', 400);
		}

		// Validate new password strength (optional)
		if (strlen($params['new_password']) < 6) {
			return $this->fail('New password must be at least 6 characters long', 400);
		}

		try {
			// Get user data
			$user = $model->find($params['user_id']);
			if (!$user) {
				return $this->failNotFound('User not found');
			}

			// Verify old password
			if (!password_verify($params['old_password'], $user->password)) {
				return $this->fail('Current password is incorrect', 401);
			}

			// Update password
			// The password will be hashed automatically by the UserModel's beforeUpdate callback
			$dataToUpdate = ['password' => $params['new_password']];
			if ($model->update($params['user_id'], $dataToUpdate)) {
				// Get updated user data (without password in response)
				$data = $model->find($params['user_id']);
				unset($data->password);
				
				$response = [
					'status' => 200,
					'error' => null,
					'messages' => [
						'success' => 'Password updated successfully'
					],
					'data' => $data
				];
				return $this->respond($response);
			} else {
				return $this->fail('Failed to update password', 500);
			}

		} catch (\Exception $e) {
			log_message('error', 'Password change failed: ' . $e->getMessage());
			return $this->fail('Failed to change password: ' . $e->getMessage(), 500);
		}
	}

}
