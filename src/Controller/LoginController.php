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
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Login controller.
 */
class LoginController extends AbstractController
{
    /**
     * Login page.
     *
     * @Route("/login", name="login")
     */
    public function index(AuthenticationUtils $utils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('homepage');
        }

        return $this->render('security/login.html.twig', [
            'error'    => $utils->getLastAuthenticationError(),
            'username' => $utils->getLastUsername(),
        ]);
    }
}
