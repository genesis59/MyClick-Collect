<?php

namespace App\Entity;

class TownSearch
{

    /**
     *
     * @var String|null
     */
    private $nameTownSearch;

    /**
     *
     * @var String|null
     */
    private $zipCodeSearch;


    /**
     * Get the value of $nameTownSearch
     *
     * @return string|null
     */
    public function getNameTownSearch(): ?string
    {
        return $this->nameTownSearch;
    }

    /**
     * Set the value of nameTownSearch
     *
     * @param string|null $nameTownSearch
     * @return self
     */
    public function setNameTownSearch(?string $nameTownSearch): self
    {
        $this->nameTownSearch = $nameTownSearch;

        return $this;
    }

    /**
     * Get the value of $zipCodeSearch
     *
     * @return string|null
     */
    public function getZipCodeSearch(): ?string
    {
        return $this->zipCodeSearch;
    }

    /**
     * Set the value of $zipCodeSearch
     *
     * @param string|null $zipCodeSearch
     * @return self
     */
    public function setZipCodeSearch(?string $zipCodeSearch): self
    {
        $this->zipCodeSearch = $zipCodeSearch;

        return $this;
    }
}
