<?php




namespace UpjvBundle\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use UpjvBundle\Entity\PoleDeCompetence;
use UpjvBundle\Entity\Parcours;
use UpjvBundle\Form\PoleDeCompetenceType;

class PolesController extends Controller
{

  /**
  * @Route("/admin/pole", name="admin_pole")
  * @return mixed
  */
  public function indexAction()
  {
    $listPole = $this->getDoctrine()->getRepository(PoleDeCompetence::class)->findAll();

    return $this->render('UpjvBundle:Admin/Poles:index.html.twig',[
      'listPole' => $listPole
    ]);
  }

  /**
  * @param $id
  * @param $request
  * @return mixed
  * @Route("/admin/pole/pole/{id}", name="admin_pole_edit")
  */
  public function updateAction($id,Request $request)
  {
    $em = $this->getDoctrine()->getManager();
    /** @var Poledecompetence $pole */
    $pole = $em->getRepository(PoleDeCompetence::class)->find($id);


    if (!$pole instanceof PoleDeCompetence) {
      $pole = new PoleDeCompetence();
    }

    $form = $this->createForm(PoleDeCompetenceType::class,$pole);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $pole = $form->getData();
      $em->persist($pole);
      $em->flush();

      return $this->redirectToRoute('admin_pole');
    }

    return $this->render('UpjvBundle:Admin/Poles:update.html.twig',[
      'pole' => $pole,
      'form' => $form->createView()
    ]);

  }

  /**
  * @param $id
  * @Route("/admin/pole/show/{id}", name="admin_pole_show")
  * @return mixed
  */
  public function showAction($id)
  {
    $em = $this->getDoctrine()->getManager();
    $pole = $em->getRepository(PoleDeCompetence::class)->find($id);

    if (!$pole instanceof PoleDeCompetence) {
        $this->get('session')->getFlashBag()->add('erreur', 'Le pole de compétence selectionné n\'existe pas');
        return $this->redirectToRoute('admin_pole');
    }

    return $this->render('UpjvBundle:Admin/Poles:show.html.twig',[
      'pole' => $pole
    ]);

  }

  /**
  * @param $id
  * @Route("/admin/pole/delete/{id}", name="admin_pole_delete")
  * @return mixed
  */
  public function deleteAction($id){
    $em = $this->getDoctrine()->getManager();
      try{
          $pole = $em->getRepository(PoleDeCompetence::class)->find($id);
          $em->remove($pole);
          $em->flush();
          $this->get('session')->getFlashBag()->add('success', 'L\'utilisateur a bien été supprimé');
      }catch (\Exception $e){
          $this->get('session')->getFlashBag()->add('erreur', 'Une erreur s\'est produite lors de la suppression');
      }

      return $this->redirectToRoute('admin_pole');
  }



}
