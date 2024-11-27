<?php
/**
 * Skeleton application for SimpleMVC
 * 
 * @link      http://github.com/simplemvc/skeleton
 * @copyright Copyright (c) Enrico Zimuel (https://www.zimuel.it)
 * @license   https://opensource.org/licenses/MIT MIT License
 */
declare(strict_types=1);

namespace App\Service;

use App\Exception\DatabaseException;
use App\Model\User;
use RedBeanPHP\Facade as R;

class Users
{
    /**
     * Returns a user by ID
     * @throws DatabaseException
     */
    public function get(int $id)
    {
		$user = R::load( 'users', $id );
        return $user;
    }
    
    /**
     * Returns all users
     * @return User[]
     */
    public function getAll(int $start, int $size): array
    {
		$users = R::findAll( 'users' );
		return $users;
    }

    /**
     * Delete a user with ID
     * @throws DatabaseException
     */
    public function delete(int $id): void
    {
		$user = R::load( 'users', $id );
	    R::trash( $user );
    }

    /**
     * Returns the total number of users
     */
    public function getTotalUsers(): int
    {
        $numOfUsers = R::count( 'users' );
        return (int) $numOfUsers;
    }

    /**
     * Returns true if the username already exist
     */
    public function exists(string $username): bool
    {
		$users  = R::find( 'users', ' username = ? ', [ $username ] );
        if (!$users) {
            return false;
        }
        return $users;
    }

    /**
     * Create a new user with username and password
     * @throws DatabaseException
     */
    public function create(string $username, string $password): void
    {
		$user = R::dispense( 'users' );
		$user->username = $username;
		$user->password = $password;
        $id = R::store( $user );
    }

    /**
     * Update the user with active and password if not empty
     * @throws DatabaseException
     */
    public function update(int $id, bool $active, string $password): void
    {
		$user = R::load( 'users', $id );
        if (empty($password)) {
			$user->active = $active;
        } else {
			$user->active = $active;
			$user->password = password_hash($password, PASSWORD_DEFAULT);
        }
		R::store( $user );
    }

    /**
     * Update the last login with the actual time
     * @throws DatabaseException
     */
    public function updateLastLogin(string $username): void
    {
		$user = R::find( 'users', ' username = ? ', [ $username ] );
		$firstUser = reset( $user );
		$firstUser->last_login = date("Y-m-d H:i:s");
		R::store( $firstUser );
    }
}
