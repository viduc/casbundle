<?php


namespace Viduc\CasBundle\Security;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\CustomCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;

class CasAuthenticator extends AbstractAuthenticator implements AuthenticationEntryPointInterface
{
    private UrlGeneratorInterface $urlGenerator;
    private SessionInterface $session;

    public function __construct(
        array $config,
        UrlGeneratorInterface $urlGenerator,
        SessionInterface $session
    ) {
        $this->urlGenerator = $urlGenerator;
        $this->session = $session;
        if (!\phpCAS::isInitialized()) {
            \phpCAS::client(
                $config['version'],
                $config['host'],
                $config['port'],
                $config['uri']
            );
            \phpCAS::setNoCasServerValidation();
        }
        $this->chargerUtilisateurEtq();
    }


    public function chargerUtilisateurEtq(): void
    {
        $user = new User();
        $user->setUsername('test');
        $tab[] = $user;
        $user = new User();
        $user->setUsername('toto');
        $tab[] = $user;
        $user = new User();
        $user->setUsername('tutu');
        $tab[] = $user;
        $this->session->set('enTantQue.users', $tab);
    }

    /**
     * @inheritDoc
     */
    public function supports(Request $request): ?bool
    {
        return $this->session->has('viduc_cas_username');
    }

    /**
     * @inheritDoc
     */
    public function authenticate(Request $request): PassportInterface
    {
        return new Passport(
            new UserBadge($this->session->get('viduc_cas_username')),
            new CustomCredentials(
                function ($credentials, UserInterface $user) {
                    return \phpCAS::isAuthenticated();
                },
                // The custom credentials
                ''
        ));
    }

    /**
     * @inheritDoc
     */
    public function onAuthenticationSuccess(
        Request $request,
        TokenInterface $token,
        string $firewallName
    ): ?Response {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function onAuthenticationFailure(
        Request $request,
        AuthenticationException $exception
    ): ?Response {
        return null;
    }

    public function start(
        Request $request,
        AuthenticationException $authException = null
    ) {
        \phpCAS::forceAuthentication();
        $this->session->set('viduc_cas_username', \phpCAS::getUser());
        return new RedirectResponse(
            $this->urlGenerator->generate($request->attributes->get('_route'))
        );
    }
}