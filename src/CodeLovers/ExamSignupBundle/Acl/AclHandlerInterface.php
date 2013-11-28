<?php
/**
 * @package rentorder
 *
 * @author Daniel Holzmann <d@velopment.at>
 * @date 30.10.13
 * @time 18:21
 */
namespace CodeLovers\ExamSignupBundle\Acl;

use CodeLovers\UserBundle\Entity\User;

interface AclHandlerInterface
{
    /**
     * @param object $object
     * @param User $user
     * @param int $permission
     * @param bool $new
     * @param null $parent
     * @return bool
     * @throws \InvalidArgumentException
     */
    public function grantAccess($object, User $user = null, $permission = Permissions::PERMISSIONS_VIEW, $new = true, $parent = null);

    /**
     * shortcut method
     *
     * @param object $object
     * @param object $parent
     *
     * @return bool
     */
    public function grantInheritedAccess($object, $parent);

    /**
     * delete the ACL for the given object
     *
     * @param object $object
     */
    public function deleteAcl($object);

    /**
     * @param object $object
     * @param User $user
     * @param int $permission
     *
     * @return AclHandlerInterface
     */
    public function revokeAccess($object, User $user, $permission = Permissions::PERMISSIONS_VIEW);
}