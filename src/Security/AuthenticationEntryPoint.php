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

namespace App\Security;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;

/**
 * Authentication entry point.
 */
class AuthenticationEntryPoint implements AuthenticationEntryPointInterface
{
    protected UrlGeneratorInterface $urlGenerator;

    /**
     * @codeCoverageIgnore Dependency Injection constructor
     */
    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * {@inheritDoc}
     */
    public function start(Request $request, AuthenticationException $authException = null): Response
    {
        return $request->isXmlHttpRequest()
            ? new JsonResponse($authException ? $authException->getMessage() : null, Response::HTTP_UNAUTHORIZED)
            : new RedirectResponse($this->urlGenerator->generate('login'));
    }
}
