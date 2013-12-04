<?php

namespace CodeLovers\ExamSignupBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends BaseController
{
    /**
     * @Route("/", name="_index")
     * @Template()
     */
    public function indexAction()
    {
        return array(
            'exams' =>  $this->getExamRepository()->findAll(),
        );
    }

    /**
     * will redirect to the admin area
     * handy for switching back from impersonating a user
     *
     * @Route("/switch_back", name="_switch_back")
     */
    public function switchBackAction()
    {
        return $this->redirect($this->generateUrl('_admin_index'));
    }
}
