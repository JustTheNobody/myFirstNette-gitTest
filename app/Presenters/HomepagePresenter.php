<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;
use App\Models\ArticleModel;

final class HomepagePresenter extends Nette\Application\UI\Presenter
{
    private $articleModel = [];
    private array $lastArticle = [];

    public function __construct(ArticleModel $articleModel)
    {
        //parent::__construct();
        $this->articleModel = $articleModel;
    }

    //get last Article
    public function beforeRender()
    {
        $this->lastArticle = $this->articleModel->getLast();
    }

    public function renderDefault($result, $status)
    {
        $this->template->article = $this->lastArticle;
        // Předání výsledku do šablony
        $this->template->result = $result;
        $this->template->status = $status;
    }
}
