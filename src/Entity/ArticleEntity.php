<?php

namespace TMCms\Modules\Articles\Entity;

use TMCms\Orm\Entity;

class ArticleEntity extends Entity
{
    protected $translation_fields = ['title', 'description', 'text'];
}