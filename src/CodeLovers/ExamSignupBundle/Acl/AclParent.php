<?php
/**
 * use this interface if you want to cascade
 * grantInheritedAccess() calls to child documents
 *
 * @package rentorder
 *
 * @author Daniel Holzmann <d@velopment.at>
 * @date 25.09.13
 * @time 14:55
 */

namespace CodeLovers\ExamSignupBundle\Acl;


interface AclParent
{
    /**
     * returns an array with the children
     * on which grantInheritedAccess() should be called
     * afterwards
     *
     * @return array
     */
    public function getAclChildren();
}