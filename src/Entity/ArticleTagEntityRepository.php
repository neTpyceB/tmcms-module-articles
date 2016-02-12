<?php

namespace TMCms\Modules\Articles\Entity;

use TMCms\Orm\EntityRepository;

class ArticleTagEntityRepository extends EntityRepository
{
    protected $db_table = 'm_articles_tags';
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