<?php
/**
 * @package examSignup
 *
 * @author Daniel Holzmann <d@velopment.at>
 * @date 04.12.13
 * @time 13:35
 */

namespace CodeLovers\ExamSignupBundle\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;
use CodeLovers\UserBundle\Entity\User;

class ChangeUsername
{
    /**
     * @var string
     *
     * @Assert\Regex(pattern="/^[sS]{1}[\d]{10}$/", message="fos_user.username.invalid")
     */
    private $username;

    /**
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->username = $user->getUsername();
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param $username
     *
     * @return ChangeUsername
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }
}