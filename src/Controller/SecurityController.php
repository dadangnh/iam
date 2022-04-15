<?php

namespace App\Controller;

use App\Entity\User\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use JsonException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @var ManagerRegistry
     */
    private ManagerRegistry $doctrine;

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    public function __construct(ManagerRegistry $doctrine, EntityManagerInterface $entityManager) {
        $this->doctrine = $doctrine;
        $this->entityManager = $entityManager;
    }

    /**
     * Login page controller
     */
    #[Route('/login', name: 'login')]
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
            'page_title' => 'IAM Admin login',

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
            'username_parameter' => '_iam_username',

            // the 'name' HTML attribute of the <input> used for the password field (default: '_password')
            'password_parameter' => '_iam_password',

            // whether to enable or not the "forgot password?" link (default: false)
            'forgot_password_enabled' => false,

            // the path (i.e. a relative or absolute URL) to visit when clicking the "forgot password?" link (default: '#')
            // 'forgot_password_path' => $this->generateUrl('...', ['...' => '...']),

            // the label displayed for the "forgot password?" link (the |trans filter is applied to it)
            'forgot_password_label' => 'Forgot your password?',

            // whether to enable or not the "remember me" checkbox (default: false)
            'remember_me_enabled' => true,

            // remember me name form field (default: '_remember_me')
            'remember_me_parameter' => '_iam_remember_me',

            // whether to check by default the "remember me" checkbox (default: false)
            'remember_me_checked' => false,

            // the label displayed for the remember me checkbox (the |trans filter is applied to it)
            'remember_me_label' => 'Remember me',
        ]);
    }

    /**
     * Method for log out
     */
    #[Route('/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * @param Request $request
     * @param UserPasswordHasherInterface $passwordHasher
     * @return JsonResponse
     * @throws JsonException
     */
    #[Route('/api/change_user_password', name: 'app_change_password_old', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function change_password_old(Request $request,
                                        UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        return $this->change_password($request, $passwordHasher);
    }

    /**
     * @param Request $request
     * @param UserPasswordHasherInterface $passwordHasher
     * @return JsonResponse
     * @throws JsonException
     */
    #[Route('/api/users/change_password', name: 'app_change_password', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function change_password(Request $request,
                                    UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        // Make sure every active user can change their own password
        $this->denyAccessUnlessGranted('ROLE_USER');

        $content = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $username = $content['username'];
        $oldPassword = $content['old_password'];
        $newPassword = $content['new_password'];

        /** @var User $user */
        $user = $this->doctrine
            ->getRepository(User::class)
            ->findOneBy(['username' => $username]);

        if (null === $user) {
            return $this->json([
                'code' => 404,
                'error' => 'No user found'
            ], 404);
        }

        // Do a cross check so only the user can change their password
        $currentUser = $this->getUser();
        if (null === $currentUser || $currentUser->getUserIdentifier() !== $user->getUserIdentifier()) {
            return $this->json([
                'code' => 401,
                'error' => 'Invalid token access.'
            ], 401);
        }

        $checkPassword = $passwordHasher->isPasswordValid($user, $oldPassword);
        if ($checkPassword) {
            // TODO: check password strength and implement password blacklist
            $newPasswordEncoded = $passwordHasher->hashPassword($user, $newPassword);
            $user->setPassword($newPasswordEncoded);
            $this->entityManager->persist($user);
            $this->entityManager->flush();
            return $this->json([
                'code' => 200,
                'message' => 'password successfully changed.'
            ]);
        }

        return $this->json([
            'code' => 401,
            'error' => 'password invalid.'
        ], 401);
    }

    /**
     * @param Request $request
     * @param UserPasswordHasherInterface $passwordHasher
     * @return JsonResponse
     * @throws JsonException
     */
    #[Route('/api/change_password_by_sikka', name: 'app_change_password_by_sikka_old', methods: ['POST'])]
    #[Security("is_granted('ROLE_SUPER_ADMIN') or is_granted('ROLE_HRIS') or is_granted('ROLE_UPK_PUSAT') or is_granted('ROLE_UPK_WILAYAH') or is_granted('ROLE_UPK_LOKAL')")]
    public function change_password_by_sikka_old(Request $request,
                                                 UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        return $this->change_password_by_sikka($request, $passwordHasher);
    }

    /**
     * @param Request $request
     * @param UserPasswordHasherInterface $passwordHasher
     * @return JsonResponse
     * @throws JsonException
     */
    #[Route('/api/users/change_password_by_sikka', name: 'app_change_password_by_sikka', methods: ['POST'])]
    #[Security("is_granted('ROLE_SUPER_ADMIN') or is_granted('ROLE_HRIS') or is_granted('ROLE_UPK_PUSAT') or is_granted('ROLE_UPK_WILAYAH') or is_granted('ROLE_UPK_LOKAL')")]
    public function change_password_by_sikka(Request $request,
                                             UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        // this endpoint should only used by UPK/ HRIS/ SUPER ADMIN to reset user password
        if (!$this->isGranted('ROLE_SUPER_ADMIN')
            && !$this->isGranted('ROLE_HRIS')
            && !$this->isGranted('ROLE_UPK_PUSAT')
            && !$this->isGranted('ROLE_UPK_WILAYAH')
            && !$this->isGranted('ROLE_UPK_LOKAL')
        ) {
            return $this->json([
                'code' => 401,
                'error' => 'Unauthorized API access.',
            ], 401);
        }

        $content = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $username = $content['username'];
        $newPassword = $content['new_password'];
        $user = $this->doctrine
            ->getRepository(User::class)
            ->findOneBy(['username' => $username]);

        if (null === $user) {
            return $this->json([
                'code' => 404,
                'error' => 'No user found'
            ], 404);
        }

        // TODO: check password strength and implement password blacklist
        $newPasswordEncoded = $passwordHasher->hashPassword($user, $newPassword);
        $user->setPassword($newPasswordEncoded);
        $this->entityManager->persist($user);
        $this->entityManager->flush();
        return $this->json([
            'code' => 200,
            'message' => 'password successfully changed.'
        ]);
    }

    /**
     * @return JsonResponse
     */
    #[Route('/api/whoami', name: 'app_whoami_old', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function whoamiOld(): JsonResponse
    {
        return $this->json($this->getUser());
    }

    /**
     * @return JsonResponse
     */
    #[Route('/api/token/whoami', name: 'app_whoami', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function whoami(): JsonResponse
    {
        return $this->json($this->getUser());
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws JsonException
     */
    #[Route('/api/users/check_identifier', name: 'app_check_user_identifier', methods: ['POST'])]
    #[IsGranted('PUBLIC_ACCESS')]
    public function checkValidUserIdentifier(Request $request): JsonResponse
    {
        $content = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $username = $content['username'];

        /** @var User $user */
        $user = $this->doctrine
            ->getRepository(User::class)
            ->findOneBy(['username' => $username]);

        if (null === $user) {
            return $this->json([
                'code' => 404,
                'error' => 'No user found'
            ], 404);
        }

        if ($user instanceof User) {
            return $this->json([
                'code' => 200,
                'message' => sprintf(
                    'User: %s found.',
                    $username
                )
            ]);
        }

        return $this->json([
            'code' => 404,
            'error' => 'No user found'
        ], 404);
    }
}
