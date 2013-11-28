<?php

namespace CodeLovers\ExamSignupBundle\Entity;

use CodeLovers\UserBundle\Entity\User;
use Doctrine\ORM\EntityRepository;

/**
 * RegistrationRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class RegistrationRepository extends EntityRepository
{
    /**
     * @param User $user
     * @param Exam $exam
     *
     * @return Registration
     */
    public function findByExamAndUser(User $user, Exam $exam)
    {
        return $this->getEntityManager()
            ->createQuery('SELECT r FROM CodeLoversExamSignupBundle:Registration r INNER JOIN r.examDate d INNER JOIN d.exam e INNER JOIN r.user u WHERE e.id = :exam AND u.id = :user')
            ->setParameter('user', $user->getId())
            ->setParameter('exam', $exam->getId())
            ->getOneOrNullResult()
        ;
    }
}