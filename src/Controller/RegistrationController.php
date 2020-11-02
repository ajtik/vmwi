<?php declare(strict_types = 1);

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegistrationController extends AbstractController
{

    /** @var string */
    private $appEmailAddress;

    /** @var string */
    private $appEmailAddressName;

    /** @var MailerInterface */
    private $mailer;

    public function __construct(string $appEmailAddress, string $appEmailAddressName, MailerInterface $mailer)
    {
        $this->appEmailAddress = $appEmailAddress;
        $this->appEmailAddressName = $appEmailAddressName;
        $this->mailer = $mailer;
    }

    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, TranslatorInterface $translator): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $this->mailer->send(
                (new TemplatedEmail())
                    ->from(new Address($this->appEmailAddress, $this->appEmailAddressName))
                    ->to($user->getEmail())
                    ->subject($translator->trans('confirmation.subject', [], 'email'))
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            );

            $this->addFlash('success', $translator->trans('registration.success', [], 'form'));

            return $this->redirectToRoute('app_login');
        }

        $formErrors = $form->getErrors(true);

        if ($formErrors->count() !== 0) {
            foreach ($formErrors as $formError) {
                $this->addFlash('danger', $formError->getMessage());
            }
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

}
