<?php declare(strict_types=1);
/******************************************************************************/
/*                                  CASBUNDLE                                 */
/*     Auteur: Tristan Fleury - https://github.com/viduc - viduc@mail.fr      */
/*                              Licence: Apache-2.0                           */
/******************************************************************************/

namespace Viduc\CasBundle\Security;

use \phpcas;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class CasAuthenticator extends AbstractGuardAuthenticator
{
    private $casVersion;
    private $casHost;
    private $casPort;
    private $casUri;
    private $failMessage;

    public function __construct(array $config)
    {
        $this->casVersion = $config['version'];
        $this->casHost = $config['host'];
        $this->casPort = $config['port'];
        $this->casUri = $config['uri'];
        $this->failMessage = "Aucun utilisateur trouvé";
    }

    /**
     * @inheritDoc
     */
    public function start(
        Request $request,
        AuthenticationException $authException = null
    ) {
        $data = array(
            'message' => 'Authentication Required'
        );

        return new JsonResponse($data, 401);
    }

    /**
     * @inheritDoc
     */
    public function supports(Request $request)
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function getCredentials(Request $request)
    {
        \phpCAS::setDebug();
        \phpCAS::setVerbose(true);
        if (!\phpCAS::isInitialized()) {
            \phpCAS::client(
                $this->casVersion,
                $this->casHost,
                $this->casPort,
                $this->casUri
            );
        }
        \phpCAS::setNoCasServerValidation();
        \phpCAS::forceAuthentication();

        return array_merge(
            ['username' => phpCAS::getUser()],
            phpCAS::getAttributes()
        );
    }

    /**
     * @inheritDoc
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        if (!$userProvider instanceof UserProvider) {
            return;
        }

        try {
            return $userProvider->loadUserByUsername($credentials['username']);
        }
        catch (UsernameNotFoundException $e) {
            throw new CustomUserMessageAuthenticationException(
                $this->failMessage
            );
        }
    }

    /**
     * @inheritDoc
     */
    public function checkCredentials($credentials, UserInterface $user)
    {
        if ($user) {
            return true;
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function onAuthenticationFailure(
        Request $request,
        AuthenticationException $exception
    ) {

    }

    /**
     * @inheritDoc
     */
    public function onAuthenticationSuccess(
        Request $request,
        TokenInterface $token,
        $providerKey
    ) {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function supportsRememberMe()
    {
        return false;
    }
}
