<?php

class User {
# -------------------- properties --------------------

    private int $id;
    private string $firstname;
    private string $lastname;
    private string $email;
    private string $password;
    private bool $isadmin;
    private string $creationDate;
    private string $lastConnectionDate;

# -------------------- Constructor --------------------

    public function __construct($firstname, $lastname, $email)
    {
        $this->setFirstname($firstname);
        $this->setLastname($lastname);
        $this->setEmail($email);
    }

# -------------------- Getters --------------------

    public function getId(): int{return $this->id;}
    public function getFirstname(): string {return $this->firstname;}
    public function getLastname(): string {return $this->lastname;}
    public function getEmail(): string {return $this->email;}
    public function getPassword(): string {return $this->password;}
    public function getIsAdmin(): bool{return $this->isadmin;}
    public function getCreationDate():string {return $this->creationDate;}
    public function getLastConnectionDate():string {return $this->lastConnectionDate;}

# -------------------- Setters --------------------

    public function setId(int $id): void {$this->id = $id;}
    public function setFirstname(string $firstname): void {$this->firstname = $firstname;}
    public function setLastname(string $lastname): void {$this->lastname = $lastname;}
    public function setEmail(string $email): void {$this->email = $email;}
    # -- The password is hashed --
    public function setPassword(string $password): void {$this->password = password_hash($password, PASSWORD_DEFAULT);}
    public function setIsAdmin(bool $isadmin): void {$this->isadmin = $isadmin;}
    public function setCreationDate(string $creationDate): void {$this->creationDate = $creationDate;}
    public function setLastConnectionDate(string $lastConnectionDate): void {$this->lastConnectionDate = $lastConnectionDate;}

}