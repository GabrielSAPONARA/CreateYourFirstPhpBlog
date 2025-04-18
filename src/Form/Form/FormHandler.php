<?php

namespace App\Form\Form;

class FormHandler
{
    private Form $form;

    public function __construct(Form $form)
    {
        $this->form = $form;
    }

    public function handlePostRequest(array $requestData) : bool
    {
        if(filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS) !== null)
        {
            return false;
        }

        $this->form->handleRequest($requestData);
        return $this->form->isValid();
    }

    public function getData(): array
    {
        return $this->form->getData();
    }

    public function getErrors(): array
    {
        return $this->form->getErrors();
    }
}