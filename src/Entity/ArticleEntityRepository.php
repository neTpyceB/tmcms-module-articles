<?php

namespace TMCms\Modules\Articles\Entity;

use TMCms\Orm\EntityRepository;

/**
 * Class ArticleEntityRepository
 * @package TMCms\Modules\Articles\Entity
 *
 * @method $this setWhereActive(int $flag)
 * @method $this setWhereCategoryId(int $category_id)
 * @method $this setWhereShowOnMain(int $flag)
 */
class ArticleEntityRepository extends EntityRepository
{
    protected $translation_fields = ['title', 'description', 'text', 'meta_keywords', 'meta_description'];
    protected $table_structure = [
        'fields' => [
            'slug' => [
                'type' => 'varchar'
            ],
            'category_id' => [
                'type' => 'index',
            ],
            'show_on_main' => [
                'type' => 'index',
            ],
            'title' => [
                'type' => 'translation',
            ],
            'image' => [
                'type' => 'varchar'
            ],
            'description' => [
                'type' => 'translation',
            ],
            'text' => [
                'type' => 'translation',
            ],
            'meta_keywords' => [
                'type' => 'translation',
            ],
            'meta_description' => [
                'type' => 'translation',
            ],
            'ts_created' => [
                'type' => 'int',
                'unsigned' => true,
            ],
            'active' => [
                'type' => 'bool',
            ],
            'order' => [
                'type' => 'ts',
            ],
        ],
    ];
}