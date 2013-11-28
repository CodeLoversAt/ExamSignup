<?php

namespace CodeLovers\ExamSignupBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * ExamDate
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="CodeLovers\ExamSignupBundle\Entity\ExamDateRepository")
 */
class ExamDate
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
     * @ORM\Column(name="date", type="datetime")
     *
     * @Assert\NotBlank
     * @Assert\DateTime
     */
    private $date;

    /**
     * @var string
     *
     * @ORM\Column(name="location", type="string", length=255)
     *
     * @Assert\NotBlank
     */
    private $location;

    /**
     * @var Exam
     *
     * @ORM\ManyToOne(targetEntity="Exam", inversedBy="dates")
     * @ORM\JoinColumn(name="exam_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     */
    private $exam;

    /**
     * @var Registration
     *
     * @ORM\OneToOne(targetEntity="Registration", inversedBy="examDate")
     * @ORM\JoinColumn(name="registration_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    private $registration;


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
     * Set date
     *
     * @param \DateTime $date
     * @return ExamDate
     */
    public function setDate($date)
    {
        $this->date = $date;
    
        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set location
     *
     * @param string $location
     * @return ExamDate
     */
    public function setLocation($location)
    {
        $this->location = $location;
    
        return $this;
    }

    /**
     * Get location
     *
     * @return string 
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @return \CodeLovers\ExamSignupBundle\Entity\Exam
     */
    public function getExam()
    {
        return $this->exam;
    }

    /**
     * @param \CodeLovers\ExamSignupBundle\Entity\Exam $exam
     *
     * @return ExamDate
     */
    public function setExam(Exam $exam)
    {
        $this->exam = $exam;

        return $this;
    }

    /**
     * @return \CodeLovers\ExamSignupBundle\Entity\Registration
     */
    public function getRegistration()
    {
        return $this->registration;
    }

    /**
     * @param \CodeLovers\ExamSignupBundle\Entity\Registration $registration
     *
     * @return ExamDate
     */
    public function setRegistration($registration)
    {
        $this->registration = $registration;

        return $this;
    }
}
