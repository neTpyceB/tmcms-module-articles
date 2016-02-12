<?php

namespace TMCms\Modules\Articles\Entity;

use TMCms\Orm\EntityRepository;

class ArticleCategoryEntityRepository extends EntityRepository
{
    protected $db_table = 'm_articles_categories';
    protected $translation_fields = ['title'];
    protected $table_structure = [
        'fields' => [
            'title' => [
                'type' => 'translation',
            ],
            'active' => [
                'type' => 'bool',
            ],
        ],
    ];
}