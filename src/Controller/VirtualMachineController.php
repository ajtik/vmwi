<?php declare(strict_types = 1);

namespace App\Controller;

use App\Entity\User;
use App\Form\VirtualMachineFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use VirtualMachineBundle\Manager\VirtualMachineManagerInterface;
use VirtualMachineBundle\Repository\VirtualMachineRepositoryInterface;

/**
 * @Route("/admin/machines")
 */
class VirtualMachineController extends AbstractController
{

    /**
     * @Route(name="app_machines")
     */
    public function index(VirtualMachineRepositoryInterface $virtualMachineRepository, VirtualMachineManagerInterface $virtualMachineManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        /** @var User */
        $user = $this->getUser();
        $checkedMachines = [];

        $uncheckedMachines = $virtualMachineRepository->findBy([
            'user' => $user->getId()
        ]);

        foreach($uncheckedMachines as $machine) {
            $checkedMachines[] = $virtualMachineManager->checkStatus($machine);
        }

        return $this->render('virtual_machine/index.html.twig', [
            'machines' => $checkedMachines,
        ]);
    }

    /**
     * @Route("/create", name="app_machines_create")
     */
    public function create(Request $request, VirtualMachineManagerInterface $virtualMachineManager, TranslatorInterface $translator): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $form = $this->createForm(VirtualMachineFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $virtualMachine = $virtualMachineManager->create($data['name'], $data['size'], $data['os'], $data['ssh_key']);

            $this->addFlash('success', $translator->trans('virtual_machine.create.success.message', [], 'form'));

            return $this->redirectToRoute('app_machines');
        }

        $formErrors = $form->getErrors(true);

        if ($formErrors->count() !== 0) {
            foreach ($formErrors as $formError) {
                $this->addFlash('danger', $formError->getMessage());
            }
        }

        return $this->render('virtual_machine/create.html.twig', [
            'virtualMachineCreateForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/restart/{id}", name="app_machines_restart")
     */
    public function restart(
        int $id,
        VirtualMachineManagerInterface $virtualMachineManager,
        VirtualMachineRepositoryInterface $virtualMachineRepository, 
        TranslatorInterface $translator): Response
    {
        $virtualMachine = $virtualMachineRepository->find($id);

        $this->denyAccessUnlessGranted('MACHINE_RESTART', $virtualMachine);

        $virtualMachineManager->restart($virtualMachine);

        $this->addFlash('success', $translator->trans('virtual_machines.restart.success', [], 'pages'));

        return $this->redirectToRoute('app_machines');
    }

    /**
     * @Route("/start/{id}", name="app_machines_start")
     */
    public function start(
        int $id,
        VirtualMachineManagerInterface $virtualMachineManager,
        VirtualMachineRepositoryInterface $virtualMachineRepository, 
        TranslatorInterface $translator): Response
    {
        $virtualMachine = $virtualMachineRepository->find($id);

        $this->denyAccessUnlessGranted('MACHINE_START', $virtualMachine);

        $virtualMachineManager->start($virtualMachine);

        $this->addFlash('success', $translator->trans('virtual_machines.start.success', [], 'pages'));

        return $this->redirectToRoute('app_machines');
    }

    /**
     * @Route("/stop/{id}", name="app_machines_stop")
     */
    public function stop(
        int $id,
        VirtualMachineManagerInterface $virtualMachineManager,
        VirtualMachineRepositoryInterface $virtualMachineRepository, 
        TranslatorInterface $translator): Response
    {
        $virtualMachine = $virtualMachineRepository->find($id);

        $this->denyAccessUnlessGranted('MACHINE_STOP', $virtualMachine);

        $virtualMachineManager->stop($virtualMachine);

        $this->addFlash('success', $translator->trans('virtual_machines.stop.success', [], 'pages'));

        return $this->redirectToRoute('app_machines');
    }

    /**
     * @Route("/delete/{id}", name="app_machines_delete")
     */
    public function delete(
        int $id,
        VirtualMachineManagerInterface $virtualMachineManager,
        VirtualMachineRepositoryInterface $virtualMachineRepository, 
        TranslatorInterface $translator): Response
    {
        $virtualMachine = $virtualMachineRepository->find($id);

        $this->denyAccessUnlessGranted('MACHINE_DELETE', $virtualMachine);

        $virtualMachineManager->delete($virtualMachine);

        $this->addFlash('success', $translator->trans('virtual_machines.delete.success', [], 'pages'));

        return $this->redirectToRoute('app_machines');
    }

}
