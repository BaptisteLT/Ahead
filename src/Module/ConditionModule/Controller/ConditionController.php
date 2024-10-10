<?php
namespace App\Module\ConditionModule\Controller;

use Exception;
use App\Entity\User;
use App\Controller\BootController;
use Doctrine\ORM\EntityManagerInterface;
use App\Module\ConditionModule\Entity\Report;
use App\Module\ConditionModule\Form\RgpdType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Module\ConditionModule\Form\SickDateType;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Module\ConditionModule\Form\DepartmentType;
use App\Module\ConditionModule\Form\SearchDiseaseType;
use App\Module\ConditionModule\Service\SiteMapService;
use App\Module\ConditionModule\Form\SearchSymptomsType;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Module\ConditionModule\Form\DiseaseDiagnosedType;
use App\Module\MapModule\Repository\DepartmentRepository;
use App\Module\ConditionModule\Repository\DiseaseRepository;
use App\Module\ConditionModule\Repository\SymptomsRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class ConditionController extends BootController
{
    public function __construct(
        protected SiteMapService $siteMapService
    ){}

    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    #[Route('/form', name: 'app_form', methods: ['GET'])]
    public function indexForm(): Response
    {
        $this->addBreadcrumb('Accueil', '/');
        $this->addBreadcrumb('Formulaire', null);

        return $this->render('index_form.html.twig');
    }

    #[Route('/api/load-form/{step}', name: 'api_load_form', methods: ['GET'])]
    public function apiLoadForm(int $step, SessionInterface $session, EntityManagerInterface $em, SymptomsRepository $symptomsRepository, DepartmentRepository $departmentRepository, DiseaseRepository $diseaseRepository): Response
    {
        if($step === 1){
            $this->clearSession($session);
        }
        //Skip "Rechercher une maladie" if the user has not been diagnosed by a doctor
        if($session->has('isDiseaseDiagnosed') && $session->get('isDiseaseDiagnosed') == 0){
            $step = $step + 1;
        }

        if($step === 1){
            $form = $this->createForm(SickDateType::class);
            return $this->renderForm($form);
        }
        if($step === 2){
            $form = $this->createForm(DiseaseDiagnosedType::class);
            return $this->renderForm($form);
        }
        if($step === 3){
            $form = $this->createForm(SearchDiseaseType::class);
            return $this->renderForm($form);
        }
        if($step === 4){
            $form = $this->createForm(SearchSymptomsType::class);
            return $this->renderForm($form);
        }
        if($step === 5){
            $form = $this->createForm(DepartmentType::class);
            return $this->renderForm($form);
        }
        if($step === 6){
            $form = $this->createForm(RgpdType::class);
            return $this->renderForm($form);
        }
        if($step === 7){

            $report = new Report();
            $report->setUser($this->getUser());
            if($session->has('disease')){
                $report->setDisease($diseaseRepository->find($session->get('disease')));
            }
            
            $department = $departmentRepository->find($session->get('department'));
            $report->setDepartment($department);
            $report->setHasAcceptedRgpd($session->get('hasAgreedRgpd'));
            $report->setDateReport($session->get('sickDate'));
            foreach($session->get('symptoms') as $symptomId){
                $symptom = $symptomsRepository->find($symptomId);
                $report->addSymptom($symptom);
                $em->persist($symptom);
            }
            $em->persist($report);
            $em->flush();

            $this->clearSession($session);
            return new JsonResponse(['redirectUrl' => $this->generateUrl('app_map')]);
        }
        return new Response('Form not found', 404);
    }

    #[Route('/api/submit-form/{step}', name: 'api_submit_form', methods: ['POST'])]
    public function apiSubmitForm(
        int $step, 
        Request $request, 
        SessionInterface $session, 
        SymptomsRepository $symptomsRepository, 
        DiseaseRepository $diseaseRepository,
        DepartmentRepository $departmentRepository
    ): JsonResponse
    {
        //Skip "Rechercher une maladie" if the user has not been diagnosed by a doctor
        if($session->has('isDiseaseDiagnosed') && $session->get('isDiseaseDiagnosed') == 0){
            $step = $step + 1;
        }

        if($step === 1){
            try{
                $sickDate = $request->request->all()['sick_date']['date'];
            }
            catch(Exception $e){
                return new JsonResponse(['error'=>'Requête invalide.'], 400);
            }
            $sickDate = \DateTimeImmutable::createFromFormat('d/m/Y', $sickDate);
            if($sickDate !== false){
                $session->set('sickDate', $sickDate);
            }
            else{
                return new JsonResponse(['error'=>'Format de date invalide'], 400);
            }
        }
        else if($step === 2){
            try{
                $isDiseaseDiagnosed = (int)$request->request->all()['disease_diagnosed']['diagnosed'];
            }
            catch(Exception $e){
                return new JsonResponse(['error'=>'Le choix Oui/Non est requis.'], 400);
            }
            
            if(($isDiseaseDiagnosed <> 1 && $isDiseaseDiagnosed <> 0)){
                return new JsonResponse(['error'=>'Réponse invalide, veuillez choisir "Oui" ou "Non"'], 400);
            }
            $session->set('isDiseaseDiagnosed', $isDiseaseDiagnosed);
        }
        else if($step === 3){
            try{
                $diseaseId = (int)$request->request->all()['search_disease']['diseases'];
            }
            catch(Exception $e){
                return new JsonResponse(['error'=>'Une maladie est requise.'], 400);
            }
            
            $disease = $diseaseRepository->find($diseaseId);
            if(!$disease){
                return new JsonResponse(['error'=>'Cette maladie n\'existe pas.'], 400);
            }
            else{
                $session->set('disease', $diseaseId);
            }
        }
        else if($step === 4){

            $symptoms = $request->request->get('symptoms', null);
            if($symptoms === null || empty(json_decode($symptoms)))
            {
                return new JsonResponse(['error'=>'Au moins un symtôme est requis.'], 400);
            }
            $symptomsArray = json_decode($symptoms);

            $symptoms = [];
            foreach($symptomsArray as $symptomId){
                $symptom = $symptomsRepository->find($symptomId);
                if(null === $symptom){
                    return new JsonResponse(['error'=>'Requête invalide, un des symptômes ne figure pas dans la liste initiale.'], 400);
                }
                $symptoms[] = $symptom;
            }

            $session->set('symptoms', $symptomsArray);
        }
        else if($step === 5){
            try{
                $departmentId = (int)$request->request->all()['department']['department'];
            }
            catch(Exception $e){
                return new JsonResponse(['error'=>'Le département est requis.'], 400);
            }
            
            $department = $departmentRepository->find($departmentId);
            if(!$department){
                return new JsonResponse(['error'=>'Le département est requis.'], 400);
            }
            else{
                $session->set('department', $departmentId);
            }
        }
        else if($step === 6){
            try{
                $hasAgreedRgpd = (int)$request->request->all()['rgpd']['agreeTerms'];
            }
            catch(Exception $e){
                return new JsonResponse(['error'=>'Il est obligatoire d\'accepter les conditions avant de continuer.'], 400);
            }
            if(empty($hasAgreedRgpd)){
                return new JsonResponse(['error'=>'Il est obligatoire d\'accepter les conditions avant de continuer.'], 400);
            }
        
            else{
                $session->set('hasAgreedRgpd', true);
            }
        }
        return new JsonResponse('ok');
    }

    private function renderForm($form){
        return $this->render('form/_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    private function clearSession(SessionInterface $session){
        $session->remove('isDiseaseDiagnosed');
        $session->remove('disease');
        $session->remove('sickDate');
        $session->remove('hasAgreedRgpd');
        $session->remove('department');
        $session->remove('symptoms');
    }
}
