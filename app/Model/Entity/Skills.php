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

}