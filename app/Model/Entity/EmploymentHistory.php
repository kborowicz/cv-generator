<?php

namespace App\Model\Entity;

/**
 * @Entity
 * @Table(name="employment_history")
 */
class employmentHistory {

    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue
     */
    protected $id;

    /**
     * @ManyToOne(targetEntity="User", inversedBy="employmentHistory")
     * @JoinColumn(nullable=false)
     */
    protected $user;

    /**
     * @Column(type="string", length=45, nullable=false)
     */
    protected $period;

    /**
     * @Column(type="string", nullable=false)
     */
    protected $title;

    /**
     * @Column(type="string")
     */
    protected $description;

}