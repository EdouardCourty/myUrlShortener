<?php

namespace App\Controller\Admin;

use App\Entity\Link;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(host: 'admin.%app_domain%')]
class DashboardController extends AbstractDashboardController
{
    public function __construct(
        private AdminUrlGenerator $adminUrlGenerator
    ) {
    }

    #[Route('/', name: 'admin_dashboard')]
    public function index(): Response
    {
        return $this->redirect(
            $this->adminUrlGenerator->setController(LinkCrudController::class)->set('menuIndex', 2)->generateUrl()
        );
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('URL Shortener');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToUrl('Public homepage', 'fa fa-home', $this->generateUrl('homepage'));

        yield MenuItem::section('CRUD');

        yield MenuItem::linkToCrud('Redirections', 'fa fa-link', Link::class);
    }
}
