<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Endroid\QrCode\Builder\Builder;
use Scheb\TwoFactorBundle\Model\Totp\TwoFactorInterface;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\Totp\TotpAuthenticator;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\Totp\TotpAuthenticatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SecurityController extends AbstractController
{
    #[Route('/logout', name: 'logout', methods: ['GET'])]
    public function logout(): void
    {
        throw new \Exception('Don\'t forget to activate logout in security.yaml');
    }

    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    #[Route('/authentication/2fa/enable', name: 'app_2fa_enable')]
    public function enable2fa(TotpAuthenticator $totpAuthenticator, EntityManagerInterface $entityManager)
    {
        /** @var User $user */
        $user = $this->getUser();

        if (!$user->isTotpAuthenticationEnabled()) {
            $user->setTotpSecret($totpAuthenticator->generateSecret());
            $entityManager->flush();
        }

        dd($totpAuthenticator->getQRContent($user));
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/authentication/2fa/qr-code', name: 'app_qr_code')]
    public function displayGoogleAuthenticatorQrCode(TotpAuthenticatorInterface $totpAuthenticator)
    {
        //$qrCode = $qrCodeGenerator->getTotpQrCode($this->getUser())
        /** @var TwoFactorInterface $user */
        $user = $this->getUser();
        $qrCodeContent = $totpAuthenticator->getQrContent($user);
        //dd($qrCodeContent);
        $result = Builder::create()
            ->data($qrCodeContent)->build();

        dd($result);

        return new Response($result->getString(), 200, ['Content-Type' => 'image/png']);
    }
}