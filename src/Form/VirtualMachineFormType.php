<?php declare(strict_types = 1);

namespace App\Form;

use App\Entity\SSHKey;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Contracts\Translation\TranslatorInterface;
use VirtualMachineBundle\Manager\ImageManagerInterface;
use VirtualMachineBundle\Manager\SizeManagerInterface;

class VirtualMachineFormType extends AbstractType
{

    /** @var SizeManagerInterface */
    private $sizeManager;

    /** @var ImageManagerInterface */
    private $imageManager;

    /** @var TranslatorInterface */
    private $translator;

    /** @var Security */
    private $security;

    public function __construct(SizeManagerInterface $sizeManager, ImageManagerInterface $imageManager, TranslatorInterface $translator, Security $security)
    {
        $this->sizeManager = $sizeManager;
        $this->imageManager = $imageManager;
        $this->security = $security;
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $sizes = $this->sizeManager->getAvailableSizes();
        $images = $this->imageManager->getAvailableImages();

        $sizeChoices = [];
        $imageChoices = [];

        foreach ($sizes as $size) {
            $label = sprintf('%sMB %svCPU %sGB', $size['memory'], $size['vcpus'], $size['disk']);
            $sizeChoices[$label] = $size['slug'];
        }

        foreach ($images as $image) {
            if ($image['slug'] === null) {
                continue;
            }

            $label = sprintf('%s %s', $image['distribution'], $image['name']);
            $imageChoices[$label] = $image['slug'];
        }

        /** @var User $user */
        $user = $this->security->getUser();
        $sshKeys = $user->getSSHKeys();
        $sshKeyChoices = [];
        
        /** @var SSHKey $sshKey */
        foreach($sshKeys as $sshKey) {
            $sshKeyChoices[$sshKey->getName()] = $sshKey->getId();
        }

        $builder
            ->add('name', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans('virtual_machine.create.validation.name.notBlank', [], 'form'),
                    ]),
                    new Regex([
                        'pattern' => "/^[a-z0-9]+$/i",
                        'message' => $this->translator->trans('virtual_machine.create.validation.name.noSpace', [], 'form'),
                    ]),
                    new Length([
                        'min' => 4,
                        'max' => 255,
                        'minMessage' => $this->translator->trans('virtual_machine.create.validation.name.length.minMessage', [], 'form'),
                        'maxMessage' => $this->translator->trans('virtual_machine.create.validation.name.length.maxMessage', [], 'form'),
                    ])
                ]
            ])
            ->add('size', ChoiceType::class, [
                'choices' => $sizeChoices,
            ])
            ->add('os', ChoiceType::class, [
                'choices' => $imageChoices,
            ])
            ->add('ssh_key', ChoiceType::class, [
                'choices' => $sshKeyChoices,
                'constraints' => [
                    new NotBlank([
                        'message' => $this->translator->trans('virtual_machine.create.validation.sshKey.notBlank', [], 'form'),
                    ])
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
    }

}
