<?php
/**
 * Created by PhpStorm.
 * User: Akitae
 * Date: 21/02/2018
 * Time: 09:58
 */

namespace UpjvBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use UpjvBundle\Entity\Groupe;
use UpjvBundle\Entity\Matiere;
use UpjvBundle\Entity\Parcours;
use UpjvBundle\Entity\Utilisateur;

class ExportUserController extends Controller
{
    /**
     * @Route("/admin/listUser", name="admin_export_user")
     * @return mixed
     */
    public function indexAction()
    {
        $listUser = $this->getDoctrine()->getRepository(Utilisateur::class)->findBy(['type' => Utilisateur::TYPE_ETUDIANT],['nom'=>'ASC']);

        $listGroup = $this->getDoctrine()->getRepository(Groupe::class)->findAll();
        $listParcours = $this->getDoctrine()->getRepository(Parcours::class)->findAll();
        $listMatieres = $this->getDoctrine()->getRepository(Matiere::class)->findAll();

        return $this->render('UpjvBundle:Admin/ExportUser:index.html.twig',[
            'listUser' => $listUser,
            'listGroup' => $listGroup,
            'listParcours' => $listParcours,
            'listMatieres' => $listMatieres
        ]);
    }

    /**
     * @Route("/admin/listUser/filterGroupe", name="admin_export_user_filter_groupe")
     * Récupère la liste des groupes en fonction de la matière séléctionné
     * @return Response
     */
    public function getListGroupe(){
        $resultGroupe = [];
        $i =0;
        foreach ($_GET as $matiere){
            $listGroup = $this->getDoctrine()->getRepository(Groupe::class)->findAll();


            foreach ($listGroup as $groupe){
                if($groupe->getMatiere()->getNom() === $matiere){
                    $resultGroupe[] = $groupe->getNom();
                }
                $i++;
            }
        }
        return new Response(json_encode($resultGroupe));
    }

    /**
     * @Route("/admin/listUser/registrationSheet", name="admin_export_user_registration")
     */
    public function getRegistrationSheet(){

        $listUser = $this->getDoctrine()->getRepository(Utilisateur::class)->filterUserByArray($_POST);

        $html = $this->renderView('UpjvBundle:Admin/ExportUser:registrationSheet.html.twig',[
            'listUser' => $listUser,
            'commentaire' => isset($_POST['commentaireRegistrationSheet'])?$_POST['commentaireRegistrationSheet']:null,
            'listUE' => isset($_POST['matiere'])?$_POST['matiere']:null
        ]);
        $html_header = $this->renderView('UpjvBundle:Admin/ExportUser:registrationSheet-header.html.twig',[
            'listUser' => $listUser,
            'commentaire' => isset($_POST['commentaireRegistrationSheet'])?$_POST['commentaireRegistrationSheet']:null,
            'listUE' => isset($_POST['matiere'])?$_POST['matiere']:null
        ]);

        $filename = sprintf('Emargement-%s.pdf', date('Y-m-d'));

        return new Response(
            $this->get('knp_snappy.pdf')
                ->getOutputFromHtml($html,['header-html'=>$html_header])
            ,
            200,
            [
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => sprintf('attachment; filename="%s"', $filename),
            ]
        );
    }
}