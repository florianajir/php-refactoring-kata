<?php

class User
{
    public $id;
    public $firstname;
    public $lastname;
    public $email;

    public function __construct($id, $firstname, $lastname, $email)
    {
        $this->id = $id;
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->email = $email;
    }
}
