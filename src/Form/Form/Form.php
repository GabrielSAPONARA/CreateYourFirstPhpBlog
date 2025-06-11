<?php

namespace App\Form\Form;

class Form
{
    private $fields = [];
    private $data = [];
    private $errors = [];

    /**
     * @return array
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @param $name
     * @param $type
     * @param $id
     * @param $value
     * @param $label
     * @param $options
     * @return $this
     */
    public function addField($name, $type, $id, $value, $label, $options = [])
    {
        $this->fields[$name] =
            [
                'type'    => $type,
                'id'      => $id,
                'value'   => $value,
                'label'   => $label,
                'options' => $options
            ];

        return $this;
    }

    /**
     * @param array $data
     * @return void
     */
    public function bind(array $data): void
    {
        $this->data = $data;
        foreach ($this->fields as $name => $field)
        {
            if (isset($data[$name]))
            {
                $this->data[$name] = $data[$name];
            }
            else
            {
                $this->data[$name] = null;
            }
        }
    }

    /**
     * @return void
     */
    public function validate(): void
    {
        foreach ($this->fields as $name => $field)
        {
            if (isset($field['options']['required']) &&
                $field['options']['required'] && empty($this->data[$name]))
            {
                $this->errors[$name] = 'The field ' . $name . ' is required.';
            }
        }
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        return empty($this->errors);
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @param array $requestData
     * @return void
     */
    public function handleRequest(array $requestData): void
    {
        $this->bind($requestData);
        $this->validate();
    }
}
