<?php

namespace TMCms\Modules\Articles\Entity;

use TMCms\Config\Settings;
use TMCms\DB\SQL;
use TMCms\Orm\Entity;
use TMCms\Routing\Structure;

/**
 * Class ArticleEntity
 * @package TMCms\Modules\Articles\Entity
 *
 * @method string getImage()
 * @method int getOrder()
 * @method string getSlug()
 * @method string getText()
 * @method string getTitle()
 * @method int getTsCreated()
 *
 * @method $this setOrder(int $order)
 * @method $this setTags(array $tags)
 * @method $this setTsCreated(int $ts)
 */
class ArticleEntity extends Entity
{
    protected $translation_fields = ['title', 'description', 'text', 'meta_keywords', 'meta_description'];

    public function getDateCreated()
    {
        return date(Settings::getDefaultDateFormat(), $this->getTsCreated());
    }

    public function getSlugUrl()
    {
        return Structure::getPathByLabel('articles') . $this->getSlug();
    }

    protected function afterSave()
    {
        // If tags are not changed - do not make changes in DB
        if (!$this->isFieldChangedForUpdate('tags') && !$this->isFieldChangedForUpdate('tags_ordered')) {
            return $this;
        }

        // Remove existing
        ArticleTagRelationEntityRepository::getInstance()
            ->setWhereArticleId($this->getId())
            ->deleteObjectCollection();

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
        // Created time
        $this->setTsCreated(NOW);

        // Next order index
        if (!$this->getOrder()) {
            $this->setOrder(SQL::getNextOrder($this->getDbTableName()));
        }

        return $this;
    }

    protected function beforeDelete()
    {
        // Remove existing relations to tags
        ArticleTagRelationEntityRepository::getInstance()
            ->setWhereArticleId($this->getId())
            ->deleteObjectCollection();

        return $this;
    }
}