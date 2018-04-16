<?php

namespace UpjvBundle\Repository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
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

}
