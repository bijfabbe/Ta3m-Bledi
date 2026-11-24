<?php require __DIR__ . '/layout/header.php'; ?>

<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;">
  <h1 style="margin:0;">Témoignages & Avis Clients</h1>
  <span class="btn btn-info">Mode lecture seule</span>
</div>

<div class="grid-2">
  <!-- Diagramme de satisfaction -->
  <div class="card">
    <div class="card-header">
      <span>Diagramme de Satisfaction</span>
      <strong style="color:#1cc88a;"><?= (int)$satisfaction['satisfactionRate'] ?>% satisfaits</strong>
    </div>
    <div class="card-body">
      <p><strong>Note moyenne :</strong> <?= e((string)$satisfaction['averageRating']) ?> / 5 ★ (<?= (int)$satisfaction['total'] ?> avis)</p>
      <?php
      $tot = max(1, (int)$satisfaction['total']);
      $bars = [
          ['label' => 'Satisfaits (4–5 ★)', 'count' => $satisfaction['satisfied'], 'color' => '#1cc88a'],
          ['label' => 'Neutres (3 ★)', 'count' => $satisfaction['neutral'], 'color' => '#f6c23e'],
          ['label' => 'À améliorer (1–2 ★)', 'count' => $satisfaction['unsatisfied'], 'color' => '#e74a3b'],
      ];
      foreach ($bars as $b):
          $pct = round(($b['count'] / $tot) * 100);
      ?>
        <div style="margin-bottom:0.75rem;">
          <div style="display:flex;justify-content:space-between;font-size:0.85rem;margin-bottom:4px;">
            <span><?= e($b['label']) ?></span>
            <strong><?= (int)$b['count'] ?> (<?= $pct ?>%)</strong>
          </div>
          <div class="progress">
            <div class="progress-bar" style="width:<?= $pct ?>%;background:<?= $b['color'] ?>;"></div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <!-- Liste en lecture seule -->
  <div class="card">
    <div class="card-header">
      <span>Avis des clients (<?= count($testimonials) ?>)</span>
      <div style="display:flex;gap:4px;">
        <a class="btn btn-light" href="index.php?page=testimonials&rating=all">Tous</a>
        <a class="btn btn-light" href="index.php?page=testimonials&rating=5">5 ★</a>
        <a class="btn btn-light" href="index.php?page=testimonials&rating=4">4 ★</a>
        <a class="btn btn-light" href="index.php?page=testimonials&rating=low">1–2 ★</a>
      </div>
    </div>
    <div class="card-body">
      <?php foreach ($testimonials as $t): ?>
        <div style="padding:0.75rem 0;border-bottom:1px solid #e3e6f0;">
          <div style="display:flex;justify-content:space-between;font-size:0.85rem;">
            <span><strong><?= e($t['author']) ?></strong> · <?= e($t['region']) ?> · <?= e($t['variety_name']) ?></span>
            <strong style="color:#f6c23e;"><?= (int)$t['rating'] ?>/5 ★</strong>
          </div>
          <p style="margin:6px 0 0;font-size:0.875rem;"><?= e($t['text']) ?></p>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<?php require __DIR__ . '/layout/footer.php'; ?>
