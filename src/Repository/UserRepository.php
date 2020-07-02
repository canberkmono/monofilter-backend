<?php

namespace App\Repository;

use App\Entity\User;
use App\Enums\UserStatusCodes;
use App\Exceptions\Login\EmailNotFoundException;
use App\Exceptions\User\UserCreateException;
use App\Exceptions\User\UserNotFoundException;
use App\Exceptions\User\UserNotValidEmailException;
use App\Exceptions\User\UserTokenUpdateException;
use App\Exceptions\User\UserUpdateException;
use App\Helpers\TokenHelper;
use App\Repository\Interfaces\UserInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements UserInterface, PasswordUpgraderInterface
{
    /**
     * @var LoggerInterface $logger
     */
    protected $logger;

    /**
     * @var UserPasswordEncoderInterface $userPasswordEncoder
     */
    private $userPasswordEncoder;

    /**
     * UserRepository constructor.
     * @param ManagerRegistry $registry
     * @param UserPasswordEncoderInterface $userPasswordEncoder
     */
    public function __construct(ManagerRegistry $registry, UserPasswordEncoderInterface $userPasswordEncoder)
    {
        $this->userPasswordEncoder = $userPasswordEncoder;
        parent::__construct($registry, User::class);
    }

    /**
     * @param string $email
     * @return User
     * @throws UserNotValidEmailException
     */
    public function checkEmail(string $email): ?User
    {
        $user = self::findOneBy(['email' => $email, 'status' => UserStatusCodes::ACTIVE_USER_STATUS]);
        if ($user instanceof User) {
            throw new UserNotValidEmailException("E-Mail Used");
        }

        return $user;
    }

    /**
     * @param Request $request
     * @return User
     * @throws UserCreateException
     */
    public function create(Request $request): ?User
    {
        $user = new User();
        $user->setEmail($request->request->get('email'));
        $user->setFullName($request->request->get('fullName'));
        $user->setToken(TokenHelper::generateToken());
        $user->setPassword($this->userPasswordEncoder->encodePassword($user, $request->request->get('password')));
        $user->setStatus(UserStatusCodes::ACTIVE_USER_STATUS);
        $user->setCreatedAt(new \DateTime());

        $entityManager = $this->getEntityManager();
        try {
            $entityManager->persist($user);
            $entityManager->flush();
        } catch (\Exception $exception) {
            $this->logger->error('User Create Exception: ' . $exception->getMessage());
            throw new UserCreateException();
        }

        return $user;
    }

    /**
     * @param string $email
     * @return User
     * @throws EmailNotFoundException
     */
    public function findByEmail(string $email): ?User
    {
        $user = self::findOneBy(['email' => $email, 'status' => UserStatusCodes::ACTIVE_USER_STATUS]);
        if (!$user instanceof User) {
            throw new EmailNotFoundException("Email Not Found");
        }

        return $user;
    }

    /**
     * @param string $token
     * @return User
     * @throws UserNotFoundException
     */
    public function findByToken(string $token): ?User
    {
        $user = self::findOneBy(['token' => $token, 'status' => UserStatusCodes::ACTIVE_USER_STATUS]);
        if (!$user instanceof User) {
            throw new UserNotFoundException("User Not Found");
        }

        return $user;
    }

    /**
     * @param User $user
     * @param string $token
     * @return User
     * @throws UserTokenUpdateException
     */
    public function updateUserToken(User $user, $token): ?User
    {
        $user->setToken($token);

        $entityManager = $this->getEntityManager();
        try {
            $entityManager->persist($user);
            $entityManager->flush();
        } catch (\Exception $exception) {
            $this->logger->error('Update Token Exception: ' . $exception->getMessage());
            throw new UserTokenUpdateException();
        }
        return $user;
    }

    /**
     * @param \Symfony\Component\Security\Core\User\UserInterface $user
     * @param string $newEncodedPassword
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function upgradePassword(\Symfony\Component\Security\Core\User\UserInterface $user, string $newEncodedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newEncodedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }

    /**
     * @return array
     */
    public function list(): array
    {
        return $this->findAll();
    }

    /**
     * @param $userId
     * @return User|null
     * @throws UserNotFoundException
     */
    public function findById($userId): ?User
    {
        $user = $this->find($userId);
        if(!$user){
            throw new UserNotFoundException('User Not Found');
        }

        return $user;
    }

    /**
     * @param User $user
     * @param Request $request
     * @return User|null
     * @throws UserUpdateException
     */
    public function updateUser(User $user, Request $request): ?User
    {
        $email = $request->request->get('email');
        if (!empty($email)) {
            $user->setEmail($request->request->get('email'));
        }

        if (!empty($request->request->get('password'))) {
            $user->setPassword($this->userPasswordEncoder->encodePassword($user, $request->request->get('password')));
        }
        if (!empty($request->request->get('fullName'))) {
            $user->setFullName($request->request->get('fullName'));
        }

        if (!empty($request->request->get('email'))) {
            $user->setEmail($request->request->get('email'));
        }

        $user->setStatus($request->request->get('status') ?? true);
        $user->setUpdatedAt(new \DateTime());
        $entityManager = $this->getEntityManager();
        try {
            $entityManager->persist($user);
            $entityManager->flush();
        } catch (\Exception $exception) {
            $this->logger->error('User Update Exception: ' . $exception->getMessage());
            throw new UserUpdateException();
        }

        return $user;
    }
}
