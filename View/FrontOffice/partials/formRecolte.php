<?php
/*
 * Formulaire Récolte (utilisé par ajouterRecolte.php ET modifierRecolte.php)
 * Variables attendues : $valeurs, $erreurs, $texteBouton, $terrains (terrains de l'utilisateur)
 * ⚠️ Pas de validation HTML5 : contrôle en JS + PHP.
 */
?>
<form action="" method="POST" id="formRecolte" novalidate>
  <?= csrfField() ?>
  <div class="row">

    <div class="col-md-6 mb-4">
      <label for="idTerrain" class="form-label">Terrain</label>
      <select class="form-select <?= isset($erreurs['idTerrain']) ? 'is-invalid' : '' ?>"
              id="idTerrain" name="idTerrain">
        <option value="">-- Choisir un terrain --</option>
        <?php foreach ($terrains as $t): ?>
          <option value="<?= e($t['id']) ?>" <?= (string) $valeurs['idTerrain'] === (string) $t['id'] ? 'selected' : '' ?>>
            <?= e($t['nom']) ?> (<?= e($t['localisation']) ?>)
          </option>
        <?php endforeach; ?>
      </select>
      <div class="erreur" id="erreur-idTerrain"><?= e($erreurs['idTerrain'] ?? '') ?></div>
    </div>

    <div class="col-md-6 mb-4">
      <label for="culture" class="form-label">Culture</label>
      <input type="text" class="form-control <?= isset($erreurs['culture']) ? 'is-invalid' : '' ?>"
             id="culture" name="culture" value="<?= e($valeurs['culture']) ?>" placeholder="Ex : Blé">
      <div class="erreur" id="erreur-culture"><?= e($erreurs['culture'] ?? '') ?></div>
    </div>

    <div class="col-md-6 mb-4">
      <label for="datePlantation" class="form-label">Date de plantation</label>
      <input type="date" class="form-control <?= isset($erreurs['datePlantation']) ? 'is-invalid' : '' ?>"
             id="datePlantation" name="datePlantation" value="<?= e($valeurs['datePlantation']) ?>">
      <div class="erreur" id="erreur-datePlantation"><?= e($erreurs['datePlantation'] ?? '') ?></div>
    </div>

    <div class="col-md-6 mb-4">
      <label for="dateRecolte" class="form-label">Date de récolte</label>
      <input type="date" class="form-control <?= isset($erreurs['dateRecolte']) ? 'is-invalid' : '' ?>"
             id="dateRecolte" name="dateRecolte" value="<?= e($valeurs['dateRecolte']) ?>">
      <div class="erreur" id="erreur-dateRecolte"><?= e($erreurs['dateRecolte'] ?? '') ?></div>
    </div>

    <div class="col-md-8 mb-4">
      <label for="quantite" class="form-label">Quantité</label>
      <input type="text" class="form-control <?= isset($erreurs['quantite']) ? 'is-invalid' : '' ?>"
             id="quantite" name="quantite" value="<?= e($valeurs['quantite']) ?>" placeholder="Ex : 800">
      <div class="erreur" id="erreur-quantite"><?= e($erreurs['quantite'] ?? '') ?></div>
    </div>

    <div class="col-md-4 mb-4">
      <label for="unite" class="form-label">Unité</label>
      <select class="form-select <?= isset($erreurs['unite']) ? 'is-invalid' : '' ?>" id="unite" name="unite">
        <?php foreach (RecolteController::UNITES as $u): ?>
          <option value="<?= e($u) ?>" <?= $valeurs['unite'] === $u ? 'selected' : '' ?>><?= e($u) ?></option>
        <?php endforeach; ?>
      </select>
      <div class="erreur" id="erreur-unite"><?= e($erreurs['unite'] ?? '') ?></div>
    </div>

  </div>

  <div class="pied">
    <a href="mesRecoltes.php" class="lien-details">Annuler</a>
    <button type="submit" class="btn-hero border-0"><?= e($texteBouton) ?></button>
  </div>
</form>

<script src="assets/js/validation-recolte.js"></script>
