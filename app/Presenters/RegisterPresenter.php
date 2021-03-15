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
final class RegisterPresenter extends Presenter
{
    /** Definice konstant pro zprávy formuláře. */
    const
    FORM_MSG_REQUIRED = 'Tohle pole je povinné.',
    FORM_MSG_RULE = 'Tohle pole má neplatný formát.';

    /** @var LoginManager Instance třídy modelu pro práci s operacemi kalkulačky. */
    private $registerManager;

    /**
     * Konstruktor s injektovaným modelem pro práci s operacemi kalkulačky.
     * @param LoginManager $RegisterManager automaticky injektovaná třída modelu pro práci s operacemi kalkulačky
     */
    public function __construct(LoginManager $registerManager)
    {
        parent::__construct();
        $this->registerManager = $registerManager;
    }
    /** @var int|null výsledek operace nebo null */
    private $result = null;
    private $status = "";

     /** Výchozí vykreslovací metoda tohoto presenteru. */
    public function renderDefault($result, $status)
    {
        // Předání výsledku do šablony
        $this->template->result = $result;
        $this->template->status = $status;
    }

    /**
     * Vrátí formulář kalkulačky.
     * @return Form formulář kalkulačky
     */
    protected function createComponentRegisterForm()
    {
        $form = new Form();
        // Získáme existující operace kalkulačky a dáme je do výběru operací.
        $form->addText('f_name', 'First Name:')
            ->setRequired(self::FORM_MSG_REQUIRED)
            ->setHtmlAttribute('class', 'form-control');
        $form->addText('l_name', 'Last Name:')
            ->setRequired(self::FORM_MSG_REQUIRED)
            ->setHtmlAttribute('class', 'form-control');
        $form->addText('email', 'Email:')
            ->setRequired(self::FORM_MSG_REQUIRED)
            ->setHtmlAttribute('class', 'form-control');
        $form->addPassword('password', 'Password:')
            ->setRequired(self::FORM_MSG_REQUIRED)
            ->setHtmlAttribute('class', 'form-control');
        $form->addPassword('repassword', 'Confirm Password:')
            ->setRequired(self::FORM_MSG_REQUIRED)//form-control
            ->setHtmlAttribute('class', 'form-control');
        $form->addSubmit('submit', 'Submit')
            ->setHtmlAttribute('class', 'btn btn-primary');
        $form->onSuccess[] = [$this, 'RegisterFormSucceeded'];

        return $form;
    }

    /**
     * Funkce se vykonaná při úspěšném odeslání formuláře kalkulačky a zpracuje odeslané hodnoty.
     * @param Form $form        formulář kalkulačky
     * @param ArrayHash $values odeslané hodnoty formuláře
     */
    public function registerFormSucceeded(ArrayHash $values)
    {
        //sanatise & trim
        foreach ($values as $key => $value) {
            $$key = trim($value);
        }

        // Necháme si vypočítat výsledek podle zvolené operace a zadaných hodnot.
        $this->result = $this->registerManager->registerUser($f_name, $l_name, $email, $password, $repassword);

        if ($this->result == "email") {
            $this->status = "fail";
            $this->flashMessage('This email address is already registered.');
            //redirect -> send the data back to form?
            $this->redirect('Register:default', $this->result, $this->status);
        } elseif ($this->result == "repassword") {
            $this->status = "fail";
            $this->flashMessage('Confirm password has to match password.');
            //redirect -> send the data back to form?
            $this->redirect('Register:default', $this->result, $this->status);
        } elseif ($this->result == "registered") {
            $this->status = "success";
            $this->result = "";
            $this->flashMessage('You are registered. You can login now.');
                //redirect -> send the data back to form?
                $this->redirect('Login:default', $this->result, $this->status);
        } else {
            $this->status = "fail";
            $this->flashMessage('Some error during registration.');
            $this->redirect('Register:default', $this->result, $this->status);
        }
    }
}
