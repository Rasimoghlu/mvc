<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Models\User;
use Exception;
use Src\Facades\Validator;
use Src\Handlers\ValidationHandler;

/**
 * User Controller
 * 
 * Handles user-related requests
 */
class UserController extends Controller
{
    /**
     * User model instance
     * 
     * @var User
     */
    protected User $user;
    
    /**
     * Create a new UserController instance
     */
    public function __construct()
    {
        $this->user = new User();
    }
    
    /**
     * Display a listing of users
     *
     * @return mixed
     */
    public function index()
    {
        $users = $this->user->all();
        
        return $this->view('users/index', compact('users'));
    }
    
    /**
     * Show the form for creating a new user
     *
     * @return mixed
     */
    public function create()
    {
        return $this->view('users/create');
    }
    
    /**
     * Display the specified user
     *
     * @param int $id
     * @return mixed
     */
    public function show(int $id)
    {
        try {
            $user = User::findOrFail($id);

            return view('users/show', [
                'user' => $user
            ]);
        } catch (Exception $e) {
            return view('errors/404', [
                'message' => 'User not found'
            ]);
        }
    }
    
    /**
     * Show the form for editing the specified user
     *
     * @param int $id
     * @return mixed
     */
    public function edit($id)
    {
        $user = $this->user->find($id);
        
        if (!$user) {
            return $this->withError('User not found')
                ->redirect('/users');
        }
        
        return $this->view('users/edit', compact('user'));
    }
    
    /**
     * Store a newly created user
     *
     * @return mixed
     */
    public function store()
    {
        try {
            $request = new UserStoreRequest();
            
            if (!$request->validate()) {
                return $request->failedValidation();
            }
            
            $data = $request->validated();
            
            // Hash password if provided
            if (isset($data['password'])) {
                $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
            }
            
            $user = $this->user->create($data);
            
            if ($user) {
                return $this->withSuccess('User created successfully')
                    ->redirect('/users');
            }
            
            return $this->withError('Failed to create user')
                ->redirect('/users/create');
        } catch (\Exception $e) {
            return $this->withError('An error occurred: ' . $e->getMessage())
                ->redirect('/users/create');
        }
    }
    
    /**
     * Update the specified user
     *
     * @param int $id
     * @return mixed
     */
    public function update($id)
    {
        try {
            $user = $this->user->find($id);
            
            if (!$user) {
                return $this->withError('User not found')
                    ->redirect('/users');
            }
            
            $request = new UserUpdateRequest();
            
            if (!$request->validate()) {
                return $request->failedValidation();
            }
            
            $data = $request->validated();
            
            $success = User::update($id, $data);
            
            if ($success) {
                return $this->withSuccess('User updated successfully')
                    ->redirect('/users');
            }
            
            return $this->withError('Failed to update user')
                ->redirect('/users/edit/' . $id);
        } catch (\Exception $e) {
            return $this->withError('An error occurred: ' . $e->getMessage())
                ->redirect('/users/edit/' . $id);
        }
    }
    
    /**
     * Remove the specified user
     *
     * @param int $id
     * @return mixed
     */
    public function destroy($id)
    {
        try {
            $user = $this->user->find($id);
            
            if (!$user) {
                return $this->withError('User not found')
                    ->redirect('/users');
            }
            
            $success = User::delete($id);
            
            if ($success) {
                return $this->withSuccess('User deleted successfully')
                    ->redirect('/users');
            }
            
            return $this->withError('Failed to delete user')
                ->redirect('/users');
        } catch (\Exception $e) {
            return $this->withError('An error occurred: ' . $e->getMessage())
                ->redirect('/users');
        }
    }
}