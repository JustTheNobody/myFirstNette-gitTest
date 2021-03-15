<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Models\CalculatorManager;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;
use Nette\Application\UI\Presenter;

/**
 * Presenter kalkulačky.
 * @package App\Presenters
 */
final class CalculatorPresenter extends Presenter
{
    /** Definice konstant pro zprávy formuláře. */
    const
    FORM_MSG_REQUIRED = 'Tohle pole je povinné.',
    FORM_MSG_RULE = 'Tohle pole má neplatný formát.';

    /**
     * Vrátí formulář kalkulačky.
     * @return Form formulář kalkulačky
     */
    protected function createComponentCalculatorForm()
    {
        $form = new Form();
        // Získáme existující operace kalkulačky a dáme je do výběru operací.
        $form->addRadioList('operation', 'Operace:', $this->calculatorManager->getOperations())
            ->setDefaultValue(CalculatorManager::ADD)
            ->setRequired(self::FORM_MSG_REQUIRED);
        $form->addText('x', 'První číslo:')
            ->setHtmlType('number')
            ->setDefaultValue(0)
            ->setRequired(self::FORM_MSG_REQUIRED)
            ->addRule(Form::INTEGER, self::FORM_MSG_RULE);
        $form->addText('y', 'Druhé číslo:')
            ->setHtmlType('number')
            ->setDefaultValue(0)
            ->setRequired(self::FORM_MSG_REQUIRED)
            ->addRule(Form::INTEGER, self::FORM_MSG_RULE)
            // Ošetříme dělení nulou.
            ->addConditionOn($form['operation'], Form::EQUAL, CalculatorManager::DIVIDE)
            ->addRule(Form::PATTERN, 'Nelze dělit nulou.', '^[^0].*');
        $form->addSubmit('calculate', 'Spočítej výsledek');
        $form->onSuccess[] = [$this, 'calculatorFormSucceeded'];

        return $form;
    }

    /** @var CalculatorManager Instance třídy modelu pro práci s operacemi kalkulačky. */
    private $calculatorManager;

    /**
     * Konstruktor s injektovaným modelem pro práci s operacemi kalkulačky.
     * @param CalculatorManager $calculatorManager automaticky injektovaná třída modelu pro práci s operacemi kalkulačky
     */
    public function __construct(CalculatorManager $calculatorManager)
    {
        parent::__construct();
        $this->calculatorManager = $calculatorManager;
    }
    /** @var int|null výsledek operace nebo null */
    private $result = null;

     /** Výchozí vykreslovací metoda tohoto presenteru. */
    public function renderDefault()
    {
        // Předání výsledku do šablony
        $this->template->result = $this->result;
    }

    /**
     * Funkce se vykonaná při úspěšném odeslání formuláře kalkulačky a zpracuje odeslané hodnoty.
     * @param Form $form        formulář kalkulačky
     * @param ArrayHash $values odeslané hodnoty formuláře
     */
    public function calculatorFormSucceeded(Form $form, ArrayHash $values)
    {
        // Necháme si vypočítat výsledek podle zvolené operace a zadaných hodnot.
        $this->result = $this->calculatorManager->calculate($values->operation, $values->x, $values->y);
    }
}
