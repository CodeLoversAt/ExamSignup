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
}
