<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "Cargaisons")]
class Cargaison
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private int $idCargaison;

    #[ORM\Column(type: "date")]
    private \DateTimeInterface $dateTransport;

    #[ORM\Column(type: "string", length: 50)]
    private string $villeDepart;

    #[ORM\Column(type: "string", length: 50)]
    private string $villeArrivee;

    #[ORM\ManyToOne(targetEntity: Camion::class, cascade: ["remove"])]
    #[ORM\JoinColumn(name: "immat", referencedColumnName: "immat", nullable: false, onDelete: "CASCADE")]
    private Camion $camion;

    #[ORM\ManyToOne(targetEntity: Chauffeur::class)]
    #[ORM\JoinColumn(name: "numero_permis", referencedColumnName: "numero_permis", nullable: false, onDelete: "CASCADE")]
    private Chauffeur $chauffeur;
    

    public function getIdCargaison(): int { return $this->idCargaison; }
    public function getDateTransport(): \DateTimeInterface { return $this->dateTransport; }
    public function getVilleDepart(): string { return $this->villeDepart; }
    public function getVilleArrivee(): string { return $this->villeArrivee; }
    public function getCamion(): Camion { return $this->camion; }
    public function getChauffeur(): Chauffeur { return $this->chauffeur; }

    public function setDateTransport(\DateTimeInterface $date): self { $this->dateTransport = $date; return $this; }
    public function setVilleDepart(string $ville): self { $this->villeDepart = $ville; return $this; }
    public function setVilleArrivee(string $ville): self { $this->villeArrivee = $ville; return $this; }
    public function setCamion(Camion $camion): self { $this->camion = $camion; return $this; }
    public function setChauffeur(Chauffeur $chauffeur): self { $this->chauffeur = $chauffeur; return $this; }
}
