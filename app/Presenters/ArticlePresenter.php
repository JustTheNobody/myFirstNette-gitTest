<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette\Application\UI\Presenter;
use Nette\Application\BadRequestException;
use app\Models\ArticleModel;
use Nette\Application\UI\Form;
use Nette\Forms\Controls\HiddenField;
use Nette\Utils\ArrayHash;

/**
 * Article Presenter
 * @package App\Presenters
 */
final class ArticlePresenter extends Presenter
{

    const
    FORM_MSG_REQUIRED = 'Tohle pole je povinné.';

    public string $status = '';
    //Home page article => last added -> by id?
    private $defaultArticleId;
    public $result = '';

    /** @var ArticleModel Article Model. */
    private $articleModel;

    //for edit/delete
    public string $article = '';
    public int $article_id = 0;
    public string $title = '';
    public string $description = '';
    public string $content = '';
    public int $user_id = 0;

    public array $articles = [];

     /**
     * Constract with default Article id
     * @param string         $defaultArticleId Article ID
     * @param ArticleManager $articleModel   Article Model
     */
    public function __construct(string $defaultArticleId = null, ArticleModel $articleModel)
    {
        parent::__construct();
        $this->defaultArticleId = $defaultArticleId;
        $this->articleModel = $articleModel;
    }

    public function checkAuth()
    {
        //check if user loged
        if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
            $this->status = "fail";
            $this->flashMessage('Sorry, it look like you are not loged in.');
            $this->redirect('Login:default', $this->result, $this->status);
        }
    }

    /**
     * Read the Default Article template.
     * @param string|null $id Article id
     * @throws BadRequestException if not found
     */
    public function renderDefault()
    {
        $articles = $this->articleModel->getLast();
                    
        // Read the Articles -> 404 if not found.
        if (!$articles) {
            $_SESSION['status'] = "fail";
            $this->status = "fail";
            $this->flashMessage('There are not any articles in here yet.');
            return $this->template->articles;
            $this->template->status = "fail";
        }

        $this->template->articles = $articles; // Send to template.
        $this->template->status = $this->status;
    }

    public function renderAdd()
    {
        //check if loged in -? if not redirect
        $this->checkAuth();
        $this->articleModel->getArticles();
    }

    protected function createComponentArticleForm()
    {
        $form = new Form();
        // Získáme existující operace kalkulačky a dáme je do výběru operací.
        $form->addText('title', 'Title:')
            ->setRequired(self::FORM_MSG_REQUIRED);
        $form->addTextArea('content', 'Content:')
            ->setHtmlAttribute('rows', 10)
            ->setHtmlAttribute('cols', 40)
            ->setRequired(self::FORM_MSG_REQUIRED);
        $form->addText('description', 'Description:')
            ->setRequired(self::FORM_MSG_REQUIRED);
        $form->addSubmit('submit', 'Submit')
            ->setHtmlAttribute('class', 'btn btn-primary');
        $form->onSuccess[] = [$this, 'articleFormSucceeded'];

        return $form;
    }

    public function articleFormSucceeded(ArrayHash $values)
    {
        $result = $this->articleModel->saveArticle($values);

        if ($result == "success") {
            //redirect 2 userPage
            $this->status = "success";
            $this->flashMessage('Article has been saved.');
            $this->redirect('Article:default');
        } else {
            //redirect
            $this->status = "fail";
            $this->flashMessage('Sorry, there was a unexpected error in saving the Article.');
            $this->redirect('Article:add');
        }
    }
//TODO
    public function renderDelete(int $article_id, int $user_id)
    {
        $this->article_id = $article_id;
        $this->user_id = $user_id;

        $articles = $this->articleModel->getArticles();
                    
        if (!$articles) {
            $_SESSION['status'] = "fail";
            $this->status = "fail";
            $this->flashMessage('There are not any articles in here yet.');
            return $this->template->articles;
        }

        $this->template->articles = $articles; // Send to template.
        $this->template->article_id = $this->article_id;
        $this->template->user_id = $this->user_id;
    }

    protected function createComponentDeleteForm()
    {
        $form = new Form();
        // Získáme existující operace kalkulačky a dáme je do výběru operací.
        $form->addHidden('user_id', $this->user_id);
        $form->addHidden('article_id', $this->article_id);
        $form->addSubmit('submit', 'Confirm to Delete this Article ');
        $form->setHtmlAttribute('class', 'deleteForm');
        $form->onSuccess[] = [$this, 'deleteFormSucceeded'];

        return $form;
    }

    public function deleteFormSucceeded(ArrayHash $values)
    {
        
        $result = $this->articleModel->removeArticle($values);

        if ($result == "success") {
            //redirect 2 userPage
            $this->status = "success";
            $this->flashMessage('Article has been deleted.');
        } else {
            //redirect
            $this->status = "fail";
            $this->flashMessage('Sorry, there was a unexpected error in deleting the Article.');
        }
        $this->redirect('Article:default');
    }

    public function renderEdit(array $article)
    {
        $this->articles = $article;
        $this->template->article = $this->article; // Send to template.
    }

    protected function createComponentEditForm()
    {
        $form = new Form();
        
        $form->addHidden('user_id', $this->articles['user_id'] ?? '');
        $form->addHidden('article_id', $this->articles['article_id'] ?? '');
 
        $form->addText('title', 'Title')
            ->setValue($this->articles['title'] ?? '');
        $form->addTextArea('content', 'Content')
            ->setValue($this->articles['content'] ?? '')
            ->setHtmlAttribute('rows', 10)
            ->setHtmlAttribute('cols', 40);
        $form->addText('description', 'Description')
            ->setValue($this->articles['description'] ?? '');

        $form->addSubmit('submit', 'Update Article ');
        $form->setHtmlAttribute('class', 'updateForm');
        
        $form->onSuccess[] = [$this, 'editFormSucceeded'];

        return $form;
    }

    public function editFormSucceeded(ArrayHash $article)
    {
   
        $result = $this->articleModel->updateArticle($article);

        if ($result == "success") {
            //redirect 2 userPage
            $this->status = "success";
            $this->flashMessage('Article has been updated.');
        } else {
            //redirect
            $this->status = "fail";
            $this->flashMessage('Sorry, there was a unexpected error in updating of the Article.');
        }
        $this->redirect('Article:default');
    }
}
