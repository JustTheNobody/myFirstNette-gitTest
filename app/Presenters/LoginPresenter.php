<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Models\LoginManager;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Nette\Application\UI\Presenter;

/**
 * Presenter kalkulačky.
 * @package App\Presenters
 */
final class LoginPresenter extends Presenter
{
    public string $status = '';
    public array $myArray = [];
    /** Form constants that i may dont use :P. */
    const
    FORM_MSG_REQUIRED = 'Tohle pole je povinné.',
    FORM_MSG_RULE = 'Tohle pole má neplatný formát.';

    /** @var LoginManager Instance třídy modelu pro práci s operacemi kalkulačky. */
    private $loginManager;

        
    /** @var int|null výsledek operace nebo null */
    private $result = null;

    /**
     * Konstruktor s injektovaným modelem pro práci s operacemi kalkulačky.
    * @param LoginManager $loginManager automaticky injektovaná třída modelu pro práci s operacemi kalkulačky
    */
    public function __construct(LoginManager $loginManager)
    {
        parent::__construct();
        $this->loginManager = $loginManager;
    }

    /**
     * Vrátí formulář kalkulačky.
     * @return Form formulář kalkulačky
     */
    protected function createComponentLoginForm()
    {
        $form = new Form();
        // Získáme existující operace kalkulačky a dáme je do výběru operací.
        $form->addText('name', 'Name:')
            ->setRequired(self::FORM_MSG_REQUIRED)
            ->setHtmlAttribute('class', 'form-control');
        $form->addPassword('password', 'Password:')
            ->setRequired(self::FORM_MSG_REQUIRED)
            ->setHtmlAttribute('class', 'form-control');
        $form->addSubmit('submit', 'Submit')
            ->setHtmlAttribute('class', 'btn btn-primary');
        $form->onSuccess[] = [$this, 'loginFormSucceeded'];

        return $form;
    }

     /** Výchozí vykreslovací metoda tohoto presenteru. */
    public function renderDefault($result, $status)
    {
        // Předání výsledku do šablony
        $this->template->result = $result;
        $this->template->status = $status;
    }

    /**
     * Funkce se vykonaná při úspěšném odeslání formuláře kalkulačky a zpracuje odeslané hodnoty.
     * @param Form $form        formulář kalkulačky
     * @param ArrayHash $values odeslané hodnoty formuláře
     */
    public function loginFormSucceeded(ArrayHash $values)
    {
        // Necháme si vypočítat výsledek podle zvolené operace a zadaných hodnot.
        $this->result = $this->loginManager->getUser($values->name, $values->password);

        if (is_array($this->result)) {
            $this->flashMessage('you are loged in now.');
            //redirect 2 userPage

            $_SESSION['status'] = "success";
            $_SESSION['user'] = $this->result['user'];
            $_SESSION['user_id'] = $this->result['user_id'];
            $this->status = "success";
            $this->redirect('Homepage:default', $this->result['user'], $this->status);
        } else {
            //redirect
            $this->status = "fail";
            $this->flashMessage('loged data not found in DB.');
            $this->redirect('Login:default', $this->result, $this->status);
        }
    }

    public function actionOut()
    {
        $this->status = "success";
        unset($_SESSION['user_id']);
        unset($_SESSION['user']);
        unset($_SESSION);
        $this->getUser()->logout();
        $this->flashMessage('Odhlášení bylo úspěšné.');
        $this->redirect('Homepage:default', $this->result, $this->status);
    }
}
