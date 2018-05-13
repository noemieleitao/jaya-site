<?php
/**
 * Created by PhpStorm.
 * User: Florian
 * Date: 12/05/2018
 * Time: 20:25
 */

namespace UpjvBundle\DTO;

class EmailWrapper
{

    /** @var array */
    private $listParcours;

    /** @var array */
    private $listMatiere;

    /** @var array */
    private $listGroupe;

    /**
     * @return array
     */
    public function getListParcours()
    {
        return $this->listParcours;
    }

    /**
     * @param array $listParcours
     */
    public function setListParcours($listParcours)
    {
        $this->listParcours = $listParcours;
    }

    /**
     * @return array
     */
    public function getListMatiere()
    {
        return $this->listMatiere;
    }

    /**
     * @param array $listMatiere
     */
    public function setListMatiere($listMatiere)
    {
        $this->listMatiere = $listMatiere;
    }

    /**
     * @return array
     */
    public function getListGroupe()
    {
        return $this->listGroupe;
    }

    /**
     * @param array $listGroupe
     */
    public function setListGroupe($listGroupe)
    {
        $this->listGroupe = $listGroupe;
    }

}