<?php
namespace App\Security\Authenticator;

use App\Repository\UserRepository;
use App\Entity\User; // your user entity
use Doctrine\ORM\EntityManagerInterface;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Provider\GoogleUser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Component\HttpFoundation\RedirectResponse;
use KnpU\OAuth2ClientBundle\Client\OAuth2ClientInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use KnpU\OAuth2ClientBundle\Security\Authenticator\OAuth2Authenticator;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

/*
Documentation used to create the authenticator: https://github.com/knpuniversity/oauth2-client-bundle?tab=readme-ov-file
*/
abstract class AbstractOAuthAuthenticator extends OAuth2Authenticator implements AuthenticationEntrypointInterface
{
    protected string $providerName = '';

    protected string $clientName = '';

    private $clientRegistry;
    private $entityManager;
    private $router;

    public function __construct(ClientRegistry $clientRegistry, EntityManagerInterface $entityManager, RouterInterface $router)
    {
        $this->clientRegistry = $clientRegistry;
        $this->entityManager = $entityManager;
        $this->router = $router;
    }

    public function supports(Request $request): ?bool
    {
        // continue ONLY if the current ROUTE matches the check ROUTE
        return $request->attributes->get('_route') === 'connect_'.$this->providerName.'_check';
    }

    abstract protected function getOrCreateUser(AccessToken $accessToken, OAuth2ClientInterface $client, EntityManagerInterface $entityManager): User;

    public function authenticate(Request $request): Passport
    {
        $client = $this->clientRegistry->getClient($this->clientName);
        $accessToken = $this->fetchAccessToken($client);

        return new SelfValidatingPassport(
            userBadge: new UserBadge($this->getOrCreateUser($accessToken, $client, $this->entityManager)->getUserIdentifier()),
            badges: [
                new RememberMeBadge()
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        $targetUrl = $this->router->generate('app_homepage');

        return new RedirectResponse($targetUrl);
    
        // or, on success, let the request continue to be handled by the controller
        //return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $message = strtr($exception->getMessageKey(), $exception->getMessageData());

        return new Response($message, Response::HTTP_FORBIDDEN);
    }
    
   /**
     * Called when authentication is needed, but it's not sent.
     * This redirects to the 'login'.
     */
    public function start(Request $request, AuthenticationException $authException = null): Response
    {
        return new RedirectResponse(
            '/connect/', // might be the site, where users choose their oauth provider
            Response::HTTP_TEMPORARY_REDIRECT
        );
    }
}