<?php

declare(strict_types=1);

namespace App\Menu;

use Knp\Menu\ItemInterface;
use Nines\UtilBundle\Menu\AbstractBuilder;

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
            'attributes' => [
                'class' => 'nav-item',
            ],
            'linkAttributes' => [
                'class' => 'nav-link',
            ],
        ]);

        $browse = $menu->addChild('browse', [
            'uri' => '#',
            'label' => 'Browse',
            'attributes' => [
                'class' => 'nav-item dropdown',
            ],
            'linkAttributes' => [
                'class' => 'nav-link dropdown-toggle',
                'role' => 'button',
                'data-bs-toggle' => 'dropdown',
                'id' => 'browse-dropdown',
            ],
            'childrenAttributes' => [
                'class' => 'dropdown-menu text-small shadow',
                'aria-labelledby' => 'browse-dropdown',
            ],
        ]);
        $browse->addChild('Item', [
            'route' => 'item_index',
            'linkAttributes' => [
                'class' => 'dropdown-item',
            ],
        ]);
        $browse->addChild('Category', [
            'route' => 'category_index',
            'linkAttributes' => [
                'class' => 'dropdown-item',
            ],
        ]);
        $browse->addChild('Culture', [
            'route' => 'civilization_index',
            'linkAttributes' => [
                'class' => 'dropdown-item',
            ],
        ]);
        $browse->addChild('Inscription Style', [
            'route' => 'inscription_style_index',
            'linkAttributes' => [
                'class' => 'dropdown-item',
            ],
        ]);
        $browse->addChild('Language', [
            'route' => 'language_index',
            'linkAttributes' => [
                'class' => 'dropdown-item',
            ],
        ]);
        $browse->addChild('Location', [
            'route' => 'location_index',
            'linkAttributes' => [
                'class' => 'dropdown-item',
            ],
        ]);
        $browse->addChild('Material', [
            'route' => 'material_index',
            'linkAttributes' => [
                'class' => 'dropdown-item',
            ],
        ]);
        $browse->addChild('Period', [
            'route' => 'period_index',
            'linkAttributes' => [
                'class' => 'dropdown-item',
            ],
        ]);
        $browse->addChild('Subject', [
            'route' => 'subject_index',
            'linkAttributes' => [
                'class' => 'dropdown-item',
            ],
        ]);
        $browse->addChild('Technique', [
            'route' => 'technique_index',
            'linkAttributes' => [
                'class' => 'dropdown-item',
            ],
        ]);

        if ($this->hasRole('ROLE_CONTENT_ADMIN')) {
            $browse->addChild('divider1', [
                'label' => '<hr class="dropdown-divider">',
                'extras' => [
                    'safe_label' => true,
                ],
            ]);
            $browse->addChild('People', [
                'route' => 'person_index',
                'linkAttributes' => [
                    'class' => 'dropdown-item',
                ],
            ]);
            $browse->addChild('Image', [
                'route' => 'image_index',
                'linkAttributes' => [
                    'class' => 'dropdown-item',
                ],
            ]);
            $browse->addChild('Remote Image', [
                'route' => 'remote_image_index',
                'linkAttributes' => [
                    'class' => 'dropdown-item',
                ],
            ]);
        }

        return $menu;
    }
}
