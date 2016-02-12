<?php

namespace TMCms\Modules\Articles\Entity;

use TMCms\Orm\Entity;

/**
 * Class ArticleTagEntity
 * @package TMCms\Modules\Articles\Entity
 *
 * @method $this setArticleId(int $article_id)
 * @method $this setTagId(int $tag_id)
 */
class ArticleTagRelationEntity extends Entity
{
    protected $db_table = 'm_articles2tags';
}