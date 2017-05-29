<?php

namespace TMCms\Modules\Articles;

use TMCms\Modules\Articles\Entity\ArticleCategoryEntityRepository;
use TMCms\Modules\Articles\Entity\ArticleEntity;
use TMCms\Modules\Articles\Entity\ArticleEntityRepository;
use TMCms\Modules\Articles\Entity\ArticleRelationEntityRepository;
use TMCms\Modules\Articles\Entity\ArticleTagEntityRepository;
use TMCms\Modules\Articles\Entity\ArticleTagRelationEntityRepository;
use TMCms\Modules\IModule;
use TMCms\Traits\singletonInstanceTrait;

defined('INC') or exit;

class ModuleArticles implements IModule
{
    use singletonInstanceTrait;

    public static function getCategoryPairs($only_active = false) {
        $categories = new ArticleCategoryEntityRepository();
        if ($only_active) {
            $categories->setWhereActive(1);
        }
        $categories->addOrderByField('title');
        return $categories->getPairs('title');
    }

    public static function getTagPairs($only_active = false) {
        $tags = new ArticleTagEntityRepository();
        if ($only_active) {
            $tags->setWhereActive(1);
        }
        $tags->addOrderByField('title');
        return $tags->getPairs('title');
    }

    /**
     * @param array $params ['active' => true, 'limit' => '3', 'order_by' => 'ts_created', 'order_desc' => true, 'show_on_main' => true, 'tag_id' => 10, 'title_like' => 'tit']
     *
     * @return ArticleEntityRepository
     */
    public static function getArticles(array $params = [])
    {
        $articles = new ArticleEntityRepository();

        if (isset($params['active'])) {
            $articles->setWhereActive(1);
        }

        if (isset($params['show_on_main'])) {
            $articles->setWhereShowOnMain(1);
        }

        if (isset($params['category_id']) && $params['category_id']) {
            $articles->setWhereCategoryId($params['category_id']);
        }

        if (isset($params['limit'])) {
            $articles->setLimit(abs((int)$params['limit']));
        }

        if (isset($params['tag_id'])) {
            $tag_relations = new ArticleTagRelationEntityRepository();
            $tag_relations->setWhereTagId($params['tag_id']);
            $articles->mergeWithCollection($tag_relations, 'id', 'article_id');
        }

        if (isset($params['title_like'])) {
            $articles->addWhereFieldIsLike('title', $params['title_like']);
        }

        if (isset($params['order_by']) && trim($params['order_by']) != '') {
            $articles->addOrderByField($params['order_by'], (int)isset($params['order_desc']));
        } else {
            $articles->addOrderByField(); // Simple order
        }

        return $articles;
    }

    /**
     * @param string $slug
     * @return ArticleEntity
     */
    public static function getArticleBySlug($slug)
    {
        /** @var ArticleEntity $article */
        $article = ArticleEntityRepository::findOneEntityByCriteria([
            'slug' => $slug,
        ]);

        return $article;
    }

    /**
     * @param array $params ['active' => 'true', 'article_id' => '3']
     * @return ArticleEntityRepository
     */
    public static function getRelaredArticles($params = [])
    {
        if (!isset($params['article_id'])) {
            dump('Param "article_id" required');
        }

        $relations = ArticleRelationEntityRepository::getInstance()
            ->setWhereArticleId($params['article_id']);

        $ids = $relations->getPairs('to_article_id');
        if (!$ids) {
            $ids = [0]; // To prevent selecting all
        }

        $articles = new ArticleEntityRepository($ids);

        if (isset($params['active'])) {
            $articles->setWhereActive(1);
        }

        $articles->addOrderByField();

        return $articles;
    }
}