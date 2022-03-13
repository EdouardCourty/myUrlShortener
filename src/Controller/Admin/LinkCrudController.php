<?php

namespace App\Controller\Admin;

use App\Entity\Link;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class LinkCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Link::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', 'ID')->hideOnForm();
        yield DateTimeField::new('createdAt', 'Creation date')->hideOnForm();
        yield DateTimeField::new('updatedAt', 'Update date')->hideOnForm();
        yield TextField::new('url', 'Redirect URL');
    }
}
