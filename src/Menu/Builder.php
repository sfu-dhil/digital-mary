<?php

declare(strict_types=1);

/*
 * (c) 2021 Michael Joyce <mjoyce@sfu.ca>
 * This source file is subject to the GPL v2, bundled
 * with this source code in the file LICENSE.
 */

namespace App\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Nines\UtilBundle\Menu\AbstractBuilder;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Class to build some menus for navigation.
 */
class Builder extends AbstractBuilder {
    /**
     * Build a menu for navigation.
     *
     * @return ItemInterface
     */
    public function mainMenu(array $options) {
        $menu = $this->factory->createItem('root');
        $menu->setChildrenAttributes([
            'class' => 'nav navbar-nav',
        ]);

        $menu->addChild('home', [
            'label' => 'Home',
            'route' => 'homepage',
        ]);

        $browse = $menu->addChild('browse', [
            'uri' => '#',
            'label' => 'Browse',
        ]);
        $browse->setAttribute('dropdown', true);
        $browse->setLinkAttribute('class', 'dropdown-toggle');
        $browse->setLinkAttribute('data-toggle', 'dropdown');
        $browse->setChildrenAttribute('class', 'dropdown-menu');
        $browse->addChild('Item', [
            'route' => 'item_index',
        ]);
        $browse->addChild('Category', [
            'route' => 'category_index',
        ]);
        $browse->addChild('Culture', [
            'route' => 'civilization_index',
        ]);
        $browse->addChild('Inscription Style', [
            'route' => 'inscription_style_index',
        ]);
        $browse->addChild('Language', [
            'route' => 'language_index',
        ]);
        $browse->addChild('Location', [
            'route' => 'location_index',
        ]);
        $browse->addChild('Material', [
            'route' => 'material_index',
        ]);
        $browse->addChild('Period', [
            'route' => 'period_index',
        ]);
        $browse->addChild('Subject', [
            'route' => 'subject_index',
        ]);
        $browse->addChild('Technique', [
            'route' => 'technique_index',
        ]);

        if ($this->hasRole('ROLE_CONTENT_ADMIN')) {
            $browse->addChild('divider2', [
                'label' => '',
            ]);
            $browse['divider2']->setAttributes([
                'role' => 'separator',
                'class' => 'divider',
            ]);
            $browse->addChild('Image', [
                'route' => 'image_index',
            ]);
            $browse->addChild('Remote Image', [
                'route' => 'remote_image_index',
            ]);
        }

        return $menu;
    }
}
