<?php

namespace TMCms\Modules\Articles\Entity;

use TMCms\Orm\EntityRepository;

/**
 * Class ArticleTagRelationEntityRepository
 * @package TMCms\Modules\Articles\Entity
 *
 * @method $this setWhereArticleId(int $article_id)
 * @method $this setWhereTagId(int $tag_id)
 */
class ArticleTagRelationEntityRepository extends EntityRepository
{
    protected $db_table = 'm_articles2tags';
    protected $table_structure = [
        'fields' => [
            'article_id' => [
                'type' => 'index',
            ],
            'tag_id' => [
                'type' => 'index',
            ],
        ],
    ];
}