<?php
namespace App\Module\ProfileModule\Controller;

use App\Controller\BootController;
use App\Module\ConditionModule\Repository\ReportRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Module\ProfileModule\Service\SiteMapService;
use App\Module\ProfileModule\Form\ChangePasswordType;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('IS_AUTHENTICATED_FULLY')]
class ProfileController extends BootController
{
    public function __construct(
        protected SiteMapService $siteMapService
    ){}

    #[Route('/profil', name: 'app_profile', methods: ['GET', 'POST'])]
    public function indexProfile(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->addBreadcrumb('Accueil', '/');
        $this->addBreadcrumb('Profil', null);

        $user = $this->getUser();
        $form = $this->createForm(ChangePasswordType::class);
        
        // Handle the form submission
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            
            // Check if old password is correct
            if (password_verify($data['old_password'], $user->getPassword())) {
                // Check if new password and confirm password match
                if ($data['new_password'] === $data['confirm_password']) {
                    // Update the password (hash it)
                    $user->setPassword(password_hash($data['new_password'], PASSWORD_BCRYPT));

                    $entityManager->persist($user);
                    $entityManager->flush();

                    // Adding flash message to the user
                    $this->addFlash('success', 'Mot de passe mis à jour.');
                } else {
                    $this->addFlash('error', 'New password and confirmation do not match.');
                }
            } else {
                $this->addFlash('error', 'L\'ancien mot de passe est incorrect.');
            }

            // Redirect to the same page
            return $this->redirectToRoute('app_profile');
        }

        return $this->render('pages/profile.html.twig', [
            'form' => $form->createView(), // Pass the form view to the template
        ]);
    }

    #[Route('/profil/reports', name: 'app_reports', methods: ['GET'])]
    public function indexProfileReports(ReportRepository $reportRepository): Response
    {
        $this->addBreadcrumb('Accueil', '/');
        $this->addBreadcrumb('Profil', null);

        return $this->render('pages/reports.html.twig', [
            'reports' => $reportRepository->findBy(['user'=>$this->getUser()]),
        ]);
    }

    
    #[Route('/profil/data', name: 'app_data', methods: ['GET'])]
    public function indexProfileDate(): Response
    {
        $this->addBreadcrumb('Accueil', '/');
        $this->addBreadcrumb('Profil', null);

        return $this->render('pages/data.html.twig', [
            'controllerName' => 'ProfileController',
        ]);
    }

    
    #[Route('/profil/data/delete', name: 'app_delete_account', methods: ['POST'])]
    public function deleteAccount(Request $request, EntityManagerInterface $entityManager, CsrfTokenManagerInterface $csrfTokenManager, Security $security): Response
    {
        $token = $request->request->get('_token');
        
        // Validate the CSRF token
        if (!$csrfTokenManager->isTokenValid(new CsrfToken('delete_account', $token))) {
            throw new AccessDeniedHttpException('Invalid CSRF token.');
        }
    
        $user = $this->getUser();
    
        // Remove the user from the database
        $entityManager->remove($user);
        $entityManager->flush();
    
        $security->logout(false);
    
        return $this->redirectToRoute('app_homepage');
    }
}
