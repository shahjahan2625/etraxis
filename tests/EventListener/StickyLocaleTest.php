<?php

//----------------------------------------------------------------------
//
//  Copyright (C) 2018 Artem Rodygin
//
//  You should have received a copy of the GNU General Public License
//  along with the file. If not, see <https://www.gnu.org/licenses/>.
//
//----------------------------------------------------------------------

namespace App\EventListener;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\AuthenticatorInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\UserPassportInterface;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

/**
 * @internal
 * @coversDefaultClass \App\EventListener\StickyLocale
 */
final class StickyLocaleTest extends WebTestCase
{
    /**
     * @covers ::getSubscribedEvents
     */
    public function testGetSubscribedEvents(): void
    {
        $expected = [
            LoginSuccessEvent::class,
            'kernel.request',
        ];

        self::assertSame($expected, array_keys(StickyLocale::getSubscribedEvents()));
    }

    /**
     * @covers ::saveLocale
     */
    public function testSaveLocale(): void
    {
        $doctrine     = self::getContainer()->get('doctrine');
        $requestStack = self::getContainer()->get('request_stack');
        $session      = self::getContainer()->get('session');

        /** @var User $user */
        $user = $doctrine->getRepository(User::class)->findOneBy(['email' => 'artem@example.com']);
        $user->setLocale('ru');

        $request = new Request();
        $request->setSession($session);

        $requestStack->push($request);

        $authenticator = $this->createMock(AuthenticatorInterface::class);

        $passport = $this->createMock(UserPassportInterface::class);
        $passport
            ->method('getUser')
            ->willReturn($user)
        ;

        $token = $this->createMock(TokenInterface::class);

        $event = new LoginSuccessEvent($authenticator, $passport, $token, $request, null, 'main');

        self::assertNull($session->get('_locale'));

        $object = new StickyLocale($requestStack, 'en');
        $object->saveLocale($event);

        self::assertSame('ru', $session->get('_locale'));
    }

    /**
     * @covers ::setLocale
     */
    public function testSetLocaleFromSession(): void
    {
        $requestStack = self::getContainer()->get('request_stack');
        $session      = self::getContainer()->get('session');

        $request = new Request();
        $request->setSession($session);
        $request->cookies->set($session->getName(), $session->getId());
        $session->set('_locale', 'ja');

        $requestStack->push($request);

        $event = new RequestEvent(self::$kernel, $request, HttpKernelInterface::MAIN_REQUEST);

        $object = new StickyLocale($requestStack, 'ru');
        $object->setLocale($event);

        self::assertSame('ja', $event->getRequest()->getLocale());
    }

    /**
     * @covers ::setLocale
     */
    public function testSetLocaleFromDefault(): void
    {
        $requestStack = self::getContainer()->get('request_stack');
        $session      = self::getContainer()->get('session');

        $request = new Request();
        $request->setSession($session);
        $request->cookies->set($session->getName(), $session->getId());

        $requestStack->push($request);

        $event = new RequestEvent(self::$kernel, $request, HttpKernelInterface::MAIN_REQUEST);

        $object = new StickyLocale($requestStack, 'ru');
        $object->setLocale($event);

        self::assertSame('ru', $event->getRequest()->getLocale());
    }
}
