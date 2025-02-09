<?php

namespace App\Form;

class Form
{
    private $fields = [];
    private $data = [];
    private $errors = [];

    public function getFields()
    {
        return $this->fields;
    }
    public function addField($name, $type, $id, $value, $label, $options = [])
    {
        $this->fields[$name] =
            [
                'type' => $type,
                'id' => $id,
                'value' => $value,
                'label' => $label,
                'options' => $options
            ];

        return $this;
    }

    public function bind(array $data): void
    {
        $this->data = $data;
        foreach ($this->fields as $name => $field) {
            if (isset($data[$name])) {
                $this->data[$name] = $data[$name];
            } else {
                $this->data[$name] = null;
            }
        }
    }

    public function validate()
    {
        foreach ($this->fields as $name => $field) {
            if (isset($field['options']['required']) && $field['options']['required'] && empty($this->data[$name])) {
                $this->errors[$name] = 'The field ' . $name . ' is required.';
            }
        }
    }

    public function isValid()
    {
        return empty($this->errors);
    }

    public function getData()
    {
        return $this->data;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function handleRequest(array $requestData): void
    {
        $this->bind($requestData);
        $this->validate();
    }
}
