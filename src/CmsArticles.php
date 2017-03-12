<?php

namespace TMCms\Modules\Articles;

use TMCms\Admin\Menu;
use TMCms\Admin\Messages;
use TMCms\DB\SQL;
use TMCms\HTML\BreadCrumbs;
use TMCms\HTML\Cms\CmsFormHelper;
use TMCms\HTML\Cms\CmsTable;
use TMCms\HTML\Cms\CmsTableHelper;
use TMCms\HTML\Cms\Column\ColumnActive;
use TMCms\HTML\Cms\Column\ColumnData;
use TMCms\HTML\Cms\Column\ColumnDelete;
use TMCms\HTML\Cms\Column\ColumnEdit;
use TMCms\Log\App;
use TMCms\Modules\Articles\Entity\ArticleCategoryEntity;
use TMCms\Modules\Articles\Entity\ArticleCategoryEntityRepository;
use TMCms\Modules\Articles\Entity\ArticleEntity;
use TMCms\Modules\Articles\Entity\ArticleEntityRepository;
use TMCms\Modules\Articles\Entity\ArticleRelationEntityRepository;
use TMCms\Modules\Articles\Entity\ArticleTagEntity;
use TMCms\Modules\Articles\Entity\ArticleTagEntityRepository;
use TMCms\Modules\Articles\Entity\ArticleTagRelationEntityRepository;
use TMCms\Modules\Settings\ModuleSettings;
use TMCms\Strings\UID;

defined('INC') or exit;

class CmsArticles
{
    /** Articles */

    public function _default()
    {
        $articles = new ArticleEntityRepository();
        $articles->addOrderByField();

        $categories = new ArticleCategoryEntityRepository();
        $categories = $categories->getPairs('title');

        BreadCrumbs::getInstance()
            ->addCrumb(__('Articles'))
            ->addAction(__('Add Article'), '?p=' . P . '&do=add')
        ;

        echo CmsTableHelper::outputTable([
            'data' => $articles,
            'columns' => [
                'image' => [
                    'type' => 'image',
                ],
                'title' => [
                    'translation' => true,
                ],
                'category_id' => [
                    'title' => __('Category'),
                    'pairs' => $categories,
                ],
                'show_on_main' => [
                    'type' => 'active'
                ],
            ],
            'filters' => [
                'category_id' => [
                    'type' => 'select',
                    'title' => 'Category',
                    'auto_submit' => true,
                    'options' => [-1 => __('All')] + $categories,
                ],
            ],
            'active' => true,
            'edit' => true,
            'order' => true,
            'delete' => true,
        ]);
    }

    public function add()
    {
        BreadCrumbs::getInstance()
            ->addCrumb(__('Articles'))
            ->addCrumb(__('Add new Article'))
        ;

        echo $this->__add_edit_form();

        UID::text2uidJS(true, array('title_' . LNG . '_' => 'slug'), 255, 1, 1);
    }

    private function __add_edit_form($data = NULL)
    {
        $article = new ArticleEntity();

        $fields = [
            'slug' => [
                'hint' => 'Do not enter manually. It will fill in automatically when you type Title',
            ],
            'category_id' => [
                'options' => ModuleArticles::getCategoryPairs(),
                'title' => 'Category',
            ],
            'image' => [
                'edit' => 'files',
                'allowed_extensions' => 'png,jpeg,jpg',
                'path' => DIR_PUBLIC_URL . 'articles/'
            ],
            'title' => [
                'translation' => true,
            ],
            'description' => [
                'type' => 'textarea',
                'edit' => 'wysiwyg',
                'translation' => true,
            ],
            'text' => [
                'type' => 'textarea',
                'edit' => 'wysiwyg',
                'translation' => true,
            ],
            'meta_keywords' => [
                'translation' => true,
            ],
            'meta_description' => [
                'translation' => true,
            ],
        ];

        if (ModuleSettings::getCustomSettingValue(P, 'enabled_tags')) {
            $fields['tags'] = [
                'type' => 'multiselect',
                'options' => ModuleArticles::getTagPairs(),
            ];
        }

        if (ModuleSettings::getCustomSettingValue(P, 'enabled_related_articles')) {
            $fields['related_articles'] = [
                'type' => 'multiselect',
                'options' => ModuleArticles::getArticles()->getPairs('title'),
            ];
        }

        return CmsFormHelper::outputForm($article->getDbTableName(), [
            'data' => $data,
            'button' => __('Add'),
            'fields' => $fields,
            'unset' => [
                'show_on_main',
                'order',
                'active',
                'ts_created',
            ],
        ]);
    }

    public function edit()
    {
        $article = new ArticleEntity($_GET['id']);
        BreadCrumbs::getInstance()
            ->addCrumb(__('Articles'))
            ->addCrumb($article->getTitle())
        ;

        $article->setTags(ArticleTagRelationEntityRepository::getInstance()
            ->setWhereArticleId($article->getId())
            ->getPairs('tag_id')
        );

        $article->setRelatedArticles(ArticleRelationEntityRepository::getInstance()
            ->setWhereArticleId($article->getId())
            ->getPairs('to_article_id')
        );

        echo $this->__add_edit_form($article)
            ->setSubmitButton('Update')
        ;

        UID::text2uidJS(true, array('title_' . LNG . '_' => 'slug'), 255, 1, 0);
    }

    public function _add()
    {
        $article = new ArticleEntity();
        $article->loadDataFromArray($_POST);
        $article->save();

        Messages::sendGreenAlert('Article created');
        App::add('Article ' . $article->getTitle() . ' created');

        go('?p=' . P . '&highlight='. $article->getId());
    }

    public function _edit()
    {
        $article = new ArticleEntity($_GET['id']);
        $article->loadDataFromArray($_POST);
        $article->save();

        Messages::sendGreenAlert('Article created');
        App::add('Article ' . $article->getTitle() . ' created');

        go('?p=' . P . '&highlight='. $article->getId());
    }

    public function _show_on_main()
    {
        $article = new ArticleEntity($_GET['id']);
        $article->flipBoolValue('show_on_main');
        $article->save();

        Messages::sendGreenAlert('Article updated');
        App::add('Article ' . $article->getTitle() . ' updated');

        if (IS_AJAX_REQUEST) {
            die('1');
        }

        go('?p=' . P . '&highlight=' . $article->getId());
    }

    public function _active()
    {
        $article = new ArticleEntity($_GET['id']);
        $article->flipBoolValue('active');
        $article->save();

        Messages::sendGreenAlert('Article updated');
        App::add('Article ' . $article->getTitle() . ' updated');

        if (IS_AJAX_REQUEST) {
            die('1');
        }

        go('?p=' . P . '&highlight='. $article->getId());
    }

    public function _order()
    {
        $article = new ArticleEntity($_GET['id']);

        if (IS_AJAX_REQUEST) {
            SQL::orderMoveByStep($article->getId(), $article->getDbTableName(), $_GET['direct'], $_GET['step']);
            die(1);
        }

        SQL::order($article->getId(), $article->getDbTableName(), $_GET['direct']);
        back();
    }

    public function _delete()
    {
        $article = new ArticleEntity($_GET['id']);
        $article->deleteObject();

        Messages::sendGreenAlert('Article deleted');
        App::add('Article ' . $article->getTitle() . ' deleted');

        if (IS_AJAX_REQUEST) {
            die('1');
        }

        go('?p=' . P);
    }

    /** Categories */


    public function categories()
    {
        $categories = new ArticleCategoryEntityRepository();
        $categories->addOrderByField('title');

        BreadCrumbs::getInstance()
            ->addCrumb(__('Articles'))
            ->addCrumb(__('Categories'))
            ->addAction(__('Add Category'), '?p=' . P . '&do=categories_add')
        ;

        echo CmsTable::getInstance()
            ->addData($categories)
            ->addColumn(ColumnData::getInstance('title')
                ->enableTranslationColumn()
            )
            ->addColumn(ColumnEdit::getInstance('edit'))
            ->addColumn(ColumnActive::getInstance('active'))
            ->addColumn(ColumnDelete::getInstance('delete'))
        ;
    }

    public function categories_add()
    {
        BreadCrumbs::getInstance()
            ->addCrumb(__('Articles'))
            ->addCrumb(__('Categories'))
            ->addCrumb(__('Add new Category'));

        echo $this->__categories_add_edit_form();
    }

    private function __categories_add_edit_form($data = NULL)
    {
        $category = new ArticleCategoryEntity();
        return CmsFormHelper::outputForm($category->getDbTableName(), [
            'data' => $data,
            'button' => __('Add'),
            'fields' => [
                'title' => [
                    'translation' => true,
                ],
            ],
            'unset' => [
                'active',
            ],
        ]);
    }

    public function categories_edit()
    {
        $category = new ArticleCategoryEntity($_GET['id']);

        BreadCrumbs::getInstance()
            ->addCrumb(__('Articles'))
            ->addCrumb(__('Categories'))
            ->addCrumb($category->getTitle())
        ;

        echo $this->__categories_add_edit_form($category)
            ->setSubmitButton('Update')
        ;
    }

    public function _categories_add()
    {
        $category = new ArticleCategoryEntity();
        $category->loadDataFromArray($_POST);
        $category->save();

        Messages::sendGreenAlert('Category created');
        App::add('Category ' . $category->getTitle() . ' created');

        go('?p=' . P . '&do=categories&highlight='. $category->getId());
    }

    public function _categories_edit()
    {
        $category = new ArticleCategoryEntity($_GET['id']);
        $category->loadDataFromArray($_POST);
        $category->save();

        Messages::sendGreenAlert('Category updated');
        App::add('Category ' . $category->getTitle() . ' updated');

        go('?p=' . P . '&do=categories&highlight=' . $category->getId());
    }

    public function _categories_active()
    {
        $category = new ArticleCategoryEntity($_GET['id']);
        $category->flipBoolValue('active');
        $category->save();

        Messages::sendGreenAlert('Category updated');
        App::add('Category ' . $category->getTitle() . ' updated');

        if (IS_AJAX_REQUEST) {
            die('1');
        }

        go('?p=' . P . '&do=tags&highlight='. $category->getId());
    }

    public function _categories_delete()
    {
        $category = new ArticleCategoryEntity($_GET['id']);
        $category->deleteObject();

        Messages::sendGreenAlert('Category deleted');
        App::add('Category ' . $category->getTitle() . ' deleted');

        if (IS_AJAX_REQUEST) {
            die('1');
        }

        go('?p=' . P . '&do=categories');
    }

    /** Tags */

    public function tags() {
        $tags = new ArticleTagEntityRepository();

        BreadCrumbs::getInstance()
            ->addCrumb(__('Articles'))
            ->addCrumb(__('Tags'))
            ->addAction(__('Add Tag'), '?p=' . P . '&do=tags_add')
        ;

        echo CmsTable::getInstance()
            ->addData($tags)
            ->addColumn(ColumnData::getInstance('title')
                ->enableTranslationColumn()
            )
            ->addColumn(ColumnEdit::getInstance('edit'))
            ->addColumn(ColumnActive::getInstance('active'))
            ->addColumn(ColumnDelete::getInstance('delete'))
        ;
    }

    public function tags_add()
    {
        BreadCrumbs::getInstance()
            ->addCrumb(__('Articles'))
            ->addCrumb(__('Tags'))
            ->addCrumb(__('Add new Tag'))
        ;

        echo $this->__tags_add_edit_form();
    }

    private function __tags_add_edit_form($data = NULL)
    {
        $tag = new ArticleTagEntity();
        return CmsFormHelper::outputForm($tag->getDbTableName(), [
            'data' => $data,
            'button' => __('Add'),
            'fields' => [
                'title' => [
                    'translation' => true,
                ],
            ],
            'unset' => [
                'active',
            ],
        ]);
    }

    public function tags_edit()
    {
        $tag = new ArticleTagEntity($_GET['id']);

        BreadCrumbs::getInstance()
            ->addCrumb(__('Articles'))
            ->addCrumb(__('Tags'))
            ->addCrumb($tag->getTitle())
        ;

        echo $this->__tags_add_edit_form($tag)
            ->setSubmitButton('Update')
        ;
    }

    public function _tags_add()
    {
        $tag = new ArticleTagEntity();
        $tag->loadDataFromArray($_POST);
        $tag->save();

        Messages::sendGreenAlert('Tag created');
        App::add('Tag ' . $tag->getTitle() . ' created');

        go('?p=' . P . '&do=tags&highlight='. $tag->getId());
    }

    public function _tags_edit()
    {
        $tag = new ArticleTagEntity($_GET['id']);
        $tag->loadDataFromArray($_POST);
        $tag->save();

        Messages::sendGreenAlert('Tag updated');
        App::add('Tag ' . $tag->getTitle() . ' updated');

        go('?p=' . P . '&do=tags&highlight='. $tag->getId());
    }

    public function _tags_active()
    {
        $tag = new ArticleTagEntity($_GET['id']);
        $tag->flipBoolValue('active');
        $tag->save();

        Messages::sendGreenAlert('Tag updated');
        App::add('Tag ' . $tag->getTitle() . ' updated');

        if (IS_AJAX_REQUEST) {
            die('1');
        }

        go('?p=' . P . '&do=tags&highlight='. $tag->getId());
    }

    public function _tags_delete()
    {
        $tag = new ArticleTagEntity($_GET['id']);
        $tag->deleteObject();

        Messages::sendGreenAlert('Tag deleted');
        App::add('Tag ' . $tag->getTitle() . ' deleted');

        if (IS_AJAX_REQUEST) {
            die('1');
        }

        go('?p=' . P . '&do=tags');
    }

    public function settings()
    {
        echo ModuleSettings::requireTableForExternalModule(P, [
            'enabled_tags' => [
                'type' => 'checkbox',
            ],
            'enabled_related_articles' => [
                'type' => 'checkbox',
            ],
        ]);
    }

    public function _settings()
    {
        ModuleSettings::requireUpdateModuleSettings(P, [
            'enabled_tags' => [
                'type' => 'checkbox',
                'value' => 1,
            ],
            'enabled_related_articles' => [
                'type' => 'checkbox',
                'value' => 1,
            ],
        ]);

        Messages::sendGreenAlert('Settings updates');
        App::add('Settings updates');

        back();
    }
}