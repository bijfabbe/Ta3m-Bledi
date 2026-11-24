<?php require __DIR__ . '/layout/header.php'; ?>

<h1 style="margin-top:0;">Gestion des Variétés / Produits (CRUD)</h1>

<?php if (!empty($flash)): ?>
  <div class="alert alert-success"><?= e($flash) ?></div>
<?php endif; ?>

<!-- Fiche Lire (Détails) -->
<?php if ($viewItem): ?>
  <div class="card border-left-info">
    <div class="card-header">
      <span>
        Fiche :
        <?php if ($viewItem['created_by_role'] === 'farmer'): ?>
          <span class="farmer-tag">(ajouté par un agriculteur)</span>
        <?php endif; ?>
        <?= e($viewItem['name']) ?>
      </span>
      <a href="index.php?page=varieties" class="btn btn-light">Fermer</a>
    </div>
    <div class="card-body">
      <p><strong>Catégorie :</strong> <?= e($viewItem['cat']) ?> · <strong>Régions :</strong> <?= e($viewItem['regions']) ?></p>
      <p><?= e($viewItem['description']) ?></p>
      <p>
        <?php if (isAdmin()): ?>
          <strong>Prix :</strong> <?= e((string)$viewItem['price']) ?> DT ·
        <?php endif; ?>
        <strong>Ventes :</strong> <?= (int)$viewItem['sales_count'] ?> ·
        <strong>Stock :</strong> <?= (int)$viewItem['stock'] ?>
      </p>
    </div>
  </div>
<?php endif; ?>

<!-- Formulaire Ajouter / Modifier -->
<div class="card">
  <div class="card-header">
    <span><?= $editItem ? 'Modifier la variété' : 'Ajouter une nouvelle variété' ?></span>
    <?php if ($editItem): ?>
      <a href="index.php?page=varieties" class="btn btn-light">Annuler</a>
    <?php endif; ?>
  </div>
  <div class="card-body">
    <form method="post" action="index.php?page=varieties&action=<?= $editItem ? 'update' : 'create' ?>"
          style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:0.75rem;">
      <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
      <?php if ($editItem): ?>
        <input type="hidden" name="id" value="<?= (int)$editItem['id'] ?>">
      <?php endif; ?>

      <input class="form-control" type="text" name="name" placeholder="Nom *" required
             value="<?= e($editItem['name'] ?? '') ?>">

      <select class="form-control" name="cat">
        <?php foreach (['Céréales', 'Oliviers', 'Fruits', 'Légumes', 'Plantes'] as $c): ?>
          <option value="<?= e($c) ?>" <?= ($editItem['cat'] ?? '') === $c ? 'selected' : '' ?>><?= e($c) ?></option>
        <?php endforeach; ?>
      </select>

      <input class="form-control" type="text" name="regions" placeholder="Régions *" required
             value="<?= e($editItem['regions'] ?? '') ?>">

      <?php if (isAdmin()): ?>
        <input class="form-control" type="number" step="0.5" min="0" name="price" placeholder="Prix (DT)"
               value="<?= e((string)($editItem['price'] ?? '20')) ?>">
      <?php endif; ?>

      <input class="form-control" type="number" min="0" name="sales_count" placeholder="Ventes"
             value="<?= e((string)($editItem['sales_count'] ?? '0')) ?>">

      <input class="form-control" type="number" min="0" name="stock" placeholder="Stock"
             value="<?= e((string)($editItem['stock'] ?? '50')) ?>">

      <input class="form-control" type="text" name="img" placeholder="Nom image (ex: tomate.jpg)"
             value="<?= e($editItem['img'] ?? '') ?>">

      <input class="form-control" type="text" name="description" placeholder="Description *" required
             value="<?= e($editItem['description'] ?? '') ?>" style="grid-column:span 2;">

      <button class="btn btn-primary" type="submit">
        <?= $editItem ? 'Mettre à jour' : 'Ajouter au catalogue' ?>
      </button>
    </form>
  </div>
</div>

<!-- Tableau Catalogue -->
<div class="card">
  <div class="card-header">
    <span>Catalogue (<?= count($varieties) ?>)</span>
    <form method="get" action="index.php" style="display:flex;gap:6px;">
      <input type="hidden" name="page" value="varieties">
      <input class="form-control" type="search" name="q" placeholder="Rechercher…" value="<?= e($search) ?>">
      <button class="btn btn-primary" type="submit">OK</button>
    </form>
  </div>
  <div class="card-body" style="padding:0;overflow-x:auto;">
    <table class="sb-table">
      <thead>
        <tr>
          <th>Produit</th>
          <th>Catégorie</th>
          <th>Régions</th>
          <?php if (isAdmin()): ?><th>Prix (DT)</th><?php endif; ?>
          <th>Ventes</th>
          <th>Stock</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($varieties as $v): ?>
          <tr>
            <td>
              <?php if ($v['created_by_role'] === 'farmer'): ?>
                <span class="farmer-tag">(ajouté par un agriculteur)</span>
              <?php endif; ?>
              <strong><?= e($v['name']) ?></strong>
            </td>
            <td><?= e($v['cat']) ?></td>
            <td><?= e($v['regions']) ?></td>
            <?php if (isAdmin()): ?>
              <td><strong><?= e((string)$v['price']) ?> DT</strong></td>
            <?php endif; ?>
            <td style="color:#1cc88a;font-weight:700;"><?= (int)$v['sales_count'] ?></td>
            <td><?= (int)$v['stock'] ?></td>
            <td style="white-space:nowrap;">
              <a class="btn btn-info" href="index.php?page=varieties&view=<?= (int)$v['id'] ?>">Lire</a>
              <a class="btn btn-primary" href="index.php?page=varieties&edit=<?= (int)$v['id'] ?>">Modifier</a>
              <form method="post" action="index.php?page=varieties&action=delete" style="display:inline;"
                    onsubmit="return confirm('Supprimer cette variété ?');">
                <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
                <input type="hidden" name="id" value="<?= (int)$v['id'] ?>">
                <button class="btn btn-danger" type="submit">Supprimer</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require __DIR__ . '/layout/footer.php'; ?>
