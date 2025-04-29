<?php

namespace Src\Handlers;

use App\Interfaces\AuthInterface;
use App\Models\User;
use Exception;
use Src\Facades\Session;

/**
 * Authentication Handler
 * 
 * Handles user authentication, session management and access control
 */
class AuthHandler implements AuthInterface
{
    /**
     * Session key for authenticated user
     * 
     * @var string
     */
    private const AUTH_SESSION = 'auth';
    
    /**
     * Error messages
     * 
     * @var array
     */
    protected array $errors = [
        'invalid_credentials' => 'Invalid email or password',
        'account_inactive' => 'Your account is inactive',
        'login_attempts' => 'Too many login attempts. Please try again later.',
        'no_permission' => 'You do not have permission to access this resource',
    ];

    /**
     * Attempt to log in a user with the provided credentials
     *
     * @param array $data Login credentials (email, password)
     * @return object|string User object if successful, error message otherwise
     */
    public function login(array $data): object|string
    {
        // Validate required fields
        if (!isset($data['email']) || !isset($data['password'])) {
            return $this->errors['invalid_credentials'];
        }
        
        try {
            // Find user by email
            $user = User::where('email', '=', $data['email'])->first();
            
            // Check if user exists and verify password
            if ($user && $this->verifyPassword($data['password'], $user->password)) {
                // Store user in session
                $this->setUserSession($user);
                return $user;
            }
            
            // Invalid credentials
            return $this->errors['invalid_credentials'];
        } catch (Exception $e) {
            // Log the error
            error_log('Login error: ' . $e->getMessage());
            return 'An error occurred during login. Please try again.';
        }
    }

    /**
     * Log out the current user
     *
     * @return bool Success status
     */
    public function logout(): bool
    {
        try {
            Session::remove(self::AUTH_SESSION);
            return true;
        } catch (Exception $e) {
            // Log the error
            error_log('Logout error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get the currently authenticated user
     *
     * @return object|null User object if authenticated, null otherwise
     */
    public function user(): object|null
    {
        try {
            return $this->getAuthenticatedUser();
        } catch (Exception $e) {
            // Log the error
            error_log('Error getting authenticated user: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Check if user is logged in
     *
     * @return bool
     */
    public function check(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Check if user is a guest (not logged in)
     *
     * @return bool
     */
    public function guest(): bool
    {
        return !$this->check();
    }
    
    /**
     * Check if a user has a specific role
     *
     * @param string $role Role to check
     * @return bool
     */
    public function hasRole(string $role): bool
    {
        $user = $this->user();
        
        // If no user is authenticated, return false
        if (!$user) {
            return false;
        }
        
        // Check if user has the specified role (adjust based on your user model structure)
        return isset($user->role) && $user->role === $role;
    }

    /**
     * Verify if the provided password matches the stored hash
     *
     * @param string $password Plain text password to verify
     * @param string $hash Stored password hash
     * @return bool Whether the password is correct
     */
    private function verifyPassword(string $password, string $hash): bool
    {
        // If password is stored using SHA1 (legacy)
        if (strlen($hash) === 40) {
            return sha1($password) === $hash;
        }
        
        // Otherwise use secure password_verify (for passwords hashed with password_hash)
        return password_verify($password, $hash);
    }

    /**
     * Store user data in the session
     *
     * @param object $user User to store in session
     * @return void
     */
    private function setUserSession(object $user): void
    {
        // Don't store the full user object with all database fields
        // Only store essential fields needed for authentication
        $sessionUser = (object)[
            'id' => $user->id,
            'email' => $user->email,
            'name' => $user->name ?? '',
            'role' => $user->role ?? 'user',
            'logged_in_at' => date('Y-m-d H:i:s')
        ];
        
        Session::set(self::AUTH_SESSION, $sessionUser);
    }

    /**
     * Get the authenticated user from session and refresh from database
     *
     * @return object|null User object if authenticated, null otherwise
     */
    private function getAuthenticatedUser(): ?object
    {
        if (!Session::has(self::AUTH_SESSION)) {
            return null;
        }
        
        $sessionUser = Session::get(self::AUTH_SESSION);
        
        // If session user has ID, fetch fresh user data from database
        if (isset($sessionUser->id)) {
            return User::find($sessionUser->id);
        }
        
        return null;
    }
}