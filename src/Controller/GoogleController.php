<?php
namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;

class GoogleController extends AbstractController
{
    #[Route('/connect/google', name: 'connect_google_start')]
    public function connectAction(ClientRegistry $clientRegistry)
    {
        // will redirect to google!
        return $clientRegistry
            ->getClient('google_main') // key used in config/packages/knpu_oauth2_client.yaml
            ->redirect([
                'email'/*,'public_profile'*/ // the scopes you want to access
            ], []);
    }

    /**
     * After going to google, you're redirected back here
     * because this is the "redirect_route" you configured
     * in config/packages/knpu_oauth2_client.yaml
     */
    #[Route('/connect/google/check', name: 'connect_google_check')]
    public function connectCheckAction()
    {
        return new Response(200);
    }
}