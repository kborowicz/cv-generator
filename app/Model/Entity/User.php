<?php

namespace App\Model\Entity;

/**
 * @Entity
 * @Table(name="users")
 */
class User {

    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue
     */
    protected $id;

    /**
     * @Column(type="string", nullable=false)
     */
    protected $email;

    /**
     * @Column(type="string", nullable=false)
     */
    protected $password;

    /**
     * @Column(type="string", length=45, nullable=false)
     */
    protected $name;

    /**
     * @Column(type="string", length=45, nullable=false)
     */
    protected $lastname;

    /**
     * @Column(type="date", nullable=false)
     */
    protected $birthDate;

    /**
     * @Column(type="string", length=45, nullable=false)
     */
    protected $adress_street;

    /**
     * @Column(type="string", length=15, nullable=false)
     */
    protected $adress_houseNumber;

    /**
     * @Column(type="string", length=15, nullable=false)
     */
    protected $adress_zipCode;

    /**
     * @Column(type="string", length=45, nullable=false)
     */
    protected $adress_Town;

    /**
     * @OneToMany(targetEntity="File", mappedBy="user", orphanRemoval=true)
     */
    protected $files;

    /**
     * @OneToMany(targetEntity="Skills", mappedBy="user", orphanRemoval=true)
     */
    protected $skills;

    /**
     * @OneToMany(targetEntity="Interests", mappedBy="user", orphanRemoval=true)
     */
    protected $interests;

    /**
     * @OneToMany(targetEntity="EmploymentHistory", mappedBy="user", orphanRemoval=true)
     */
    protected $employmentHistory;

    /**
     * @OneToMany(targetEntity="EducationHistory", mappedBy="user", orphanRemoval=true)
     */
    protected $educationHistory;

    public function getFullName() {
        return $this->name . ' ' . $this->lastname;
    }

}