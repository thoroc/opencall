<?php

namespace OnCall\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use DateTime;
use OnCall\AdminBundle\Model\ItemStatus;

/**
 * @ORM\MappedSuperclass
 */
abstract class Item
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    protected $name;

    /**
     * @ORM\Column(type="smallint")
     */
    protected $status;

    /**
     * @ORM\Column(type="date")
     */
    protected $date_create;

    public function __construct()
    {
        $this->status = ItemStatus::ACTIVE;
        $this->date_create = new DateTime();
    }

    // begin setters
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    public function setInactive()
    {
        // do not set children inactive
        $this->setStatus(ItemStatus::INACTIVE);

        // set unassign number
        $this->unassignNumber();

        return $this;
    }

    public function unassignNumber()
    {
        $children = $this->getChildren();
        foreach ($children as $child)
            $child->unassignNumber();

        return $this;
    }
    // end setters

    // begin getters
    public function getID()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function isActive()
    {
        if ($this->status == ItemStatus::ACTIVE)
            return true;

        return false;
    }

    public function isInactive()
    {
        if ($this->status == ItemStatus::INACTIVE)
            return true;

        return false;
    }

    public function getDateCreate()
    {
        return $this->date_create;
    }

    public function getDateCreateFormatted()
    {
        return $this->date_create->format('d M Y');
    }
    // end getters

    public function getData()
    {
        $data = array(
            'id' => $this->getID(),
            'name' => $this->getName(),
            'status' => $this->getStatus(),
            'date_create' => $this->getDateCreateFormatted()
        );

        return $data;
    }

    public function jsonify()
    {
        return json_encode($this->getData());
    }
}
