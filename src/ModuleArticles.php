<?php

namespace TMCms\Modules\Articles;

use TMCms\Modules\Articles\Entity\ArticleCategoryEntityRepository;
use TMCms\Modules\IModule;
use TMCms\Traits\singletonInstanceTrait;

defined('INC') or exit;

class ModuleArticles implements IModule
{
    use singletonInstanceTrait;

    public static function getCategoryPairs($only_active = false) {
        $categories = new ArticleCategoryEntityRepository();
        if ($only_active) {
            $categories->setWhereActive(1);
        }
        $categories->addOrderByField('title');
        return $categories->getPairs('title');
    }
}