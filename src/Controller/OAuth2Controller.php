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

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * OAuth2 controller.
 *
 * @Route("/oauth2")
 */
class OAuth2Controller extends AbstractController
{
    /**
     * OAuth2 callback URL for GitHub.
     *
     * @Route("/github", name="oauth2_github")
     */
    public function github(ClientRegistry $clientRegistry): Response
    {
        if ($this->getUser()) {
            return $this->forward(LoginController::class);
        }

        return $clientRegistry->getClient('github')->redirect(['user:email'], []);
    }

    /**
     * OAuth2 callback URL for Google.
     *
     * @Route("/google", name="oauth2_google")
     */
    public function google(ClientRegistry $clientRegistry): Response
    {
        if ($this->getUser()) {
            return $this->forward(LoginController::class);
        }

        return $clientRegistry->getClient('google')->redirect([], []);
    }
}
