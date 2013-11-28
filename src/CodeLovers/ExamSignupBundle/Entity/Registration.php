<?php

namespace CodeLovers\ExamSignupBundle\Entity;

use CodeLovers\UserBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * Registration
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="CodeLovers\ExamSignupBundle\Entity\RegistrationRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Registration
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="CodeLovers\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    private $user;

    /**
     * @var ExamDate
     *
     * @ORM\OneToOne(targetEntity="ExamDate", mappedBy="registration")
     */
    private $examDate;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Registration
     */
    public function setCreated($created)
    {
        $this->created = $created;
    
        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime 
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @return \CodeLovers\UserBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param \CodeLovers\UserBundle\Entity\User $user
     *
     * @return Registration
     */
    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return \CodeLovers\ExamSignupBundle\Entity\ExamDate
     */
    public function getExamDate()
    {
        return $this->examDate;
    }

    /**
     * @param \CodeLovers\ExamSignupBundle\Entity\ExamDate $examDate
     *
     * @return Registration
     */
    public function setExamDate(ExamDate $examDate)
    {
        $this->examDate = $examDate;
        $examDate->setRegistration($this);

        return $this;
    }

    /**
     *
     * @return string
     */
    public function __toString()
    {
        return (string)$this->getUser();
    }

    /**
     * @ORM\PrePersist()
     */
    public function prePersist()
    {
        $this->setCreated(new \DateTime());
    }
}
