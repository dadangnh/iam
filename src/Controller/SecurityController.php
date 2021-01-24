<?php

namespace App\Controller;

use ApiPlatform\Core\Api\IriConverterInterface;
use App\Entity\User\User;
use Doctrine\ORM\EntityManagerInterface;
use JsonException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/login", name="app_login")
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        // If user is admin and fully authenticated, redirect to admin page
        if ($this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('admin');
        }

        // if user is not admin, log out
        if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('app_logout');
        }

        return $this->render('@EasyAdmin/page/login.html.twig', [
            // parameters usually defined in Symfony login forms
            'error' => $error,
            'last_username' => $lastUsername,

            // OPTIONAL parameters to customize the login form:

            // the translation_domain to use (define this option only if you are
            // rendering the login template in a regular Symfony controller; when
            // rendering it from an EasyAdmin Dashboard this is automatically set to
            // the same domain as the rest of the Dashboard)
            'translation_domain' => 'admin',

            // the title visible above the login form (define this option only if you are
            // rendering the login template in a regular Symfony controller; when rendering
            // it from an EasyAdmin Dashboard this is automatically set as the Dashboard title)
            'page_title' => 'DJPConnect Admin login',

            // the string used to generate the CSRF token. If you don't define
            // this parameter, the login form won't include a CSRF token
            'csrf_token_intention' => 'authenticate',

            // the URL users are redirected to after the login (default: '/admin')
            'target_path' => $this->generateUrl('admin'),

            // the label displayed for the username form field (the |trans filter is applied to it)
            'username_label' => 'Your username',

            // the label displayed for the password form field (the |trans filter is applied to it)
            'password_label' => 'Your password',

            // the label displayed for the Sign In form button (the |trans filter is applied to it)
            'sign_in_label' => 'Log in',

            // the 'name' HTML attribute of the <input> used for the username field (default: '_username')
            'username_parameter' => 'username',

            // the 'name' HTML attribute of the <input> used for the password field (default: '_password')
            'password_parameter' => 'password',
        ]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * @Route("/json_login", name="app_json_login", methods={"POST"})
     * @param IriConverterInterface $iriConverter
     * @return Response
     */
    public function json_login(IriConverterInterface $iriConverter): Response
    {
        return $this->json([
            'user' => $this->getUser() ? $iriConverter->getIriFromItem($this->getUser()) : null,
            'username' => $this->getUser() ? $this->getUser()->getUsername() : null,
            'role' => $this->getUser() ? $this->getUser()->getRoles() : null,
            'pegawai' => $this->getUser()->getPegawai() ? $iriConverter->getIriFromItem($this->getUser()->getPegawai()) : null,
            'nama' => $this->getUser()->getPegawai() ? $this->getUser()->getPegawai()->getNama() : null,
            'nip9' => $this->getUser()->getPegawai() ? $this->getUser()->getPegawai()->getNip9() : null,
            'nip18' => $this->getUser()->getPegawai() ? $this->getUser()->getPegawai()->getNip18() : null,
        ]);
    }

    /**
     * @Route("/api/change_user_password", name="app_change_password", methods={"POST"})
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return JsonResponse
     * @throws JsonException
     */
    public function change_password(Request $request, UserPasswordEncoderInterface $passwordEncoder): JsonResponse
    {
        if (!$this->isGranted('ROLE_APLIKASI')) {
            return $this->json([
                'message' => 'Unauthorized API access.',
                'request' => $request
            ]);
        }

        $content = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $username = $content['username'];
        $oldPassword = $content['old_password'];
        $newPassword = $content['new_password'];
        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['username' => $username]);
        if (null === $user) {
            return $this->json(['error' => 'No user found']);
        }

        $checkPassword = $passwordEncoder->isPasswordValid($user, $oldPassword);
        if ($checkPassword) {
            // TODO: check password strength and implement password blacklist
            $newPasswordEncoded = $passwordEncoder->encodePassword($user, $newPassword);
            $user->setPassword($newPasswordEncoded);
            $this->entityManager->persist($user);
            $this->entityManager->flush();
            return $this->json(['message' => 'password successfully changed.']);
        }

        return $this->json(['error' => 'password invalid.']);
    }

    /**
     * @Route("/api/whoami", name="app_whoami", methods={"POST"})
     * @return JsonResponse
     */
    public function whoami(): JsonResponse
    {
        return $this->json($this->getUser());
    }
}
