<?php

namespace Api\Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * Connection
 *
 * @ORM\Table(name="`connection`", uniqueConstraints={@ORM\UniqueConstraint(name="id_user-id_user_parent", columns={"id_user", "id_user_parent"})}, indexes={@ORM\Index(name="id_user_parent", columns={"id_user_parent"}), @ORM\Index(name="status", columns={"state"}), @ORM\Index(name="IDX_29F773666B3CA4B", columns={"id_user"})})
 * @ORM\Entity
 */
class Connection
{
    const STATE_ACCEPTED = 'accepted';
    const STATE_REJECTED = 'rejected';

    /**
     * @var integer
     *
     * @ORM\Column(name="`connection_id`", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="`date_created`", type="datetime", nullable=false)
     */
    private $dateCreated = 'CURRENT_TIMESTAMP';

    /**
     * @var string
     *
     * @ORM\Column(name="`state`", type="string", nullable=false)
     */
    private $state = 'rejected';

    /**
     * @var \Api\Entities\User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_user", referencedColumnName="user_id")
     * })
     */
    private $user;

    /**
     * @var \Api\Entities\User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="id_user_parent", referencedColumnName="user_id")
     * })
     */
    private $parent;

    /**
     * @param int $id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param \DateTime $dateCreated
     *
     * @return $this
     */
    public function setDateCreated($dateCreated)
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * @param \Api\Entities\User $idUser
     *
     * @return $this
     */
    public function setUser($idUser)
    {
        $this->user = $idUser;

        return $this;
    }

    /**
     * @return \Api\Entities\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param \Api\Entities\User $idUserParent
     *
     * @return $this
     */
    public function setParent($idUserParent)
    {
        $this->parent = $idUserParent;

        return $this;
    }

    /**
     * @return \Api\Entities\User
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param string $state
     *
     * @return $this
     */
    public function setState($state)
    {
        $this->state = $state;

        return $this;
    }

    /**
     * @return string
     */
    public function getState()
    {
        return $this->state;
    }

}
