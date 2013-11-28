<?php
/**
 * Created by JetBrains PhpStorm.
 * User: daniel
 * Date: 16.08.13
 * Time: 12:22
 */

namespace CodeLovers\ExamSignupBundle\Acl;


abstract class Permissions
{
    const PERMISSIONS_IGNORE = 99; // if this permission is passed, we'll create no extra ACL entries, but inherit from parent
    const PERMISSIONS_OWNER = 0;
    const PERMISSIONS_VIEW = 1;
    const PERMISSIONS_EDIT = 2;
}