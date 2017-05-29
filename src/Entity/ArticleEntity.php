<?php

namespace TMCms\Modules\Articles\Entity;

use TMCms\Config\Settings;
use TMCms\DB\SQL;
use TMCms\Modules\Settings\ModuleSettings;
use TMCms\Orm\Entity;
use TMCms\Routing\Structure;

/**
 * Class ArticleEntity
 * @package TMCms\Modules\Articles\Entity
 *
 * @method int getCategoryId()
 * @method string getDescription()
 * @method string getImage()
 * @method int getOrder()
 * @method string getSlug()
 * @method string getText()
 * @method string getTitle()
 * @method int getTsCreated()
 *
 * @method $this setCategoryId(int $category_id)
 * @method $this setOrder(int $order)
 * @method $this setTsCreated(int $ts)
 *
 * @method array getRelatedArticles()
 * @method array getTags()
 * @method $this setRelatedArticles(array $article_ids)
 * @method $this setTags(array $tag_ids)
 */
class ArticleEntity extends Entity
{
    protected $translation_fields = ['title', 'description', 'text', 'meta_keywords', 'meta_description'];

    public function getDateCreated()
    {
        return date(Settings::getDefaultDateFormat(), $this->getTsCreated());
    }

    public function getSlugUrl($lng = LNG)
    {
        return Structure::getPathByLabel('articles') . $this->getSlug() . '/';
    }

    protected function afterSave()
    {
        //=== TAGS
        if (ModuleSettings::getCustomSettingValue('articles', 'enabled_tags')) {
            // If tags are not changed - do not make changes in DB
            if ($this->isFieldChangedForUpdate('tags') || $this->isFieldChangedForUpdate('tags_ordered')) {
                // Remove existing
                ArticleTagRelationEntityRepository::getInstance()
                    ->setWhereArticleId($this->getId())
                    ->deleteObjectCollection();

                // Create new relations
                if ($this->getTags()) {
                    $tag_clone = new ArticleTagRelationEntity();

                    foreach ($this->getTags() as $tag_id) {
                        $tag = clone $tag_clone;
                        $tag->setArticleId($this->getId());
                        $tag->setTagId($tag_id);
                        $tag->save();
                    }
                }
            }
        }

        //=== RELATED ARTICLES
        if (ModuleSettings::getCustomSettingValue('articles', 'enabled_related_articles')) {
            // If tags are not changed - do not make changes in DB
            if ($this->isFieldChangedForUpdate('related_articles') || $this->isFieldChangedForUpdate('related_articles_ordered')) {
                // Remove existing
                ArticleRelationEntityRepository::getInstance()
                    ->setWhereArticleId($this->getId())
                    ->deleteObjectCollection();

                // And bacwards
                ArticleRelationEntityRepository::getInstance()
                    ->setWhereToArticleId($this->getId())
                    ->deleteObjectCollection();

                // Create new relations - save both directions
                if ($this->getRelatedArticles()) {
                    $relation_clone = new ArticleRelationEntity();

                    foreach ($this->getRelatedArticles() as $to_article_id) {
                        $relation = clone $relation_clone;
                        $relation->setArticleId($this->getId());
                        $relation->setToArticleId($to_article_id);
                        $relation->save();

                        // Back way link
                        $relation = clone $relation_clone;
                        $relation->setArticleId($to_article_id);
                        $relation->setToArticleId($this->getId());
                        $relation->save();
                    }
                }
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

        // Remove existing relations to other articles
        ArticleRelationEntityRepository::getInstance()
            ->setWhereArticleId($this->getId())
            ->deleteObjectCollection();

        // And bacwards relations
        ArticleRelationEntityRepository::getInstance()
            ->setWhereToArticleId($this->getId())
            ->deleteObjectCollection();

        return $this;
    }
}