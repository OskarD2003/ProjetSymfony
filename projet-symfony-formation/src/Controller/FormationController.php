<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Formation;
use App\Entity\Employe;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use App\Form\FormationType;
use App\Form\EmployeType;
use Symfony\Component\HttpFoundation\Session\Session;
use App\Entity\Inscription;
class FormationController extends AbstractController
{
    #[Route('/formation', name: 'app_formation')]
    public function index(): Response
    {
        return $this->render('formation/index.html.twig', [
            'controller_name' => 'FormationController',
        ]);
    }
    //faire laffichage et la suppresion 
    #[Route('/afficheLesFormations', name: 'app_aff')]   
public function afficheLesFormationsAction(ManagerRegistry $doctrine)
{
    $LesFormations= $doctrine->getManager()->getRepository(Formation::class)->findall();
    if(!$LesFormations)
    {
        $message = "Pas de Formation";
    }
    else
    {
        $message = null;
    }
   
    return $this->render('formation/listeformation.html.twig', array('ensFormations'=>$LesFormations, 'message'=>$message));
}
#[Route('/suppFormation/{id}', name: 'app_form_sup')] 

public function suppFormationAction($id, ManagerRegistry $doctrine)
{
    $formation= $doctrine->getManager()->getRepository(Formation::class)->find($id);
    $entityManager = $doctrine->getManager();
    $entityManager->remove($formation);
    $entityManager->flush();

    return $this->redirectToRoute('app_aff');
}
#[Route('/ajoutFormation', name: 'app_formation_ajouter')] 

public function ajoutFormationAction(Request $request, ManagerRegistry $doctrine, $formation=null)
{
    if($formation == null)
    {
        $formation = new Formation();
    }
    $form = $this->createForm(FormationType::class, $formation);

    $form->handleRequest($request);

    if($form->isSubmitted()&& $form->isValid())
    {
        $em = $doctrine->getManager();
        $em->persist($formation);
        $em->flush();
        return $this->redirectToRoute('app_aff');
    }

    return $this->render('formation/ajoutFormation.html.twig', array('form'=>$form->createView()));
}

#[Route('/login', name: 'login')] 
public function connexion(Request $request, ManagerRegistry $doctrine, $emp=null)
{
    if($emp==null){
        $emp = new Employe();
    }
    $form =$this->createForm(EmployeType::class, $emp);
    $form->handleRequest($request);
    if($form->isSubmitted() && $form->isValid()){
        $employe = $doctrine->getRepository(Employe::class)->findOneBySomeLoginMdp($emp->getLogin(),$emp->getMdp());
        if($employe)
        {
            $session = new Session();
            $session->set('employeId', $employe->getId());
            if($employe->getStatut()== 0)
            {
                return $this->redirectToRoute('app_aff_employe');//statue = 0
            }
            else{

                return $this->redirectToRoute('app_aff'); //statue = 1(admin)
            
         }
        }
    }
    return $this->render('formation/connexion.html.twig', array('form'=>$form->createView()));
}

#[Route('/afficheLesFormEmploye', name: 'app_aff_employe')]   
public function afficheLesFormationsActionEmploye(ManagerRegistry $doctrine)
{
    $LesFormations= $doctrine->getManager()->getRepository(Formation::class)->findall();
    if(!$LesFormations)
    {
        $message = "Pas de Formation";
    }
    else
    {
        $message = null;
    }
   
    return $this->render('formation/ListeFormationEmploye.html.twig', array('ensFormations'=>$LesFormations, 'message'=>$message));
}

#[Route('/afficheLesFormEmploye/{id}', name: 'app_aff_FormEmploye')]   
public function IncriptionFormation($id, Request $request, ManagerRegistry $doctrine, Session $session)
{
    $employe=$doctrine->getManager()->getRepository(Employe::class)->find($session->get("employeId"));
    $formation= $doctrine->getManager()->getRepository(Formation::class)->find($id);
    $statut="en cours";
    $inscription = new Inscription();
    $inscription->setStatut("en cours");
            $message = null;
            $inscription->setFormation($formation);
            $inscription->setEmploye($employe);
            $insert= $doctrine->getManager();
            $insert->persist($inscription);
            $insert->flush();
   
    return $this->render('formation/InscriptionReussi.html.twig', array('message'=>$message));
}
//exerice 2
#[Route('/afficheInscriptionEmploye/{id}', name: 'app_aff_inscr_employe')]   
public function afficherInscriptionEmploye($id,ManagerRegistry $doctrine, Session $session, Request $request)

{  
    $employe= $doctrine->getManager()->getRepository(Employe::class)->find($id);
    $entityManager = $doctrine->getManager();
    $entityManager->flush();
    $LesInscriptions= $doctrine->getManager()->getRepository(Inscription::class)->findall();
    if(!$LesInscriptions)
    {
        $message = "Pas d'Incription pour cette utilisateur";
    }
    else
    {
        $message = null;
    }
   
    return $this->render('formation/InscriptionEmploye.html.twig', array('ensInscriptions'=>$LesInscriptions, 'message'=>$message));
}
#[Route('/afficheLesEmployes', name: 'aff_Employes')]   
public function afficheLesEmployes(ManagerRegistry $doctrine)
{
    $LesEmployes= $doctrine->getManager()->getRepository(Employe::class)->findByStatut();
    if(!$LesEmployes)
    {
        $message = "Pas d'Employe";
    }
    else
    {
        $message = null;
    }
    return $this->render('formation/ListeDesEmployes.html.twig', array('ensEmployes'=>$LesEmployes, 'message'=>$message));

}

}