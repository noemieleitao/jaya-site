<?php

namespace UpjvBundle\Repository;
use UpjvBundle\Entity\Matiere;
use UpjvBundle\Entity\Utilisateur;

/**
 * GroupeRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class GroupeRepository extends \Doctrine\ORM\EntityRepository
{

    public function getOneGroupeByUserAndMatiere(Matiere $matiere, Utilisateur $user){
        return $this
            ->createQueryBuilder('o')
            ->join('o.utilisateurs','utilisateurs')
            ->join('utilisateurs.matieres','matiere')
            ->where('utilisateurs = :user')
            ->setParameter('user',$user)
            ->andWhere('matiere = :matiere')
            ->setParameter('matiere',$matiere)
            ->getQuery()
            ->getOneOrNullResult()
            ;
    }
  
  public function resetAllGroupe()
    {
        $rawSql = "DELETE FROM groupe";

        $stmt = $this->getEntityManager()->getConnection()->prepare($rawSql);
        $stmt->execute();
    }

    public function findByMatiere ($arrayMatiere) {
        $queryBuilder = $this->createQueryBuilder('e');
        $queryBuilder
            ->where('e.matiere IN (:matiere)')
            ->setParameter('matiere', $arrayMatiere);
        return $queryBuilder->getQuery()->getResult();
    }


}
