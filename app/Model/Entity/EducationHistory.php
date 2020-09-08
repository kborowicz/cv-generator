<?php

namespace App\Model\Entity;

/**
 * @Entity
 * @Table(name="education_history")
 */
class EducationHistory {

    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue
     */
    protected $id;

    /**
     * @ManyToOne(targetEntity="User", inversedBy="educationHistory")
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