<?php
/*
 * Model : Terrain
 * Représente une ligne de la table "terrain".
 */
class Terrain
{
    private ?int $id;
    private string $nom;
    private string $localisation;
    private float $superficie;
    private string $typeSol;
    private int $idUtilisateur;

    public function __construct(
        string $nom,
        string $localisation,
        float $superficie,
        string $typeSol,
        int $idUtilisateur,
        ?int $id = null
    ) {
        $this->id            = $id;
        $this->nom           = $nom;
        $this->localisation  = $localisation;
        $this->superficie    = $superficie;
        $this->typeSol       = $typeSol;
        $this->idUtilisateur = $idUtilisateur;
    }

    // ----- Getters -----
    public function getId(): ?int            { return $this->id; }
    public function getNom(): string         { return $this->nom; }
    public function getLocalisation(): string{ return $this->localisation; }
    public function getSuperficie(): float   { return $this->superficie; }
    public function getTypeSol(): string     { return $this->typeSol; }
    public function getIdUtilisateur(): int  { return $this->idUtilisateur; }

    // ----- Setters -----
    public function setId(?int $id): void                  { $this->id = $id; }
    public function setNom(string $nom): void              { $this->nom = $nom; }
    public function setLocalisation(string $loc): void     { $this->localisation = $loc; }
    public function setSuperficie(float $superficie): void { $this->superficie = $superficie; }
    public function setTypeSol(string $typeSol): void      { $this->typeSol = $typeSol; }
    public function setIdUtilisateur(int $id): void        { $this->idUtilisateur = $id; }
}
