<?php declare(strict_types = 1);

namespace App\Controller;

use App\Form\SSHKeyFormType;
use App\Form\UserFormType;
use App\Repository\UserRepository;
use DigitalOceanBundle\Repository\SSHKeyRepository;
use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use VirtualMachineBundle\Entity\SSHKey;
use VirtualMachineBundle\Manager\SSHKeyManagerInterface;
use VirtualMachineBundle\Repository\SSHKeyRepositoryInterface;

/**
 * @Route("/admin/users")
 */
class UserController extends AbstractController
{

    /**
     * @Route(name="app_users")
     */
    public function index(UserRepository $userRepository)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $users = $userRepository->findAll();

        return $this->render('user/index.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_users_edit")
     */
    public function edit(
        int $id,
        UserRepository $userRepository,
        Request $request,
        UserPasswordEncoderInterface $passwordEncoder,
        EntityManagerInterface $entityManager,
        TranslatorInterface $translator
    ): Response
    {
        $userToEdit = $userRepository->find($id);

        $this->denyAccessUnlessGranted('USER_EDIT', $userToEdit);

        $form = $this->createForm(UserFormType::class, null, [
            'data' => [
                'user' => $userToEdit,
            ],
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();

            if ($formData['password'] !== null) {
                $userToEdit->setPassword($passwordEncoder->encodePassword($userToEdit, $formData['password']));
                $this->addFlash('success', $translator->trans('user.edit.success.message', [], 'form'));
            }

            if (isset($formData['isActive'])) {
                $userToEdit->setActive((bool) $formData['isActive']);
            }

            $entityManager->flush();
        }

        $formErrors = $form->getErrors(true);

        if ($formErrors->count() !== 0) {
            foreach ($formErrors as $formError) {
                $this->addFlash('danger', $formError->getMessage());
            }
        }

        $userSshKeys = $userToEdit->getSSHKeys();

        return $this->render('user/edit.html.twig', [
            'userEditForm' => $form->createView(),
            'sshKeys' => $userSshKeys,
            'userToEdit' => $userToEdit
        ]);
    }

    /**
     * @Route("/{id}/sshkey/add", name="app_users_sshkey_add")
     */
    public function sshKeyAdd(
        int $id, 
        Request $request, 
        UserRepository $userRepository, 
        TranslatorInterface $translator,
        SSHKeyManagerInterface $sshKeyManager): Response
    {
        $user = $userRepository->find($id);

        $sshKey = new SSHKey();
        $form = $this->createForm(SSHKeyFormType::class, $sshKey);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            try {
                $sshKey = $sshKeyManager->create($sshKey);
                $user->addSSHKey($sshKey);
                $manager = $this->getDoctrine()->getManager();
                $manager->flush();
                $this->addFlash('success', $translator->trans('sshkey.add.success.message', [], 'form'));
            } catch(Exception $e) {
                $this->addFlash('danger', $translator->trans('sshkey.add.fail.message', [], 'form'));
            }

            return $this->redirectToRoute('app_users_edit', ['id' => $user->getId()]);
        }

        return $this->render('user/sshkey/add.html.twig', [
            'sshKeyAddForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/sshkey/{keyId}/edit", name="app_users_sshkey_edit")
     */
    public function sshKeyEdit(
        int $id, 
        int $keyId, 
        Request $request, 
        UserRepository $userRepository,
        SSHKeyRepositoryInterface $sshKeyRepository,
        TranslatorInterface $translator): Response
    {
        $user = $userRepository->find($id);
        $sshKey = $sshKeyRepository->find($keyId);

        $this->denyAccessUnlessGranted('KEY_EDIT', $sshKey);

        $form = $this->createForm(SSHKeyFormType::class, $sshKey);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager = $this->getDoctrine()->getManager();
            $manager->flush();

            $this->addFlash('success', $translator->trans('sshkey.edit.success.message', [], 'form'));

            return $this->redirectToRoute('app_users_edit', ['id' => $user->getId()]);
        }

        return $this->render('user/sshkey/edit.html.twig', [
            'sshKeyEditForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}/sshkey/{keyId}/delete", name="app_users_sshkey_delete")
     */
    public function sshKeyRemove(
        int $id, 
        int $keyId, 
        UserRepository $userRepository, 
        SSHKeyRepositoryInterface $sshKeyRepository,
        SSHKeyManagerInterface $sshKeyManager,
        TranslatorInterface $translator): Response
    {
        $user = $userRepository->find($id);
        $sshKey = $sshKeyRepository->find($keyId);

        $user->removeSSHKey($sshKey);

        try {
            $sshKeyManager->delete($sshKey);
            $this->addFlash('success', $translator->trans('sshkey.delete.success.message', [], 'form'));
        } catch(ForeignKeyConstraintViolationException $e) {
            $this->addFlash('danger', $translator->trans('sshkey.delete.fail.message', [], 'form'));
        }

        return $this->redirectToRoute('app_users_edit', ['id' => $user->getId()]);
    }
}
