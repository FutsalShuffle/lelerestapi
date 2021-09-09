<?php
namespace App\Services;

class IndexService
{
    public $context;

    public function __construct($context)
    {
        $this->context = $context;
    }
    
    /**
     * Данные по категории
     * @return array
     */
    public function getIndexPage()
    {
        $slides = \Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
            SELECT hs.`id_homeslider_slides` as id_slide, hss.`position`, hss.`active`, hssl.`title`,
            hssl.`url`, hssl.`legend`, hssl.`description`, hssl.`image`
            FROM '._DB_PREFIX_.'homeslider hs
            LEFT JOIN '._DB_PREFIX_.'homeslider_slides hss ON (hs.id_homeslider_slides = hss.id_homeslider_slides)
            LEFT JOIN '._DB_PREFIX_.'homeslider_slides_lang hssl ON (hss.id_homeslider_slides = hssl.id_homeslider_slides)
            WHERE id_shop = '.(int)$this->context->shop->id.'
            AND hssl.id_lang = '.(int)$this->context->language->id.
            ' AND hss.`active` = 1
            ORDER BY hss.position'
        );

        foreach ($slides as &$slide) {
            $slide['image_url'] = $this->context->link->getMediaLink(_MODULE_DIR_.'ps_imageslider/images/'.$slide['image']);
        }

        $products = [];
        $category = new Category((int)\Configuration::get('HOME_FEATURED_CAT'), (int)$this->context->language->id);
        $nb = (int)\Configuration::get('HOME_FEATURED_NBR');
        if ($nb) {
            if (\Configuration::get('HOME_FEATURED_RANDOMIZE'))
                $products = $category->getProducts((int)$this->context->language->id, 1, ($nb ? $nb : 8), null, null, false, true, true, ($nb ? $nb : 8));
            else
                $products = $category->getProducts((int)$this->context->language->id, 1, ($nb ? $nb : 8), 'position');
        }

        return [
            'homeslider' => $slides,
            'featuredproducts' => $products
        ];
    }
}