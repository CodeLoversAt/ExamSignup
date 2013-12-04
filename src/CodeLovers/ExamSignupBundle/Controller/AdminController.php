<?php
/**
 * @package examSignup
 *
 * @author Daniel Holzmann <d@velopment.at>
 * @date 04.12.13
 * @time 10:42
 */

namespace CodeLovers\ExamSignupBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class AdminController extends BaseController
{
    /**
     * @Route("/", name="_admin_index")
     * @Template()
     */
    public function indexAction()
    {
        return array(
            'users' => $this->getUserRepository()->findAll()
        );
    }
} 