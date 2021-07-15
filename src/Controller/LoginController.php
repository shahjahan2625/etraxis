<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2018 Artem Rodygin
//
//  This file is part of eTraxis.
//
//  You should have received a copy of the GNU General Public License
//  along with eTraxis. If not, see <https://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Http\Authenticator\Token\PostAuthenticationToken;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

/**
 * Login controller.
 */
class LoginController extends AbstractController
{
    use TargetPathTrait;

    /**
     * Login page.
     *
     * @Route("/login", name="login")
     */
    public function index(Request $request, TokenStorageInterface $tokenStorage, AuthenticationUtils $utils): Response
    {
        if ($this->getUser()) {
            /** @var PostAuthenticationToken $token */
            $token        = $tokenStorage->getToken();
            $firewallName = $token->getFirewallName();
            $targetPath   = $this->getTargetPath($request->getSession(), $firewallName);

            return new RedirectResponse($targetPath ?? $this->generateUrl('homepage'));
        }

        return $this->render('security/login.html.twig', [
            'error'    => $utils->getLastAuthenticationError(),
            'username' => $utils->getLastUsername(),
        ]);
    }
}
