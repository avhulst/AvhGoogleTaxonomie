<?php

namespace AvhGoogleTaxonomie\Models;

use Shopware\Components\Model\ModelEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="s_plugin_avh_taxonomie")
 * @ORM\Entity
 */
class AvhTaxonomie extends ModelEntity
{
    /**
     * @var integer $id
     *
     * @ORM\Column(type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string $name
     *
     * @ORM\Column(type="string", length=500, nullable=false)
     */
    private $name;

    /**
     * @var string $googleid
     *
     * @ORM\Column(type="string", length=500, nullable=false)
     */
    private $googleid;


    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getGoogleid()
    {
        return $this->googleid;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @param string $name
     */
    public function setGoogleid($googleid)
    {
        $this->googleid = $googleid;
    }

}