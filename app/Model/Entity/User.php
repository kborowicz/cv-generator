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
     * @Column(type="date", nullable=true)
     */
    protected $birthDate;

    /**
     * @Column(type="integer", length=9, nullable=true)
     */
    protected $phoneNumber;

    /**
     * @Column(type="string", nullable=true)
     */
    protected $adressStreetAndHouseNumber;

    /**
     * @Column(type="string", nullable=true)
     */
    protected $adressTownAndZipCode;

    /**
     * @Column(type="string", nullable=true)
     */
    protected $imageFile;

    /**
     * @Column(type="string", nullable=true)
     */
    protected $rodo;

    /**
     * @Column(type="string", nullable=true)
     */
    protected $githubLink;

    /**
     * @Column(type="json", nullable=true)
     */
    protected $skills;

    /**
     * @Column(type="json", nullable=true)
     */
    protected $interests;

    /**
     * @Column(type="json", nullable=true)
     */
    protected $employmentHistory;

    /**
     * @Column(type="json", nullable=true)
     */
    protected $educationHistory;

    /**
     * Get id.
     *
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set email.
     *
     * @param string $email
     *
     * @return User
     */
    public function setEmail($email) {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email.
     *
     * @return string
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     * Set password.
     *
     * @param string $password
     *
     * @return User
     */
    public function setPassword($password) {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password.
     *
     * @return string
     */
    public function getPassword() {
        return $this->password;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return User
     */
    public function setName($name) {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Set lastname.
     *
     * @param string $lastname
     *
     * @return User
     */
    public function setLastname($lastname) {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * Get lastname.
     *
     * @return string
     */
    public function getLastname() {
        return $this->lastname;
    }

    /**
     * Get fullname.
     *
     * @return string
     */
    public function getFullName() {
        return $this->name . ' ' . $this->lastname;
    }

    /**
     * Set birthDate.
     *
     * @param \DateTime $birthDate
     *
     * @return User
     */
    public function setBirthDate($birthDate) {
        $this->birthDate = $birthDate;

        return $this;
    }

    /**
     * Get birthDate.
     *
     * @return \DateTime
     */
    public function getBirthDate() {
        return $this->birthDate;
    }

    /**
     * Set adressStreetAndHouseNumber.
     *
     * @param string $adressStreetAndHouseNumber
     *
     * @return User
     */
    public function setAdressStreetAndHouseNumber($adressStreetAndHouseNumber) {
        $this->adressStreetAndHouseNumber = $adressStreetAndHouseNumber;

        return $this;
    }

    /**
     * Get adressStreetAndHouseNumber.
     *
     * @return string
     */
    public function getAdressStreetAndHouseNumber() {
        return $this->adressStreetAndHouseNumber;
    }

    /**
     * Set adressTownAndZipCode.
     *
     * @param string $adressTownAndZipCode
     *
     * @return User
     */
    public function setAdressTownAndZipCode($adressTownAndZipCode) {
        $this->adressTownAndZipCode = $adressTownAndZipCode;

        return $this;
    }

    /**
     * Get adressTownAndZipCode.
     *
     * @return string
     */
    public function getAdressTownAndZipCode() {
        return $this->adressTownAndZipCode;
    }

    /**
     * Set skills.
     *
     * @param json|null $skills
     *
     * @return User
     */
    public function setSkills($skills = null) {
        $this->skills = $skills;

        return $this;
    }

    /**
     * Get skills.
     *
     * @return json|null
     */
    public function getSkills() {
        return $this->skills;
    }

    /**
     * Set interests.
     *
     * @param json|null $interests
     *
     * @return User
     */
    public function setInterests($interests = null) {
        $this->interests = $interests;

        return $this;
    }

    /**
     * Get interests.
     *
     * @return json|null
     */
    public function getInterests() {
        return $this->interests;
    }

    /**
     * Set employmentHistory.
     *
     * @param json|null $employmentHistory
     *
     * @return User
     */
    public function setEmploymentHistory($employmentHistory = null) {
        $this->employmentHistory = $employmentHistory;

        return $this;
    }

    /**
     * Get employmentHistory.
     *
     * @return json|null
     */
    public function getEmploymentHistory() {
        return $this->employmentHistory;
    }

    /**
     * Set educationHistory.
     *
     * @param json|null $educationHistory
     *
     * @return User
     */
    public function setEducationHistory($educationHistory = null) {
        $this->educationHistory = $educationHistory;

        return $this;
    }

    /**
     * Get educationHistory.
     *
     * @return json|null
     */
    public function getEducationHistory() {
        return $this->educationHistory;
    }

    /**
     * Set rodo.
     *
     * @param string|null $rodo
     *
     * @return User
     */
    public function setRodo($rodo = null) {
        $this->rodo = $rodo;

        return $this;
    }

    /**
     * Get rodo.
     *
     * @return string|null
     */
    public function getRodo() {
        return $this->rodo;
    }

    /**
     * Set githubLink.
     *
     * @param string|null $githubLink
     *
     * @return User
     */
    public function setGithubLink($githubLink = null) {
        $this->githubLink = $githubLink;

        return $this;
    }

    /**
     * Get githubLink.
     *
     * @return string|null
     */
    public function getGithubLink() {
        return $this->githubLink;
    }

    /**
     * Set phoneNumber.
     *
     * @param int|null $phoneNumber
     *
     * @return User
     */
    public function setPhoneNumber($phoneNumber = null) {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    /**
     * Get phoneNumber.
     *
     * @return int|null
     */
    public function getPhoneNumber() {
        return $this->phoneNumber;
    }

    /**
     * Set imageFile.
     *
     * @param string|null $imageFile
     *
     * @return User
     */
    public function setImageFile($imageFile = null)
    {
        $this->imageFile = $imageFile;

        return $this;
    }

    /**
     * Get imageFile.
     *
     * @return string|null
     */
    public function getImageFile()
    {
        return $this->imageFile;
    }
}
