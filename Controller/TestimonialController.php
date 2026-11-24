<?php
require_once __DIR__ . '/../Model/TestimonialModel.php';

class TestimonialController
{
    private TestimonialModel $model;

    public function __construct(PDO $pdo)
    {
        $this->model = new TestimonialModel($pdo);
    }

    public function list(): void
    {
        requireBackendAuth();
        $rating = $_GET['rating'] ?? 'all';
        $testimonials = $this->model->getAll($rating);
        $satisfaction = $this->model->getSatisfactionStats();

        require __DIR__ . '/../View/BackOffice/layout/testimoniale.php';
    }
}
