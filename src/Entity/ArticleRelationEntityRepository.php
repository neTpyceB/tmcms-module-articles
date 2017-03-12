<?php

namespace TMCms\Modules\Articles\Entity;

use TMCms\Orm\EntityRepository;

/**
 * Class ArticleRelationEntityRepository
 * @package TMCms\Modules\Articles\Entity
 *
 * @method $this setWhereArticleId(int $article_id)
 * @method $this setWhereToArticleId(int $article_id)
 */
class ArticleRelationEntityRepository extends EntityRepository
{
    protected $table_structure = [
        'fields' => [
            'article_id' => [
                'type' => 'index',
            ],
            // Save relations to both directions for better sql selects
            'to_article_id' => [
                'type' => 'index',
            ],
        ],
    ];
}