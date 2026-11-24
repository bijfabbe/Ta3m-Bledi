<?php
/*
 * Formulaire Terrain (utilisé par ajouterTerrain.php ET modifierTerrain.php)
 * Variables attendues : $valeurs (tableau), $erreurs (tableau), $texteBouton (texte)
 * ⚠️ Pas de validation HTML5 (required, type="number"...) : contrôle en JS + PHP.
 */
?>
<form action="" method="POST" id="formTerrain" novalidate>
  <?= csrfField() ?>
  <div class="row">

    <div class="col-md-6 mb-4">
      <label for="nom" class="form-label">Nom du terrain</label>
      <input type="text" class="form-control <?= isset($erreurs['nom']) ? 'is-invalid' : '' ?>"
             id="nom" name="nom" value="<?= e($valeurs['nom']) ?>" placeholder="Ex : Terrain Zaghouan">
      <div class="erreur" id="erreur-nom"><?= e($erreurs['nom'] ?? '') ?></div>
    </div>

    <div class="col-md-6 mb-4">
      <label for="localisation" class="form-label">Localisation (region)</label>
      <select class="form-select <?= isset($erreurs['localisation']) ? 'is-invalid' : '' ?>"
              id="localisation" name="localisation">
        <option value="">-- Choisir une region --</option>
        <?php foreach (TerrainController::WILAYAS as $region): ?>
          <option value="<?= e($region) ?>" <?= $valeurs['localisation'] === $region ? 'selected' : '' ?>>
            <?= e($region) ?>
          </option>
        <?php endforeach; ?>
      </select>
      <div class="erreur" id="erreur-localisation"><?= e($erreurs['localisation'] ?? '') ?></div>
    </div>

    <div class="col-md-6 mb-4">
      <label for="superficie" class="form-label">Superficie (ha)</label>
      <input type="text" class="form-control <?= isset($erreurs['superficie']) ? 'is-invalid' : '' ?>"
             id="superficie" name="superficie" value="<?= e($valeurs['superficie']) ?>" placeholder="Ex : 2.5">
      <div class="erreur" id="erreur-superficie"><?= e($erreurs['superficie'] ?? '') ?></div>
    </div>

    <div class="col-md-6 mb-4">
      <label for="typeSol" class="form-label">Type de sol</label>
      <select class="form-select <?= isset($erreurs['typeSol']) ? 'is-invalid' : '' ?>"
              id="typeSol" name="typeSol">
        <option value="">-- Choisir --</option>
        <?php foreach (TerrainController::TYPES_SOL as $type): ?>
          <option value="<?= e($type) ?>" <?= $valeurs['typeSol'] === $type ? 'selected' : '' ?>>
            <?= e($type) ?>
          </option>
        <?php endforeach; ?>
      </select>
      <div class="erreur" id="erreur-typeSol"><?= e($erreurs['typeSol'] ?? '') ?></div>
    </div>

  </div>

  <div class="pied">
    <a href="mesTerrains.php" class="lien-details">Annuler</a>
    <button type="submit" class="btn-hero border-0"><?= e($texteBouton) ?></button>
  </div>
</form>

<script src="assets/js/validation-terrain.js"></script>
