<?php
/*
 * Model : Attribution
 * Un badge attribué à un utilisateur (table "utilisateur_badge").
 */
class Attribution
{
    private ?int $id;
    private int $idUtilisateur;
    private int $idBadge;
    private string $motif;

    public function __construct(int $idUtilisateur, int $idBadge, string $motif, ?int $id = null)
    {
        $this->id            = $id;
        $this->idUtilisateur = $idUtilisateur;
        $this->idBadge       = $idBadge;
        $this->motif         = $motif;
    }

    public function getId(): ?int           { return $this->id; }
    public function getIdUtilisateur(): int { return $this->idUtilisateur; }
    public function getIdBadge(): int       { return $this->idBadge; }
    public function getMotif(): string      { return $this->motif; }

    public function setId(?int $id): void            { $this->id = $id; }
    public function setIdUtilisateur(int $id): void  { $this->idUtilisateur = $id; }
    public function setIdBadge(int $id): void        { $this->idBadge = $id; }
    public function setMotif(string $motif): void    { $this->motif = $motif; }
}
