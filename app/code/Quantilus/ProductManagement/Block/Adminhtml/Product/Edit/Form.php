<?php
namespace Quantilus\ProductManagement\Block\Adminhtml\Product\Edit;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Framework\Data\Form as DataForm;

class Form extends Generic
{
    protected function _prepareForm()
    {
        $form = $this->_formFactory->create([
            'data' => [
                'id' => 'edit_form',
                'action' => $this->getUrl('*/*/save'),
                'method' => 'post',
            ],
        ]);
        $form->setUseContainer(true);

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Product Information')]);

        $fieldset->addField(
            'sku',
            'text',
            [
                'name' => 'sku',
                'label' => __('SKU'),
                'title' => __('SKU'),
                'required' => true,
            ]
        );

        $fieldset->addField(
            'name',
            'text',
            [
                'name' => 'name',
                'label' => __('Name'),
                'title' => __('Name'),
                'required' => true,
            ]
        );

        $fieldset->addField(
            'description',
            'textarea',
            [
                'name' => 'description',
                'label' => __('Description'),
                'title' => __('Description'),
                'required' => true,
            ]
        );

        $fieldset->addField(
            'price',
            'text',
            [
                'name' => 'price',
                'label' => __('Price'),
                'title' => __('Price'),
                'required' => true,
            ]
        );

        $this->setForm($form);
        return parent::_prepareForm();
    }
}