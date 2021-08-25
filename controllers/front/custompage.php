<?php
use App\Controllers\RestController;
/**
 * LelerestapiCustomPageModuleFrontController
 */
class LelerestapiInitModuleFrontController extends RestController
{

    public function proccessGetMethod()
    {
        $slides = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
        SELECT hs.`id_homeslider_slides` as id_slide, hss.`position`, hss.`active`, hssl.`title`,
        hssl.`url`, hssl.`legend`, hssl.`description`, hssl.`image`
        FROM '._DB_PREFIX_.'homeslider hs
        LEFT JOIN '._DB_PREFIX_.'homeslider_slides hss ON (hs.id_homeslider_slides = hss.id_homeslider_slides)
        LEFT JOIN '._DB_PREFIX_.'homeslider_slides_lang hssl ON (hss.id_homeslider_slides = hssl.id_homeslider_slides)
        WHERE id_shop = '.(int)$this->context->shop->id.'
        AND hssl.id_lang = '.(int)$this->context->language->id.
        ' AND hss.`active` = 1
        ORDER BY hss.position');

        foreach ($slides as &$slide) {
            $slide['image_url'] = $this->context->link->getMediaLink(_MODULE_DIR_.'ps_imageslider/images/'.$slide['image']);
        }
       $this->response->setResult('homeslider', $slides);
       $this->response->returnResponse();
    }
    public function processPostMethod()
    {
        $this->response->return403Error();
    }
}