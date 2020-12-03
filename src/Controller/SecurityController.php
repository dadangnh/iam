<?php

namespace App\Controller;

use ApiPlatform\Core\Api\IriConverterInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
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
    public function json_login(IriConverterInterface $iriConverter)
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
}
