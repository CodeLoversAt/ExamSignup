<?php

namespace CodeLovers\ExamSignupBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Exam
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="CodeLovers\ExamSignupBundle\Entity\ExamRepository")
 */
class Exam
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     *
     * @Assert\NotBlank
     */
    private $name;

    /**
     * @var Collection
     *
     * @Assert\Valid
     *
     * @ORM\OneToMany(targetEntity="ExamDate", mappedBy="exam", cascade={"persist"})
     * @ORM\OrderBy({"date"="ASC"})
     */
    private $dates;

    /**
     * ctor
     */
    public function __construct()
    {
        $this->dates = new ArrayCollection();
    }
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
     * Set name
     *
     * @param string $name
     * @return Exam
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getDates()
    {
        return $this->dates;
    }

    /**
     * @param ExamDate $date
     *
     * @return $this
     */
    public function addDate(ExamDate $date)
    {
        $date->setExam($this);

        $this->dates->add($date);

        return $this;
    }

    /**
     * @param ExamDate $date
     *
     * @return $this
     */
    public function removeDate(ExamDate $date)
    {
        $this->dates->removeElement($date);

        return $this;
    }
}
