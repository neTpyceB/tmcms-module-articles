<?php

namespace TMCms\Modules\Articles\Entity;

use TMCms\Orm\EntityRepository;

/**
 * Class ArticleCategoryEntityRepository
 * @package TMCms\Modules\Articles\Entity
 *
 * @method $this setWhereActive(bool $flag)
 */
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