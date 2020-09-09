<?php

namespace App\Model\Entity;

/**
 * @Entity
 * @Table(name="skills")
 */
class Skills {

    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue
     */
    protected $id;

    /**
     * @ManyToOne(targetEntity="User", inversedBy="skills")
     * @JoinColumn(nullable=false)
     */
    protected $user;

    /**
     * @Column(type="string")
     */
    protected $description;

    /**
     * Get id.
     *
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set description.
     *
     * @param string $description
     *
     * @return Skills
     */
    public function setDescription($description) {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description.
     *
     * @return string
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * Set user.
     *
     * @param \App\Model\Entity\User $user
     *
     * @return Skills
     */
    public function setUser(\App\Model\Entity\User $user) {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user.
     *
     * @return \App\Model\Entity\User
     */
    public function getUser() {
        return $this->user;
    }
}
