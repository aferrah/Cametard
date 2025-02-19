<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "Chauffeurs")]
class Chauffeur
{
    #[ORM\Id]
    #[ORM\Column(type: "string", length: 20)]
    private string $numeroPermis;

    #[ORM\Column(type: "string", length: 50)]
    private string $nom;

    #[ORM\Column(type: "string", length: 50)]
    private string $prenom;

    public function getNumeroPermis(): string { return $this->numeroPermis; }
    public function getNom(): string { return $this->nom; }
    public function getPrenom(): string { return $this->prenom; }

    public function setNumeroPermis(string $numeroPermis): self { $this->numeroPermis = $numeroPermis; return $this; }
    public function setNom(string $nom): self { $this->nom = $nom; return $this; }
    public function setPrenom(string $prenom): self { $this->prenom = $prenom; return $this; }
}
