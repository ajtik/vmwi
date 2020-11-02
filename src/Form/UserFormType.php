<?php declare(strict_types = 1);

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserFormType extends AbstractType
{

    /** @var TranslatorInterface */
    private $translator;

    /** @var Security */
    private $security;

    public function __construct(TranslatorInterface $translator, Security $security)
    {
        $this->translator = $translator;
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var User */
        $user = $options['data']['user'];

        if (in_array('ROLE_ADMIN', $this->security->getUser()->getRoles())) {
            $builder->add('isActive', ChoiceType::class, [
                'choices' => [
                    $this->translator->trans('user.edit.input.status.inactive', [], 'form') => 0,
                    $this->translator->trans('user.edit.input.status.active', [], 'form') => 1,
                ],
                'choice_attr' => function ($choice, $key, $value) use ($user) {
                    return (bool) $choice === $user->isActive() ? ['selected' => ''] : [];
                },
            ]);
        }

        $builder
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'required' => false,
                'invalid_message' => $this->translator->trans('user.edit.validation.password.not_same', [], 'form'),
                'constraints' => [
                    new Length([
                        'min' => 6,
                        'minMessage' => $this->translator->trans('user.edit.validation.password.length.min', ['%min%' => 6], 'form'),
                        'max' => 4096,
                    ]),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
    }

}
