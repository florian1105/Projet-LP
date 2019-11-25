<?php
namespace App\Entity;

class Fichiers
{
    private $filePath;

    public function getFilePath()
    {
        return $this->filePath;
    }

    public function setFilePath($filePath)
    {
        $this->filePath = $filePath;

        return $this;
    }
}
