<?php require __DIR__ . '/layout/header.php'; ?>

<h1 style="margin-top:0;">Tableau de bord</h1>

<!-- 4 Cartes KPI SB Admin 2 -->
<div class="grid-4" style="margin-bottom:1.5rem;">
  <div class="card border-left-primary"><div class="card-body">
    <small style="color:#4e73df;font-weight:800;">VARIÉTÉS</small>
    <div style="font-size:1.5rem;font-weight:800;"><?= (int)$sales['totalVarieties'] ?></div>
  </div></div>

  <div class="card border-left-success"><div class="card-body">
    <small style="color:#1cc88a;font-weight:800;">VENTES / ADOPTIONS</small>
    <div style="font-size:1.5rem;font-weight:800;"><?= (int)$sales['totalSales'] ?></div>
  </div></div>

  <div class="card border-left-info"><div class="card-body">
    <small style="color:#36b9cc;font-weight:800;">SATISFACTION</small>
    <div style="font-size:1.5rem;font-weight:800;"><?= (int)$satisfaction['satisfactionRate'] ?>%</div>
  </div></div>

  <div class="card border-left-warning"><div class="card-body">
    <small style="color:#f6c23e;font-weight:800;">AVIS CLIENTS (LECTURE)</small>
    <div style="font-size:1.5rem;font-weight:800;"><?= (int)$satisfaction['total'] ?></div>
  </div></div>
</div>

<!-- 2 Diagrammes : Salling Produit & Satisfaction -->
<div class="grid-2">
  <div class="card">
    <div class="card-header">
      <span>Diagramme de Ventes Produits (Salling Produit)</span>
      <?php if (isAdmin()): ?>
        <small style="color:#1cc88a;">CA : <?= number_format((float)$sales['totalRevenue'], 0, ',', ' ') ?> DT</small>
      <?php endif; ?>
    </div>
    <div class="card-body">
      <?php
      $top = $sales['topProducts'];
      $maxSales = !empty($top) ? max(10, (int)$top[0]['sales_count']) : 10;
      foreach ($top as $p):
          $pct = max(5, round(((int)$p['sales_count'] / $maxSales) * 100));
      ?>
        <div style="margin-bottom:0.8rem;">
          <div style="display:flex;justify-content:space-between;font-size:0.8rem;margin-bottom:4px;">
            <span>
              <?php if ($p['created_by_role'] === 'farmer'): ?>
                <span class="farmer-tag">(ajouté par un agriculteur)</span>
              <?php endif; ?>
              <strong><?= e($p['name']) ?></strong> · <?= e($p['cat']) ?>
            </span>
            <span>
              <strong><?= (int)$p['sales_count'] ?> ventes</strong>
              <?php if (isAdmin()): ?>
                <span style="color:#1cc88a;">(<?= number_format((float)$p['revenue'], 0, ',', ' ') ?> DT)</span>
              <?php endif; ?>
            </span>
          </div>
          <div class="progress">
            <div class="progress-bar" style="width:<?= $pct ?>%;background:#4e73df;"></div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <div class="card">
    <div class="card-header">
      <span>Diagramme de Satisfaction Clients</span>
      <span style="color:#1cc88a;"><?= (int)$satisfaction['satisfactionRate'] ?>% satisfaits</span>
    </div>
    <div class="card-body">
      <?php
      $tot = max(1, (int)$satisfaction['total']);
      $pSat = round(($satisfaction['satisfied'] / $tot) * 100);
      $pNeu = round(($satisfaction['neutral'] / $tot) * 100);
      $pBad = max(0, 100 - $pSat - $pNeu);
      ?>
      <div style="display:flex;align-items:center;gap:1.5rem;flex-wrap:wrap;">
        <div style="width:120px;height:120px;border-radius:50%;padding:16px;
             background:conic-gradient(#1cc88a 0% <?= $pSat ?>%, #f6c23e <?= $pSat ?>% <?= $pSat + $pNeu ?>%, #e74a3b <?= $pSat + $pNeu ?>% 100%);">
          <div style="width:100%;height:100%;border-radius:50%;background:#fff;display:flex;flex-direction:column;align-items:center;justify-content:center;">
            <strong style="font-size:1.3rem;"><?= e((string)$satisfaction['averageRating']) ?></strong>
            <small>/ 5 ★</small>
          </div>
        </div>
        <div style="flex:1;font-size:0.85rem;display:grid;gap:0.5rem;">
          <div><strong style="color:#1cc88a;">Satisfaits (4–5 ★) :</strong> <?= (int)$satisfaction['satisfied'] ?> (<?= $pSat ?>%)</div>
          <div><strong style="color:#f6c23e;">Neutres (3 ★) :</strong> <?= (int)$satisfaction['neutral'] ?> (<?= $pNeu ?>%)</div>
          <div><strong style="color:#e74a3b;">À améliorer (1–2 ★) :</strong> <?= (int)$satisfaction['unsatisfied'] ?> (<?= $pBad ?>%)</div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Derniers témoignages (Lecture seule) -->
<div class="card">
  <div class="card-header">
    <span>Derniers témoignages clients (Lecture seule)</span>
    <a href="index.php?page=testimonials" class="btn btn-light">Voir tout</a>
  </div>
  <div class="card-body">
    <?php foreach ($recentTestimonials as $t): ?>
      <div style="padding:0.6rem 0;border-bottom:1px solid #e3e6f0;">
        <strong><?= e($t['author']) ?></strong> (<?= e($t['region']) ?> · <?= e($t['variety_name']) ?>) —
        <strong style="color:#f6c23e;"><?= (int)$t['rating'] ?>/5 ★</strong>
        <p style="margin:4px 0 0;font-size:0.875rem;"><?= e($t['text']) ?></p>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<?php require __DIR__ . '/layout/footer.php'; ?>
