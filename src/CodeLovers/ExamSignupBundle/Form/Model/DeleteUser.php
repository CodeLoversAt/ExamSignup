<?php
/**
 * @package examSignup
 *
 * @author Daniel Holzmann <d@velopment.at>
 * @date 04.12.13
 * @time 13:19
 */

namespace CodeLovers\ExamSignupBundle\Form\Model;

use CodeLovers\UserBundle\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;

class DeleteUser
{
    /**
     * @var bool
     *
     * @Assert\True
     */
    private $confirm = false;

    /**
     * @var User
     */
    private $user;

    /**
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return boolean
     */
    public function getConfirm()
    {
        return $this->confirm;
    }

    /**
     * @param boolean $confirm
     *
     * @return DeleteUser
     */
    public function setConfirm($confirm)
    {
        $this->confirm = $confirm;

        return $this;
    }

    /**
     * @return \CodeLovers\UserBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }
}