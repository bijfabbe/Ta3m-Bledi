<?php

require_once __DIR__ . '/../Model/VarietyModel.php';
require_once __DIR__ . '/../Model/TestimonialModel.php';

class VarietyController
{
    private VarietyModel $varietyModel;
    private TestimonialModel $testimonialModel;

    public function __construct(PDO $pdo)
    {
        $this->varietyModel = new VarietyModel($pdo);
        $this->testimonialModel = new TestimonialModel($pdo);
    }

    public function dashboard(): void
    {
        requireBackendAuth();
        $sales = $this->varietyModel->getSalesStats();
        $satisfaction = $this->testimonialModel->getSatisfactionStats();
        $recentTestimonials = array_slice($this->testimonialModel->getAll(), 0, 4);

        require __DIR__ . '/../View/BackOffice/layout/dashboard.php';
    }

    public function list(): void
    {
        requireBackendAuth();
        $category = $_GET['cat'] ?? 'Tous';
        $search = $_GET['q'] ?? '';
        $varieties = $this->varietyModel->getAll($category, $search);

        // Si on modifie ou consulte une fiche précise
        $editItem = isset($_GET['edit']) ? $this->varietyModel->findById((int)$_GET['edit']) : null;
        $viewItem = isset($_GET['view']) ? $this->varietyModel->findById((int)$_GET['view']) : null;
        $flash = $_SESSION['flash_success'] ?? null;
        unset($_SESSION['flash_success']);

        require __DIR__ . '/../View/BackOffice/layout/variete.php';
    }

    public function create(): void
    {
        requireBackendAuth();
        verifyCsrf();

        // Seul l'admin définit un prix ; un agriculteur reste à 0 et est marqué 'farmer'
        $price = isAdmin() ? max(0, (float)($_POST['price'] ?? 0)) : 0;
        $createdByRole = isAdmin() ? 'admin' : 'farmer';

        $this->varietyModel->create([
            'name' => $_POST['name'] ?? '',
            'cat' => $_POST['cat'] ?? 'Céréales',
            'regions' => $_POST['regions'] ?? '',
            'description' => $_POST['description'] ?? '',
            'img' => $_POST['img'] ?? '',
            'price' => $price,
            'sales_count' => $_POST['sales_count'] ?? 0,
            'stock' => $_POST['stock'] ?? 50,
            'created_by_role' => $createdByRole,
        ]);

        $_SESSION['flash_success'] = 'Variété ajoutée avec succès.';
        redirect('varieties');
    }

    public function update(): void
    {
        requireBackendAuth();
        verifyCsrf();

        $id = (int)($_POST['id'] ?? 0);
        $this->varietyModel->update($id, $_POST, isAdmin());

        $_SESSION['flash_success'] = 'Variété mise à jour.';
        redirect('varieties');
    }

    public function delete(): void
    {
        requireBackendAuth();
        verifyCsrf();

        $id = (int)($_POST['id'] ?? 0);
        $this->varietyModel->delete($id);

        $_SESSION['flash_success'] = 'Variété supprimée.';
        redirect('varieties');
    }
}
