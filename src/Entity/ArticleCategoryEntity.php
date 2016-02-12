<?php

namespace TMCms\Modules\Articles\Entity;

use TMCms\Orm\Entity;

/**
 * Class ArticleCategoryEntity
 * @package TMCms\Modules\Articles\Entity
 *
 * @method string getTitle()
 */
class ArticleCategoryEntity extends Entity
{
    protected $db_table = 'm_articles_categories';
    protected $translation_fields = ['title'];
}