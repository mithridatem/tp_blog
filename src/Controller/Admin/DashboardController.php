<?php

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Media;
use App\Entity\Commentary;
use App\Entity\User;
class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        $url = $this->adminUrlGenerator
        ->setController(ArticleCrudController::class)
        ->generateUrl();
        return $this->redirect($url);
    }


    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Tpblog');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Articles', 'fas fa-list', Article::class);
        yield MenuItem::linkToCrud('Categories', 'fas fa-newspaper', Category::class);
        yield MenuItem::linkToCrud('Utilisateurs', 'fas fa-users', User::class);
        yield MenuItem::linkToCrud('Medias', 'fas fa-users', Media::class);
        yield MenuItem::linkToCrud('Commentaires', 'fas fa-users', Commentary::class);

    }
    public function __construct(private AdminUrlGenerator $adminUrlGenerator){}
}