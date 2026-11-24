<?php
/*
 * Model : Badge
 * Représente une ligne de la table "badge".
 */
class Badge
{
    private ?int $id;
    private string $nom;
    private string $description;
    private string $critere;
    private int $points;
    private ?string $icone;   // nom du fichier image (dossier assets/img/uploads/badges)

    public function __construct(
        string $nom,
        string $description,
        string $critere,
        int $points,
        ?string $icone = null,
        ?int $id = null
    ) {
        $this->id          = $id;
        $this->nom         = $nom;
        $this->description = $description;
        $this->critere     = $critere;
        $this->points      = $points;
        $this->icone       = $icone;
    }

    // ----- Getters -----
    public function getId(): ?int            { return $this->id; }
    public function getNom(): string         { return $this->nom; }
    public function getDescription(): string { return $this->description; }
    public function getCritere(): string     { return $this->critere; }
    public function getPoints(): int         { return $this->points; }
    public function getIcone(): ?string      { return $this->icone; }

    // ----- Setters -----
    public function setId(?int $id): void                 { $this->id = $id; }
    public function setNom(string $nom): void             { $this->nom = $nom; }
    public function setDescription(string $d): void       { $this->description = $d; }
    public function setCritere(string $c): void           { $this->critere = $c; }
    public function setPoints(int $points): void          { $this->points = $points; }
    public function setIcone(?string $icone): void        { $this->icone = $icone; }
}
