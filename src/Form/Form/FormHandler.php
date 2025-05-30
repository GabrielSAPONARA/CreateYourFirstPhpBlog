<?php

namespace App\Form\Form;

class FormHandler
{
    private Form $form;

    /**
     * @param Form $form
     */
    public function __construct(Form $form)
    {
        $this->form = $form;
    }

    /**
     * @param array $requestData
     * @return bool
     */
    public function handlePostRequest(array $requestData) : bool
    {
        if(filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS) !== null)
        {
            return false;
        }

        $this->form->handleRequest($requestData);
        return $this->form->isValid();
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->form->getData();
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->form->getErrors();
    }
}