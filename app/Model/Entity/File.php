<?php

namespace App\Model\Entity;

/**
 * @Entity
 * @Table(name="files")
 */
class File {

    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue
     */
    protected $id;

    /**
     * @ManyToOne(targetEntity="User", inversedBy="files")
     * @JoinColumn(nullable=false)
     */
    protected $user;

    /**
     * @Column(type="datetime", nullable=false)
     */
    protected $uploadedDate;

    /**
     * @Column(type="string", length=320, nullable=false)
     */
    protected $name;

}