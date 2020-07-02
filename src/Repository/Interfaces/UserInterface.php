<?php
/**
 * Created by PhpStorm.
 * User: canberkgecgel
 * Date: 28.06.2020
 * Time: 16:19
 */

namespace App\Repository\Interfaces;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;

interface UserInterface
{
    public function checkEmail(string $email): ?User;

    public function create(Request $request): ?User;

    public function findByEmail(string $email): ?User;

    public function findByToken(string $token): ?User;

    public function updateUserToken(User $user, $token): ?User;

    public function list(): array;

    public function findById($userId): ?User;

    public function updateUser(User $user, Request $request): ?User;
}