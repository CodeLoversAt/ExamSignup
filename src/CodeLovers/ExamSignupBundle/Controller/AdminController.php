<?php
/**
 * @package examSignup
 *
 * @author Daniel Holzmann <d@velopment.at>
 * @date 04.12.13
 * @time 10:42
 */

namespace CodeLovers\ExamSignupBundle\Controller;

use CodeLovers\ExamSignupBundle\Form\ChangeUsernameType;
use CodeLovers\ExamSignupBundle\Form\DeleteUserType;
use CodeLovers\ExamSignupBundle\Form\Model\ChangeUsername;
use CodeLovers\ExamSignupBundle\Form\Model\DeleteUser;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use CodeLovers\ExamSignupBundle\Export\ExcelExport;
use Symfony\Component\HttpFoundation\Response;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\Request;
use CodeLovers\ExamSignupBundle\Acl\UsernameChanger;

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

    /**
     * deletes a user
     *
     * @Route("/delete_user/{id}", name="_delete_user")
     * @Secure(roles="ROLE_SUPER_ADMIN")
     * @Template()
     */
    public function deleteUserAction(Request $request, $id)
    {
        $user = $this->loadUser($id);

        $model = new DeleteUser($user);
        $form = $this->createForm(new DeleteUserType(), $model);

        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $userManager = $this->get('fos_user.user_manager');
                $userManager->deleteUser($user);

                return $this->redirect($this->generateUrl('_admin_index'));
            }
        }

        return array(
            'form' => $form->createView(),
            'user' => $user,
        );
    }

    /**
     * change a user's username
     *
     * @Route("/change_username/{user_id}", name="_change_username",
     *  requirements={"user_id"="\d+"}
     * )
     * @Template()
     * @Secure(roles="ROLE_SUPER_ADMIN")
     */
    public function changeUsernameAction($user_id, Request $request)
    {
        $user = $this->loadUser($user_id);
        $model = new ChangeUsername($user);

        $form = $this->createForm(new ChangeUsernameType(), $model);

        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $oldUsername = $user->getUsername();

                $userManager = $this->get('fos_user.user_manager');
                $user->setUsername($model->getUsername());
                $userManager->updateUser($user);

                /** @var UsernameChanger $usernameChanger */
                $usernameChanger = $this->get('acl.userNameChanger');
                $usernameChanger->changeUsername($user, $oldUsername);

                return $this->redirect($this->generateUrl('_admin_index'));
            }
        }

        return array(
            'form' => $form->createView(),
            'user' => $user
        );
    }

    /**
     * download an exam file
     *
     * @Route("/download/{id}", name="_admin_download")
     */
    public function downloadAction($id)
    {
        $exam = $this->loadExam($id);

        /** @var ExcelExport $exporter */
        $exporter = $this->get('code_lovers.export.excel');

        $response = new Response($exporter->export($exam));

        $response->headers->set('Content-Type', 'application/vnd.ms-excel');
        $response->headers->set('Content-Disposition', 'attachment; filename=' . $this->createSaveFileName(sprintf("%s.xlsx", $exam)));

        return $response;
    }
}