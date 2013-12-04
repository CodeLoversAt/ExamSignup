<?php
/**
 * @package examSignup
 *
 * @author Daniel Holzmann <d@velopment.at>
 * @date 28.11.13
 * @time 12:47
 */

namespace CodeLovers\ExamSignupBundle\Controller;

use CodeLovers\ExamSignupBundle\Acl\Permissions;
use CodeLovers\ExamSignupBundle\Entity\Exam;
use CodeLovers\ExamSignupBundle\Entity\ExamDate;
use CodeLovers\ExamSignupBundle\Entity\ExamDateRepository;
use CodeLovers\ExamSignupBundle\Entity\ExamRepository;
use CodeLovers\ExamSignupBundle\Entity\Registration;
use CodeLovers\ExamSignupBundle\Entity\RegistrationRepository;
use CodeLovers\UserBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;


abstract class BaseController extends Controller
{
    /**
     *
     * @return \Doctrine\ORM\EntityManager
     */
    protected function getEntityManager()
    {
        return $this->get('doctrine.orm.default_entity_manager');
    }

    /**
     *
     * @return UserRepository
     */
    protected function getUserRepository()
    {
        return $this->getEntityManager()->getRepository('CodeLoversUserBundle:User');
    }

    /**
     *
     * @return ExamRepository
     */
    protected function getExamRepository()
    {
        return $this->getEntityManager()->getRepository('CodeLoversExamSignupBundle:Exam');
    }

    /**
     *
     * @return ExamDateRepository
     */
    protected function getExamDateRepository()
    {
        return $this->getEntityManager()->getRepository('CodeLoversExamSignupBundle:ExamDate');
    }

    /**
     *
     * @return RegistrationRepository
     */
    protected function getRegistrationRepository()
    {
        return $this->getEntityManager()->getRepository('CodeLoversExamSignupBundle:Registration');
    }

    /**
     * @param int $id
     *
     * @return Exam
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    protected function loadExam($id)
    {
        $exam = $this->getExamRepository()->find($id);

        if (!$exam) {
            throw $this->createNotFoundException('Invalid exam id: ' . $id);
        }

        return $exam;
    }

    /**
     * {@inheritDoc}
     */
    public function createForm($type, $data = null, array $options = array())
    {
        $options['attr'] = array_merge(array('class' => 'form-horizontal', 'role' => 'form'), (isset($options['attr']) ? $options['attr'] : array()));

        return parent::createForm($type, $data, $options);
    }

    /**
     * @param $object
     * @param User $user
     * @param int $permission
     * @param bool $new
     * @param null $parent
     *
     * @return bool
     */
    public function grantAccess($object, User $user = null, $permission = Permissions::PERMISSIONS_VIEW, $new = true, $parent = null)
    {
        return $this->get('aclHandler')->grantAccess($object, $user, $permission, $new, $parent);
    }

    /**
     * @param $object
     * @param $parent
     *
     * @return bool
     */
    public function grantInheritedAccess($object, $parent)
    {
        return $this->get('aclHandler')->grantInheritedAccess($object, $parent);
    }

    /**
     * @param int $id
     *
     * @return ExamDate
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function loadExamDate($id)
    {
        $date = $this->getExamDateRepository()->find($id);

        if (!$date) {
            throw $this->createNotFoundException('invalid exam date id: ' . $id);
        }

        return $date;
    }

    /**
     * @param int $id
     *
     * @return Registration
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    public function loadRegistration($id)
    {
        $registration = $this->getRegistrationRepository()->find($id);

        if (!$registration) {
            throw $this->createNotFoundException('invalid registration id: ' . $id);
        }

        if (false === $this->get('security.context')->isGranted('EDIT', $registration)) {
            throw new AccessDeniedException();
        }

        return $registration;
    }

    /**
     * @param string $message
     * @param string $type
     *
     */
    protected function addFlashMessage($message, $type = 'success')
    {
        /** @var Session $session */
        $session = $this->getRequest()->getSession();
        $session->getFlashBag()->add($type, $message);
    }

    /**
     * @param string $fileName
     * @return string
     * @throws \RuntimeException
     */
    protected function createSaveFileName($fileName)
    {
        if (!$fileName || !is_string($fileName)) {
            throw new \RuntimeException('filename needs to be a string');
        }

        return preg_replace('#[^a-zA-Z0-9_.]+#', '_', $fileName);
    }

    /**
     * @param int $id
     *
     * @return User
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @throws \Symfony\Component\Security\Core\Exception\AccessDeniedException
     */
    protected function loadUser($id)
    {
        /** @var User $user */
        $user = $this->getUserRepository()->find($id);

        if (!$user) {
            throw $this->createNotFoundException();
        }

        // check permission
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        if ($user->getId() !== $currentUser->getId() && false === $this->get('security.context')->isGranted('ROLE_SUPER_ADMIN')) {
            throw new AccessDeniedException();
        }

        return $user;
    }
}