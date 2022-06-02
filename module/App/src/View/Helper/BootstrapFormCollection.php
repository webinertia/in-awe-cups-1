<?php

declare(strict_types=1);

namespace App\View\Helper;

use Laminas\Form\Element\Collection as CollectionElement;
use Laminas\Form\ElementInterface;
use Laminas\Form\FieldsetInterface;
use Laminas\Form\LabelAwareInterface;
use Laminas\Form\View\Helper\FormCollection;

use function count;
use function method_exists;
use function sprintf;

class BootstrapFormCollection extends FormCollection
{
    /** @var string $defaultElementHelper */
    protected $defaultElementHelper = 'bootstrapFormRow';

    /**
     * Invoke helper as function
     *
     * Proxies to {@link render()}.
     *
     * @template T as null|ElementInterface
     * @psalm-param T $element
     * @psalm-return (T is null ? self : string)
     * @return string|FormCollection
     */
    public function __invoke(?ElementInterface $element = null, bool $wrap = true)
    {
        if (! $element) {
            return $this;
        }
        $this->setShouldWrap($wrap);
        return $this->render($element);
    }

    /**
     * Render a collection by iterating through all fieldsets and elements
     */
    public function render(ElementInterface $element): string
    {
        // todo: fixme $labelPosition set to empty string as a temp fix for moving to 8.1 support
        $labelPosition = '';
        $renderer      = $this->getView();
        if (! method_exists($renderer, 'plugin')) {
        //Hold off rendering, if the plugin method does not exists
            return '';
        }
        $markup         = '';
        $templateMarkup = '';
        //$this->setDefaultElementHelper('bootstrapFormRow');
        $elementHelper  = $this->getElementHelper();
        $fieldsetHelper = $this->getFieldsetHelper();
        if ($element instanceof CollectionElement && $element->shouldCreateTemplate()) {
            $templateMarkup = $this->renderTemplate($element);
        }
        $this->shouldWrap = false;

        foreach ($element->getIterator() as $elementOrFieldset) {
            if ($elementOrFieldset instanceof FieldsetInterface) {
                $markup .= $fieldsetHelper($elementOrFieldset, $this->shouldWrap());
            } elseif ($elementOrFieldset instanceof ElementInterface) {
                $markup .= $elementHelper($elementOrFieldset, $labelPosition);
            }
        }

        // each collection of elements is palced according to the specified style
        if ($this->shouldWrap) {
            $attributes = $element->getAttributes();
            unset($attributes['name']);
            $attributesString = count($attributes) ? ' ' . $this->createAttributesString($attributes) : '';
            $label            = $element->getLabel();
            $legend           = '';
            if (! empty($label)) {
                if (null !== ($translator = $this->getTranslator())) {
                    $label = $translator->translate($label, $this->getTranslatorTextDomain());
                }

                if (! $element instanceof LabelAwareInterface || ! $element->getLabelOption('disable_html_escape')) {
                    $escapeHtmlHelper = $this->getEscapeHtmlHelper();
                    $label            = $escapeHtmlHelper($label);
                }
                $legend = sprintf($this->labelWrapper, $label);
            }

            $markup = sprintf($this->wrapper, $markup, $legend, $templateMarkup, $attributesString);
        } else {
            $markup .= $templateMarkup;
        }

        return $markup;
    }
}
