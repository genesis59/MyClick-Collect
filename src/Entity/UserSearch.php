<?php

namespace App\Entity;

class UserSearch {

    /**
     *
     * @var String|null
     */
    private $emailSearch;

    /**
     * 
     * @var String|null
     */
    private $lastNameSearch;


    /**
     * Get the value of emailSearch
     *
     * @return  String|null
     */ 
    public function getEmailSearch()
    {
        return $this->emailSearch;
    }

    /**
     * Set the value of emailSearch
     *
     * @param  String|null  $emailSearch
     *
     * @return  self
     */ 
    public function setEmailSearch($emailSearch)
    {
        $this->emailSearch = $emailSearch;

        return $this;
    }

    /**
     * Get the value of lastNameSearch
     *
     * @return  String|null
     */ 
    public function getLastNameSearch()
    {
        return $this->lastNameSearch;
    }

    /**
     * Set the value of lastNameSearch
     *
     * @param  String|null  $lastNameSearch
     *
     * @return  self
     */ 
    public function setLastNameSearch($lastNameSearch)
    {
        $this->lastNameSearch = $lastNameSearch;

        return $this;
    }
}