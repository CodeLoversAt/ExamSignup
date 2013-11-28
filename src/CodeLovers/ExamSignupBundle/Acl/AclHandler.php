<?php
/**
 * Created by JetBrains PhpStorm.
 * User: daniel
 * Date: 16.08.13
 * Time: 12:10
 */

namespace CodeLovers\ExamSignupBundle\Acl;


use CodeLovers\UserBundle\Entity\User;
use Monolog\Logger;
use Symfony\Component\Security\Acl\Domain\Acl;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Exception\AclAlreadyExistsException;
use Symfony\Component\Security\Acl\Exception\AclNotFoundException;
use Symfony\Component\Security\Acl\Model\DomainObjectInterface;
use Symfony\Component\Security\Acl\Model\EntryInterface;
use Symfony\Component\Security\Acl\Model\MutableAclProviderInterface;
use Symfony\Component\Security\Acl\Model\ObjectIdentityInterface;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;
use Symfony\Component\Security\Core\SecurityContextInterface;

class AclHandler implements AclHandlerInterface
{
    const PROXY_CLASS_NAME = 'Doctrine\ODM\MongoDB\Proxy\Proxy';

    /**
     * @var MutableAclProviderInterface
     */
    private $aclProvider;

    /**
     * @var SecurityContextInterface $sc
     */
    private $sc;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @param MutableAclProviderInterface $aclProvider
     * @param SecurityContextInterface $sc
     * @param \Monolog\Logger $logger
     */
    public function __construct(MutableAclProviderInterface $aclProvider, SecurityContextInterface $sc, Logger $logger)
    {
        $this->aclProvider = $aclProvider;
        $this->sc = $sc;
        $this->logger = $logger;
    }

    /**
     * @param object $object
     * @param User $user
     * @param int $permission
     * @param bool $new
     * @param null $parent
     * @return bool
     * @throws \InvalidArgumentException
     */
    public function grantAccess($object, User $user = null, $permission = Permissions::PERMISSIONS_VIEW, $new = true, $parent = null)
    {
        $objectIdentity = $this->getObjectIdentity($object);

        if (!$objectIdentity) {
            $this->logger->addDebug('could not create ObjectIdentity for object of class ' . get_class($objectIdentity));
        } else {
            $acl = $this->getAcl($objectIdentity, $new);

            // grant permission
            if (null === ($mask = $this->getMask($permission)) && !$parent) {
                $this->logger->addCritical('if permission is set to \'ignore\’ you have to pass in a parent object to inherit from');
                throw new \InvalidArgumentException('if permission is set to \'ignore\’ you have to pass in a parent object to inherit from');
            }

            if ($mask) {
                // if no user is given, get the one currently logged in
                if (!$user) {
                    $user = $this->sc->getToken()->getUser();
                }

                $securityIdentity = UserSecurityIdentity::fromAccount($user);
                $acl->insertObjectAce($securityIdentity, $mask);
            }

            // inherit?
            if ($parent && is_object($parent)) {
                $parentIdentity = $this->getObjectIdentity($parent);
                $parentAcl = $this->getAcl($parentIdentity, false);
                $acl->setParentAcl($parentAcl);
            }

            $this->aclProvider->updateAcl($acl);
            $this->logger->addDebug('updated acl for ' . get_class($object));
        }

        // cascade
        if ($object instanceof AclParent) {
            if ($objectIdentity) {
                $parent = $object;
            } elseif (!$parent) {
                // there's no parent to inherit from
                // and we can't create an object identity
                // for the current object
                // so we're not able to inherit from anything
                return true;
            }
            $this->logger->addDebug('cascading ACL to children of ' . get_class($parent));
            foreach ($object->getAclChildren() as $child) {
                if ($child) {
                    $this->grantInheritedAccess($child, $parent);
                }
            }
        }

        return true;
    }

    /**
     * shortcut method
     *
     * @param object $object
     * @param object $parent
     *
     * @return bool
     */
    public function grantInheritedAccess($object, $parent)
    {
        return $this->grantAccess($object, null, Permissions::PERMISSIONS_IGNORE, true, $parent);
    }

    /**
     * @param object $object
     * @param User $user
     * @param int $permission
     *
     * @return AclHandlerInterface
     */
    public function revokeAccess($object, User $user, $permission = Permissions::PERMISSIONS_VIEW)
    {
        $objectIdentity = $this->getObjectIdentity($object);
        try {
            $acl = $this->aclProvider->findAcl($objectIdentity);
        } catch (AclNotFoundException $e) {
            // nothing to do
            return $this;
        }

        $securityIdentity = UserSecurityIdentity::fromAccount($user);
        $mask = $this->getMask($permission);

        foreach ($acl->getObjectAces() as $i => $ace) {
            /** @var EntryInterface $ace */
            if ($securityIdentity->equals($ace->getSecurityIdentity())) {
                $this->revokeMask($i, $acl, $ace, $mask);
            }
        }

        $this->aclProvider->updateAcl($acl);

        return $this;

    }

    /**
     * delete the ACL for the given object
     *
     * @param object $object
     */
    public function deleteAcl($object)
    {
        try {
            if ($objectIdentity = $this->getObjectIdentity($object)) {
                $this->aclProvider->deleteAcl($objectIdentity);
            }
        } catch (\Exception $e) {
            // nothing to do
        }
    }

    /**
     * @param int $index
     * @param Acl $acl
     * @param EntryInterface $ace
     * @param int $mask
     *
     * @return AclHandlerInterface
     */
    private function revokeMask($index, Acl $acl, EntryInterface $ace, $mask)
    {
        $acl->updateObjectAce($index, $ace->getMask() & ~$mask);

        return $this;
    }

    /**
     * @param int $permission
     * @return int|null
     * @throws \InvalidArgumentException
     */
    private function getMask($permission = Permissions::PERMISSIONS_VIEW)
    {
        if ($permission === Permissions::PERMISSIONS_OWNER) {
            return MaskBuilder::MASK_OWNER;
        } elseif ($permission === Permissions::PERMISSIONS_VIEW) {
            return MaskBuilder::MASK_VIEW;
        } elseif ($permission === Permissions::PERMISSIONS_EDIT) {
            return MaskBuilder::MASK_MASTER;
        } elseif ($permission === Permissions::PERMISSIONS_IGNORE) {
            return null;
        } else {
            throw new \InvalidArgumentException(sprintf("invalid permission given: %d", $permission));
        }
    }

    /**
     * @param object $domainObject
     * @return ObjectIdentityInterface
     */
    private function getObjectIdentity($domainObject)
    {
        if (!is_object($domainObject)) {
            return null;
        }

        if ($domainObject instanceof DomainObjectInterface) {
            return new ObjectIdentity($domainObject->getObjectIdentifier(), $this->getObjectClass($domainObject));
        } elseif (method_exists($domainObject, 'getId')) {
            return new ObjectIdentity($domainObject->getId(), $this->getObjectClass($domainObject));
        }

        return null;
    }

    /**
     * @param ObjectIdentityInterface $objectIdentity
     * @param boolean $new
     * @return \Symfony\Component\Security\Acl\Model\MutableAclInterface
     */
    private function getAcl(ObjectIdentityInterface $objectIdentity, $new)
    {
        if ($new) {
            try {
                return $this->aclProvider->createAcl($objectIdentity);
            } catch (AclAlreadyExistsException $e) {
                return $this->aclProvider->findAcl($objectIdentity);
            }
        } else {
            try {
                return $this->aclProvider->findAcl($objectIdentity);
            } catch( AclNotFoundException $e) {
                return $this->aclProvider->createAcl($objectIdentity);
            }
        }
    }

    /**
     * @param $domainObject
     * @return string
     */
    private function getObjectClass($domainObject)
    {
        if (in_array(self::PROXY_CLASS_NAME, class_implements($domainObject))) {
            return get_parent_class($domainObject);
        }

        return get_class($domainObject);
    }
}