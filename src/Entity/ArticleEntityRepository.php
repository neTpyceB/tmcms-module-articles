<?php

namespace TMCms\Modules\Articles\Entity;

use TMCms\Orm\EntityRepository;

/**
 * Class ArticleEntityRepository
 * @package TMCms\Modules\Articles\Entity
 *
 * @method $this setWhereCategoryId(int $category_id)
 */
class ArticleEntityRepository extends EntityRepository
{
    protected $translation_fields = ['title', 'description', 'text'];
    protected $table_structure = [
        'fields' => [
            'category_id' => [
                'type' => 'index',
            ],
            'title' => [
                'type' => 'translation',
            ],
            'description' => [
                'type' => 'translation',
            ],
            'text' => [
                'type' => 'translation',
            ],
            'ts_created' => [
                'type' => 'int',
                'unsigned' => true,
            ],
            'active' => [
                'type' => 'bool',
            ],
        ],
    ];
}