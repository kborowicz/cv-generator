<?php

namespace App\Model\Entity;

/**
 * @Entity
 * @Table(name="interests")
 */
class Interests {

    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue
     */
    protected $id;

    /**
     * @ManyToOne(targetEntity="User", inversedBy="interests")
     * @JoinColumn(nullable=false)
     */
    protected $user;

    /**
     * @Column(type="string")
     */
    protected $description;

}