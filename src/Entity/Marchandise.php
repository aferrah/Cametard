<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Enum\CamionTypeEnum; // Ajoute cette ligne

#[ORM\Entity]
#[ORM\Table(name: "Marchandises")]
class Marchandise
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private int $idMarchandise;

    #[ORM\Column(type: "string", length: 50)]
    private string $nom;

    #[ORM\Column(type: "string", length: 20, enumType: CamionTypeEnum::class)]
    private CamionTypeEnum $typeRequis;

    #[ORM\Column(type: "decimal", precision: 15, scale: 2)]
    private string $poids;
    

    #[ORM\ManyToOne(targetEntity: Cargaison::class)]
    #[ORM\JoinColumn(name: "id_cargaison", referencedColumnName: "id_cargaison", nullable: false, onDelete: "CASCADE")]
    private Cargaison $cargaison;
    

    public function getIdMarchandise(): int { return $this->idMarchandise; }
    public function getNom(): string { return $this->nom; }
    public function getTypeRequis(): CamionTypeEnum { return $this->typeRequis; }
    public function getPoids(): float { return $this->poids; }
    public function getCargaison(): Cargaison { return $this->cargaison; }

    public function setNom(string $nom): self { $this->nom = $nom; return $this; }
    public function setTypeRequis(CamionTypeEnum $typeRequis): self { $this->typeRequis = $typeRequis; return $this; }
    public function setPoids(float $poids): self 
    { 
        if ($poids <= 0) {
            throw new \InvalidArgumentException("Le poids doit être supérieur à 0.");
        }
        $this->poids = $poids;
        return $this; 
    }
    public function setCargaison(Cargaison $cargaison): self { $this->cargaison = $cargaison; return $this; }
}
