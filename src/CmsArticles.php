<?php

namespace TMCms\Modules\Articles;

use TMCms\Admin\Menu;
use TMCms\Admin\Messages;
use TMCms\HTML\BreadCrumbs;
use TMCms\HTML\Cms\CmsFormHelper;
use TMCms\HTML\Cms\CmsTable;
use TMCms\HTML\Cms\Column\ColumnActive;
use TMCms\HTML\Cms\Column\ColumnData;
use TMCms\HTML\Cms\Column\ColumnDelete;
use TMCms\HTML\Cms\Column\ColumnEdit;
use TMCms\HTML\Cms\Columns;
use TMCms\Log\App;
use TMCms\Modules\Articles\Entity\ArticleCategoryEntity;
use TMCms\Modules\Articles\Entity\ArticleCategoryEntityRepository;
use TMCms\Modules\Articles\Entity\ArticleEntity;
use TMCms\Modules\Articles\Entity\ArticleEntityRepository;
use TMCms\Modules\Articles\Entity\ArticleTagEntity;
use TMCms\Modules\Articles\Entity\ArticleTagEntityRepository;
use TMCms\Modules\Articles\Entity\ArticleTagRelationEntityRepository;

defined('INC') or exit;

Menu::getInstance()
    ->addSubMenuItem('categories')
    ->addSubMenuItem('tags')
;

class CmsArticles
{
    /** Articles */

    public function _default()
    {
        $news = new ArticleEntityRepository();
        $category = new ArticleCategoryEntityRepository();

        echo BreadCrumbs::getInstance()
            ->addCrumb(__('Articles'))
        ;

        echo Columns::getInstance()
            ->add('<a class="btn btn-success" href="?p=' . P . '&do=add">'. __('Add Article') . '</a><br><br>', ['align' => 'right'])
        ;

        echo CmsTable::getInstance()
            ->addData($news)
            ->addColumn(ColumnData::getInstance('title')
                ->enableTranslationColumn()
            )
            ->addColumn(ColumnData::getInstance('category_id')
                ->setPairedDataOptionsForKeys($category->getPairs('title'))
            )
            ->addColumn(ColumnEdit::getInstance('edit'))
            ->addColumn(ColumnActive::getInstance('active'))
            ->addColumn(ColumnDelete::getInstance('delete'))
        ;
    }

    private function __add_edit_form($data = NULL)
    {
        $article = new ArticleEntity();
        return CmsFormHelper::outputForm($article->getDbTableName(), [
            'combine' => true,
            'data' => $data,
            'button' => __('Add'),
            'fields' => [
                'category_id' => [
                    'options' => ModuleArticles::getCategoryPairs(),
                    'title' => 'Category',
                ],
                'tags' => [
                    'type' => 'multiselect',
                    'options' => ModuleArticles::getTagPairs(),
                ],
                'title' => [
                    'translation' => true,
                ],
                'description' => [
                    'translation' => true,
                ],
                'text' => [
                    'type' => 'textarea',
                    'translation' => true,
                ],
            ],
            'unset' => [
                'active',
                'ts_created',
            ],
        ]);
    }

    public function add()
    {
        echo BreadCrumbs::getInstance()
            ->addCrumb(__('Articles'))
            ->addCrumb(__('Add new Article'))
        ;

        echo $this->__add_edit_form();
    }

    public function edit()
    {
        $article = new ArticleEntity($_GET['id']);
        echo BreadCrumbs::getInstance()
            ->addCrumb(__('Articles'))
            ->addCrumb($article->getTitle())
        ;

        $article->setTags(ArticleTagRelationEntityRepository::getInstance()
            ->setWhereArticleId($article->getId())
            ->getPairs('tag_id')
        );

        echo $this->__add_edit_form($article)
            ->setSubmitButton('Update')
        ;
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

        echo BreadCrumbs::getInstance()
            ->addCrumb(__('Articles'))
            ->addCrumb(__('Categories'))
        ;

        echo Columns::getInstance()
            ->add('<a class="btn btn-success" href="?p=' . P . '&do=categories_add">'. __('Add Category') . '</a><br><br>', ['align' => 'right'])
        ;

        echo CmsTable::getInstance()
            ->addData($categories)
            ->addColumn(ColumnData::getInstance('title'))
            ->addColumn(ColumnEdit::getInstance('edit'))
            ->addColumn(ColumnActive::getInstance('active'))
            ->addColumn(ColumnDelete::getInstance('delete'))
        ;
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

    public function categories_add()
    {
        echo BreadCrumbs::getInstance()
            ->addCrumb(__('Articles'))
            ->addCrumb(__('Categories'))
            ->addCrumb(__('Add new Category'))
        ;

        echo $this->__categories_add_edit_form();
    }

    public function categories_edit()
    {
        $category = new ArticleCategoryEntity($_GET['id']);

        echo BreadCrumbs::getInstance()
            ->addCrumb(__('Articles'))
            ->addCrumb(__('Categories'))
            ->addCrumb($category->getTitle())
        ;

        echo $this->__tags_add_edit_form($category)
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

        go('?p=' . P . '&do=tags&highlight='. $category->getId());
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

        echo BreadCrumbs::getInstance()
            ->addCrumb(__('Articles'))
            ->addCrumb(__('Tags'))
        ;

        echo Columns::getInstance()
            ->add('<a class="btn btn-success" href="?p=' . P . '&do=tags_add">'. __('Add Tag') . '</a><br><br>', ['align' => 'right'])
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

    public function tags_add()
    {
        echo BreadCrumbs::getInstance()
            ->addCrumb(__('Articles'))
            ->addCrumb(__('Tags'))
            ->addCrumb(__('Add new Tag'))
        ;

        echo $this->__tags_add_edit_form();
    }

    public function tags_edit()
    {
        $tag = new ArticleTagEntity($_GET['id']);

        echo BreadCrumbs::getInstance()
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
}