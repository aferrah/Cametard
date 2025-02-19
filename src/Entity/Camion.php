<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Enum\CamionTypeEnum; // Ajoute cette ligne

#[ORM\Entity]
class Camion
{
    #[ORM\Id]
    #[ORM\Column(type: "string", length: 10)]
    private string $immat;

    #[ORM\Column(type: "string", length: 20, enumType: CamionTypeEnum::class)]
    private CamionTypeEnum $typeCamion;

    #[ORM\Column(type: "decimal", precision: 15, scale: 2)]
    private string $poidsTransport;
    

    public function getImmat(): string { return $this->immat; }
    public function getTypeCamion(): CamionTypeEnum { return $this->typeCamion; }
    public function getPoidsTransport(): float { return $this->poidsTransport; }

    public function setImmat(string $immat): self { $this->immat = $immat; return $this; }
    public function setTypeCamion(CamionTypeEnum $typeCamion): self { $this->typeCamion = $typeCamion; return $this; }
    public function setPoidsTransport(float $poidsTransport): self 
    { 
        if ($poidsTransport <= 0) {
            throw new \InvalidArgumentException("Le poids transporté doit être supérieur à 0.");
        }
        $this->poidsTransport = $poidsTransport;
        return $this; 
    }
}
