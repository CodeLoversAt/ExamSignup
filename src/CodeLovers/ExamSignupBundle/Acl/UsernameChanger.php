<?php
/**
 * @package examSignup
 *
 * @author Daniel Holzmann <d@velopment.at>
 * @date 04.12.13
 * @time 12:49
 */

namespace CodeLovers\ExamSignupBundle\Acl;


use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use FOS\UserBundle\Model\UserInterface;

class UsernameChanger
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @param UserInterface $user
     * @param $oldUserName
     *
     */
    public function changeUsername(UserInterface $user, $oldUserName)
    {
        $connection = $this->em->getConnection();
        // CodeLovers\UserBundle\Entity\User-alexander19861986@gmail.com
        // CodeLovers\UserBundle\Entity\User-andreas.etzelstorfer@students.fh-hagenberg.at
        // CodeLovers\UserBundle\Entity\User-juliaw18@hotmail.com
        // CodeLovers\UserBundle\Entity\User-s1110307014@students.fh-hagenberg.at

        $pattern = sprintf("%s-%%s", get_class($user));

        $stmt = $connection->prepare('UPDATE acl_security_identities SET identifier = :new WHERE username = 1 AND identifier = :old');
        $stmt->bindValue('new', sprintf($pattern, $user->getUsernameCanonical()));
        $stmt->bindValue('old', sprintf($pattern, $oldUserName));
        $stmt->execute();
        $stmt->closeCursor();
    }
} 