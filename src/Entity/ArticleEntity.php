<?php

namespace TMCms\Modules\Articles\Entity;

use TMCms\Orm\Entity;

/**
 * Class ArticleEntity
 * @package TMCms\Modules\Articles\Entity
 *
 * @method string getTitle()
 * @method $this setTags(array $tags)
 */
class ArticleEntity extends Entity
{
    protected $translation_fields = ['title', 'description', 'text', 'meta_keywords', 'meta_description'];

    protected function afterSave()
    {
        // If tags are not changed - do not make changes in DB
        if (!$this->isFieldChangedForUpdate('tags') && !$this->isFieldChangedForUpdate('tags_ordered')) {
            return $this;
        }

        // Remove existing
        ArticleTagRelationEntityRepository::getInstance()
            ->setWhereArticleId($this->getId())
            ->deleteObjectCollection()
        ;

        // Create new relations
        if (!empty($_POST['tags'])) {
            $tag_clone = new ArticleTagRelationEntity();

            foreach ($_POST['tags'] as $tag_id) {
                $tag = clone $tag_clone;
                $tag->setArticleId($this->getId());
                $tag->setTagId($tag_id);
                $tag->save();
            }
        }

        return $this;
    }

	protected function beforeCreate()
	{
		$this->setTsCreated(NOW);
	}

    protected function beforeDelete()
    {
        // Remove existing relations to tags
        ArticleTagRelationEntityRepository::getInstance()
            ->setWhereArticleId($this->getId())
            ->deleteObjectCollection()
        ;

        return $this;
    }
}