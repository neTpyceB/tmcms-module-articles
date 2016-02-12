<?php

namespace TMCms\Modules\Articles\Entity;

use TMCms\Orm\Entity;

/**
 * Class ArticleTagEntity
 * @package TMCms\Modules\Articles\Entity
 *
 * @method string getTitle()
 */
class ArticleTagEntity extends Entity
{
    protected $db_table = 'm_articles_tags';
    protected $translation_fields = ['title'];
}