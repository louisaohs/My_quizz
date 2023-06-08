<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use App\Repository\UserRepository;
use App\Service\JWTService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mime\Address;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;

class SecurityController extends AbstractController
{
    // /**
    //  * @Route("/inscription", name="security_inscription")
    //  */
    public function register(Request $request,  ManagerRegistry $manager, PasswordHasherFactoryInterface $hasher, VerifyEmailHelperInterface $verifyEmailHelper, JWTService $jwt, MailerInterface $mailer)
    {
        $user = new User();

        $form = $this->createForm(RegisterType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $factory = $hasher->getPasswordHasher($user);

            $confim_password = $factory->hash(plainPassword: $user->getConfirmPassword());

            $user->setConfirmPassword($confim_password);

            $password = $factory->hash(plainPassword: $user->getPassword());

            $user->setPassword($password);

            // sauvegarder l'utilisateur :
            $em = $manager->getManager();
            $em->persist($user);
            $em->flush();

            $signatureComponents = $verifyEmailHelper->generateSignature(
                'app_verify_email',
                $user->getId(),
                $user->getEmail(),
                ['id' => $user->getId()]
            );
            // On génère le JWT de l'utilisateur
            // On crée le Header
            $header = [
                'typ' => 'JWT',
                'alg' => 'HS256'
            ];

            // On crée le Payload
            $payload = [
                'user_id' => $user->getId()
            ];
            $token = $jwt->generate($header, $payload, $this->getParameter('app.jwtsecret'));

            $email = (new TemplatedEmail())
                ->to($user->getEmail())
                ->from(new Address('mailtrap@example.com', 'Mailtrap'))
                ->subject('Salut à toi')
                ->subject('Best practices of building HTML emails')
                ->htmlTemplate('emails/verifEmail.html.twig')
                ->context(['token' => $token]);

            $mailer->send($email);
            $this->addFlash('success', 'Email envoyer' . $signatureComponents->getSignedUrl());

            return $this->redirectToRoute('index');
        }

        // --------------------

        return $this->render('security/register.html.twig', ['form' => $form->createView()]);
    }

    #[Route(path: '/login', name: 'app_login')]

    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout')]

    public function logout(): void
    {
        // throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    public function account()
    {
        return $this->render('security/account_user.html.twig');
    }

    #[Route(path: "/verif/{token}", name: "app_verify_email", requirements: ["token" => ".+"])]
    public function verifyUserEmail($token, UserRepository $userRepository, EntityManagerInterface $entityManager, JWTService $jwt): Response
    {
        if ($jwt->isValid($token) && !$jwt->isExpired($token) && $jwt->check($token, $this->getParameter('app.jwtsecret'))) {

            $payload = $jwt->getPayload($token);

            $user = $userRepository->find($payload['user_id']);

            if (!$user) {
                throw $this->createNotFoundException();
            }
            $user->setIsVerified(true);
            $entityManager->flush();
            $this->addFlash('success', 'Account Verified! You can now log in.');
            return $this->redirectToRoute('app_login');
        }
        $this->addFlash('danger', 'Le token est invalide ou a expiré');
        return $this->redirectToRoute('app_login');
    }
}
