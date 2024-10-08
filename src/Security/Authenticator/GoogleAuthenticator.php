<?php
namespace App\Security\Authenticator;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use League\OAuth2\Client\Token\AccessToken;
use KnpU\OAuth2ClientBundle\Client\OAuth2ClientInterface;

/*
Documentation used to create the authenticator: https://github.com/knpuniversity/oauth2-client-bundle?tab=readme-ov-file
*/
class GoogleAuthenticator extends AbstractOAuthAuthenticator
{
    protected string $providerName = 'google';
    protected string $clientName = 'google_main';

    protected function getOrCreateUser(AccessToken $accessToken, OAuth2ClientInterface $client, EntityManagerInterface $entityManager): User
    {
        $googleUser = $client->fetchUserFromToken($accessToken);

        $email = $googleUser->getEmail();

        // 1) have they logged in with google before? Easy!
        $existingUser = $entityManager->getRepository(User::class)->findOneBy(['OAuth2ProviderId' => $googleUser->getId(), 'OAuth2ProviderName' => 'google']);

        if ($existingUser) {
            return $existingUser;
        }

        // 2) do we have a matching user by email?
        $existingUser = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

        if($existingUser)
        {
            return $existingUser;
        }


        // 3) If we went through step 1 and 2, then it is a new User and we're gonna create it
        // a User object
        $newUser = new User();
        $newUser->setEmail($email);
        $newUser->setOAuth2ProviderName($this->providerName);
        $newUser->setOAuth2ProviderId($googleUser->getId());
        $newUser->setVerified(true);
        $newUser->setPassword('');
        $entityManager->persist($newUser);
        $entityManager->flush();

        return $newUser;
    }
}