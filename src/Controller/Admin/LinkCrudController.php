<?php

namespace App\Controller\Admin;

use App\Entity\Link;
use App\Service\UrlHasher;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class LinkCrudController extends AbstractCrudController
{
    public function __construct(
        private UrlHasher $urlHasher,
        private UrlGeneratorInterface $urlGenerator
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return Link::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->showEntityActionsInlined()
            ->setEntityLabelInSingular('Link')
            ->setEntityLabelInPlural('Links')
            ->setPageTitle(Crud::PAGE_INDEX, 'Redirect links')
            ->setDefaultSort([
                'id' => 'ASC'
            ])
            ->setPaginatorPageSize(30);
    }

    public function configureActions(Actions $actions): Actions
    {
        $visitPageAction = Action::new('visitPage', 'See link page')
            ->linkToUrl(fn (Link $link) => $this->urlGenerator->generate('link_view', [
                'shortcode' => $link->getCustomShortcode() ?? $this->urlHasher->getHasher()->encode($link->getId())
            ], UrlGeneratorInterface::ABSOLUTE_URL));

        return $actions
            ->remove(Crud::PAGE_INDEX, Action::EDIT)
            ->remove(Crud::PAGE_INDEX, Action::DELETE)
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_INDEX, $visitPageAction)
            ->add(Crud::PAGE_DETAIL, $visitPageAction);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')->hideOnForm();
        yield TextField::new('customShortcode', 'Custom shortcode')->hideWhenUpdating();
        yield TextField::new('url', 'Redirect URL');
        yield NumberField::new('usageCount', 'Usage count')->hideOnForm();
        yield DateTimeField::new('createdAt', 'Creation date')->hideOnForm();
        yield DateTimeField::new('updatedAt', 'Update date')->hideOnForm();
    }
}
