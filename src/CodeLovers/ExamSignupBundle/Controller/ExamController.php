<?php

namespace CodeLovers\ExamSignupBundle\Controller;

use CodeLovers\ExamSignupBundle\Acl\Permissions;
use CodeLovers\ExamSignupBundle\Entity\Exam;
use CodeLovers\ExamSignupBundle\Entity\ExamDate;
use CodeLovers\ExamSignupBundle\Entity\Registration;
use CodeLovers\ExamSignupBundle\Form\ExamType;
use CodeLovers\UserBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\Request;

class ExamController extends BaseController
{
    /**
     * @Route("/{id}", name="_exam_show",
     *  requirements={"id"="\d+"}
     * )
     * @Template
     */
    public function showAction($id)
    {
        return array(
            'exam' => $this->loadExam($id)
        );
    }

    /**
     * @Secure(roles="ROLE_ADMIN")
     * @Route("/edit/{id}", name="_exam_edit",
     *  requirements={"id"="\d+"},
     *  defaults={"id"=0}
     * )
     * @Template()
     */
    public function editAction($id, Request $request)
    {
        if ($id) {
            $exam = $this->loadExam($id);
        } else {
            $exam = new Exam();
        }

        $form = $this->createForm(new ExamType(), $exam);

        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                foreach ($exam->getDates() as $date) {
                    /** @var ExamDate $date */
                    $date->setExam($exam);
                }
                $em = $this->getEntityManager();

                if (!$id) {
                    $em->persist($exam);
                }
                $em->flush();

                return $this->redirect($this->generateUrl('_index'));
            }
        }

        return array(
            'form' => $form->createView(),
        );
    }

    /**
     * signs up the currently logged in user to the given exam date
     *
     * @Route("/signup/{id}", name="_examdate_signup",
     *  requirements={"id"="\d+"}
     * )
     */
    public function signupAction($id)
    {
        $date = $this->loadExamDate($id);
        $exam = $date->getExam();
        /** @var User $user */
        $user = $this->getUser();

        // check existing registration
        if ($date->getRegistration()) {
            // from the same user?
            if ($user->getId() == $date->getRegistration()->getUser()->getId()) {
                /** @Desc("You're already registered for this appointment") */
                $message = $this->get('translator')->trans('exam.signedUp.already');
                $messageType = 'info';
            } else {
                /** @Desc("Sorry, but this appointment is already occupied") */
                $message = $this->get('translator')->trans('exam.signedUp.occupied');
                $messageType = 'danger';
            }
        } else {
            // free slot

            // check if there's another registration for the current user?
            $em = $this->getEntityManager();
            $messageType = 'success';

            if ($existingRegistration = $this->getRegistrationRepository()->findByExamAndUser($user, $exam)) {
                // remove this registration
                $em->remove($existingRegistration);

                /** @Desc("You registration has been changed to the selected appointment") */
                $message = $this->get('translator')->trans('exam.signedUp.updated');
            } else {
                /** @Desc("Your registration has been saved") */
                $message = $this->get('translator')->trans('exam.signedUp.success');
            }

            $registration = new Registration();
            $registration->setExamDate($date);
            $registration->setUser($user);

            $em->persist($registration);
            $em->flush();

            // grant access
            $this->grantAccess($registration, $user, Permissions::PERMISSIONS_OWNER);
        }

        $this->addFlashMessage($message, $messageType);

        return $this->redirect($this->generateUrl('_exam_show', array('id' => $exam->getId())));
    }

    /**
     * delete the given registration
     *
     * @Route("/unregister/{id}", name="_registration_delete",
     *  requirements={"id"="\d+"}
     * )
     */
    public function unregisterAction($id)
    {
        $registration = $this->loadRegistration($id);
        $exam = $registration->getExamDate()->getExam();
        $em = $this->getEntityManager();
        $em->remove($registration);
        $em->flush();

        /** @Desc("You have been unregistiered from the selected appointment") */
        $message = $this->get('translator')->trans('registration.deleted');

        $this->addFlashMessage($message, 'info');

        return $this->redirect($this->generateUrl('_exam_show', array('id' => $exam->getId())));
    }
}
