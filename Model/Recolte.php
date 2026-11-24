<?php
/*
 * Model : Recolte
 * Représente une ligne de la table "recolte".
 */
class Recolte
{
    private ?int $id;
    private string $culture;
    private string $datePlantation;   // format AAAA-MM-JJ
    private string $dateRecolte;      // format AAAA-MM-JJ
    private float $quantite;
    private string $unite;
    private int $idTerrain;

    public function __construct(
        string $culture,
        string $datePlantation,
        string $dateRecolte,
        float $quantite,
        string $unite,
        int $idTerrain,
        ?int $id = null
    ) {
        $this->id             = $id;
        $this->culture        = $culture;
        $this->datePlantation = $datePlantation;
        $this->dateRecolte    = $dateRecolte;
        $this->quantite       = $quantite;
        $this->unite          = $unite;
        $this->idTerrain      = $idTerrain;
    }

    // ----- Getters -----
    public function getId(): ?int              { return $this->id; }
    public function getCulture(): string       { return $this->culture; }
    public function getDatePlantation(): string{ return $this->datePlantation; }
    public function getDateRecolte(): string   { return $this->dateRecolte; }
    public function getQuantite(): float       { return $this->quantite; }
    public function getUnite(): string         { return $this->unite; }
    public function getIdTerrain(): int        { return $this->idTerrain; }

    // ----- Setters -----
    public function setId(?int $id): void                { $this->id = $id; }
    public function setCulture(string $culture): void    { $this->culture = $culture; }
    public function setDatePlantation(string $d): void   { $this->datePlantation = $d; }
    public function setDateRecolte(string $d): void      { $this->dateRecolte = $d; }
    public function setQuantite(float $quantite): void   { $this->quantite = $quantite; }
    public function setUnite(string $unite): void        { $this->unite = $unite; }
    public function setIdTerrain(int $idTerrain): void   { $this->idTerrain = $idTerrain; }
}
