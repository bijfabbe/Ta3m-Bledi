<?php
/*
 * Model : Utilisateur
 * Représente une ligne de la table "utilisateur".
 * Le mot de passe est toujours stocké HACHÉ (password_hash), jamais en clair.
 */
class Utilisateur
{
    private ?int $id;
    private string $nom;
    private string $prenom;
    private string $email;
    private string $telephone;
    private string $motDePasse;   // hash
    private string $role;         // 'ADMIN' ou 'AGRICULTEUR'
    private string $statut;       // 'ACTIF' ou 'BLOQUE'
    private ?string $photo;

    public function __construct(
        string $nom,
        string $prenom,
        string $email,
        string $telephone,
        string $motDePasse = '',
        string $role = 'AGRICULTEUR',
        string $statut = 'ACTIF',
        ?string $photo = null,
        ?int $id = null
    ) {
        $this->id         = $id;
        $this->nom        = $nom;
        $this->prenom     = $prenom;
        $this->email      = $email;
        $this->telephone  = $telephone;
        $this->motDePasse = $motDePasse;
        $this->role       = $role;
        $this->statut     = $statut;
        $this->photo      = $photo;
    }

    // ----- Getters -----
    public function getId(): ?int          { return $this->id; }
    public function getNom(): string       { return $this->nom; }
    public function getPrenom(): string    { return $this->prenom; }
    public function getEmail(): string     { return $this->email; }
    public function getTelephone(): string { return $this->telephone; }
    public function getMotDePasse(): string{ return $this->motDePasse; }
    public function getRole(): string      { return $this->role; }
    public function getStatut(): string    { return $this->statut; }
    public function getPhoto(): ?string    { return $this->photo; }

    // ----- Setters -----
    public function setId(?int $id): void              { $this->id = $id; }
    public function setNom(string $nom): void          { $this->nom = $nom; }
    public function setPrenom(string $prenom): void    { $this->prenom = $prenom; }
    public function setEmail(string $email): void      { $this->email = $email; }
    public function setTelephone(string $tel): void    { $this->telephone = $tel; }
    public function setMotDePasse(string $hash): void  { $this->motDePasse = $hash; }
    public function setRole(string $role): void        { $this->role = $role; }
    public function setStatut(string $statut): void    { $this->statut = $statut; }
    public function setPhoto(?string $photo): void     { $this->photo = $photo; }
}
