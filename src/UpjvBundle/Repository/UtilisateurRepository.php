<?php

namespace UpjvBundle\Repository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use UpjvBundle\Entity\Matiere;
use UpjvBundle\Entity\Semestre;
use UpjvBundle\Entity\Utilisateur;

/**
 * UtilisateurRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UtilisateurRepository extends \Doctrine\ORM\EntityRepository
{

    public function findUser ($email, $motDePasse) {
        $queryBuilder = $this->createQueryBuilder('e');
        $queryBuilder->where('e.email = :email')->setParameter('email', $email)
        ->andWhere('e.motDePasse = :motDePasse')->setParameter('motDePasse', $motDePasse);

        try {
            return $queryBuilder->getQuery()->getSingleResult();
        } catch (NoResultException $e) {
            return null;
        }

    }

    /**
     * Recherche les utilisateurs suivant un role précis.
     * @param $role
     * @return mixed
     */
    public function findByRole ($role) {
        $queryBuilder = $this->createQueryBuilder('e');
        $queryBuilder
            ->where('e.roles LIKE :roles')
            ->setParameter('roles', '%'.$role.'%')
            ->orderBy('e.nom');

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * Recherche l'utilisateur par l'identifiant.
     * @param $username
     * @return mixed
     */
    public function findByUsername ($username) {
        $queryBuilder = $this->createQueryBuilder('e');
        $queryBuilder->where('e.username = :username')->setParameter('username', $username);

        try {
            return $queryBuilder->getQuery()->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            return $queryBuilder->getQuery()->getResult()[0];
        }
    }

    /**
     * Rercherche l'utilisateur par l'adresse email.
     * @param $email
     * @return mixed
     */
    public function findUserByEmail ($email) {
        $queryBuilder = $this->createQueryBuilder('e');
        $queryBuilder->where('e.email = :email')->setParameter('email', $email);

        try {
            return $queryBuilder->getQuery()->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            return $queryBuilder->getQuery()->getResult()[0];
        }
    }

    /**
     * Recherche l'utilisateur par le numéro étudiant.
     * @param $numeroEtudiant
     * @return mixed
     */
    public function findUserByNumeroEtudiant ($numeroEtudiant) {
        $queryBuilder = $this->createQueryBuilder('e');
        $queryBuilder->where('e.numeroEtudiant = :numeroEtudiant')->setParameter('numeroEtudiant', $numeroEtudiant);

        try {
            return $queryBuilder->getQuery()->getOneOrNullResult();
        } catch (NonUniqueResultException $e) {
            return $queryBuilder->getQuery()->getResult()[0];
        }
    }


    public function filterUserByArray($filtres){

        $queryBuilder = $this
            ->createQueryBuilder('q')
            ->select('DISTINCT(q.id),q.nom,q.prenom,p.nom as parcours')
            ->leftJoin('q.matieres','m')
            ->leftJoin('q.parcours','p')
            ->leftJoin('q.groupes', 'g')
            ->orderBy('q.nom')
        ;

        $queryBuilder->where('q.roles like :roles')->setParameter('roles','%'.Utilisateur::ROLE_ETUDIANT.'%');
        if(isset($filtres['nom'])){
            $queryBuilder->andWhere('q.nom IN (:nom)')->setParameter('nom',$filtres['nom']);
        }

        if(isset($filtres['prenom'])){
            $queryBuilder->andWhere('q.prenom IN (:prenom)')->setParameter('prenom',$filtres['prenom']);
        }
        if(isset($filtres['parcours'])){
            $queryBuilder->andWhere('p.nom IN (:parcours)')->setParameter('parcours',$filtres['parcours']);
        }
        if(isset($filtres['matiere'])){
            foreach ($filtres['matiere'] as $matiere){
                $subString = explode(' - ', $matiere);
                $matieres[] = $subString[0];
            }
            $queryBuilder->andWhere('m.code IN (:matiere)')->setParameter('matiere',$matieres);
        }
        if(isset($filtres['groupe'])){
            $queryBuilder->andWhere('g.nom IN (:groupe)')->setParameter('groupe',$filtres['groupe']);
        }

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * @param Matiere $matiere
     * @param bool $optionnel
     * @param bool $stagiare
     * @return array|mixed
     * @throws \Doctrine\DBAL\DBALException
     * Return la liste des utilisateurs pour une matiere
     */
    public function findListUserByMatiere(Matiere $matiere, Semestre $semestre, $optionnel = false, $stagiare = false){
        $sql = "
        SELECT u.id FROM utilisateur u  JOIN parcours p on u.parcours_id = p.id WHERE p.stagiare= :stagiare AND u.parcours_id IN
        (SELECT mp.parcour_id FROM matiere JOIN matiere_parcours mp on matiere.id = mp.matieres_id WHERE matiere.id= :matiereId AND mp.optionnel = :optionnel AND matiere.semestre_id = :semestre);
        ";

        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $stmt->bindValue('stagiare',$stagiare);
        $stmt->bindValue('matiereId',$matiere->getId());
        $stmt->bindValue('optionnel',$optionnel);
        $stmt->bindValue('semestre',$semestre->getId());

        $stmt
            ->execute();
        $result = $stmt->fetchAll();
        if($result == null){
            return $result;
        }
        foreach ($result as $r){
            $resultat[] = $r['id'];
        }

        return $this->createQueryBuilder('u')
            ->where('u.id IN (:result)')
            ->andWhere('u.roles like :roles')
            ->setParameter('roles','%'.Utilisateur::ROLE_ETUDIANT.'%')
            ->setParameter('result',$resultat)
            ->getQuery()
            ->getResult();
    }

    public function findListUserForMatiereOptionnel(Matiere $matiere,Semestre $semestre, $ordre){
        return $this
            ->createQueryBuilder('u')
            ->join('u.optionnel','o')
            ->join('o.matiere','matiere')
            ->join('matiere.semestre', 'semestre')
            ->where('matiere.semestre = :semestre')
            ->setParameter('semestre',$semestre)
            ->andWhere('matiere = :matiere')
            ->setParameter('matiere', $matiere)
            ->andWhere('o.ordre = :ordre')
            ->setParameter('ordre', $ordre)
            ->getQuery()
            ->getResult()
            ;
    }

    public function getUniqueListUserOptionnelByPole($poleDeCompetence){
        return $this
            ->createQueryBuilder('u')
            ->join('u.optionnel','optionnel')
            ->join('optionnel.matiere','matiere')
            ->where('matiere.poleDeCompetence = :poleDeCompetence')
            ->setParameter('poleDeCompetence',$poleDeCompetence)
            ->getQuery()
            ->getResult()
            ;
    }

}
