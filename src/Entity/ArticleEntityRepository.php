<?php

namespace TMCms\Modules\Articles\Entity;

use TMCms\Orm\EntityRepository;

class ArticleEntityRepository extends EntityRepository
{
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