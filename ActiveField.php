<?php
namespace avikarsha\form;

use yii\helpers\ArrayHelper;

/**
 * A Bootstrap 3 enhanced version of [[\yii\widgets\ActiveField]].
 *
 * This class adds some useful features to [[\yii\widgets\ActiveField|ActiveField]] to render all
 * sorts of Bootstrap 3 form fields in different form layouts:
 *
 * - [[inputTemplate]] is an optional template to render complex inputs, for example input groups
 * - [[horizontalCssClasses]] defines the CSS grid classes to add to label, wrapper, error and hint
 *   in horizontal forms
 * - [[inline]]/[[inline()]] is used to render inline [[checkboxList()]] and [[radioList()]]
 * - [[enableError]] can be set to `false` to disable to the error
 * - [[enableLabel]] can be set to `false` to disable to the label
 * - [[label()]] can be used with a `boolean` argument to enable/disable the label
 *
 * There are also some new placeholders that you can use in the [[template]] configuration:
 *
 * - `{beginLabel}`: the opening label tag
 * - `{labelTitle}`: the label title for use with `{beginLabel}`/`{endLabel}`
 * - `{endLabel}`: the closing label tag
 * - `{beginWrapper}`: the opening wrapper tag
 * - `{endWrapper}`: the closing wrapper tag
 *
 * The wrapper tag is only used for some layouts and form elements.
 *
 * Note that some elements use slightly different defaults for [[template]] and other options.
 * You may want to override those predefined templates for checkboxes, radio buttons, checkboxLists
 * and radioLists in the [[\yii\widgets\ActiveForm::fieldConfig|fieldConfig]] of the
 * [[\yii\widgets\ActiveForm]]:
 *
 * - [[checkboxTemplate]] the template for checkboxes in default layout
 * - [[radioTemplate]] the template for radio buttons in default layout
 * - [[horizontalCheckboxTemplate]] the template for checkboxes in horizontal layout
 * - [[horizontalRadioTemplate]] the template for radio buttons in horizontal layout
 * - [[inlineCheckboxListTemplate]] the template for inline checkboxLists
 * - [[inlineRadioListTemplate]] the template for inline radioLists
 *
 * Example:
 *
 * ```php
 * use yii\bootstrap\ActiveForm;
 *
 * $form = ActiveForm::begin(['layout' => 'horizontal']);
 *
 * // Form field without label
 * echo $form->field($model, 'demo', [
 *     'inputOptions' => [
 *         'placeholder' => $model->getAttributeLabel('demo'),
 *     ],
 * ])->label(false);
 *
 * // Inline radio list
 * echo $form->field($model, 'demo')->inline()->radioList($items);
 *
 * // Control sizing in horizontal mode
 * echo $form->field($model, 'demo', [
 *     'horizontalCssClasses' => [
 *         'wrapper' => 'col-sm-2',
 *     ]
 * ]);
 *
 * // With 'default' layout you would use 'template' to size a specific field:
 * echo $form->field($model, 'demo', [
 *     'template' => '{label} <div class="row"><div class="col-sm-4">{input}{error}{hint}</div></div>'
 * ]);
 *
 * // Input group
 * echo $form->field($model, 'demo', [
 *     'inputTemplate' => '<div class="input-group"><span class="input-group-addon">@</span>{input}</div>',
 * ]);
 *
 * ActiveForm::end();
 * ```
 *
 * @see \yii\bootstrap\ActiveForm
 * @see http://getbootstrap.com/css/#forms
 *
 * @author Michael Härtl <haertl.mike@gmail.com>
 * @since 2.0
 */
class ActiveField extends \yii\widgets\ActiveField
{
    /**
     * @var boolean whether to render [[checkboxList()]] and [[radioList()]] inline.
     */
    public $inline = false;
    /**
     * @var string|null optional template to render the `{input}` placeholder content
     */
    public $inputTemplate;
    /**
     * @var array options for the wrapper tag, used in the `{beginWrapper}` placeholder
     */
    public $wrapperOptions = [];
    /**
     * @var null|array CSS grid classes for horizontal layout. This must be an array with these keys:
     *  - 'offset' the offset grid class to append to the wrapper if no label is rendered
     *  - 'label' the label grid class
     *  - 'wrapper' the wrapper grid class
     *  - 'error' the error grid class
     *  - 'hint' the hint grid class
     */

    public $horizontalCssClasses;
    /**
     * @var string the template for checkboxes in default layout
     */
    public $checkboxTemplate = "<div class=\"css-checkbox\">\n{input}\n{beginLabel}\n{labelTitle}\n{endLabel}\n{error}\n{hint}\n</div>";
    /**
     * @var string the template for radios in default layout
     */
    public $radioTemplate = "<div class=\"radio\">\n{input}\n{beginLabel}\n{labelTitle}\n{endLabel}\n{error}\n{hint}\n</div>";
    /**
     * @var string the template for checkboxes in horizontal layout
     */
    public $horizontalCheckboxTemplate = "{beginWrapper}\n<div class=\"checkbox\">\n{beginLabel}\n{input}\n{labelTitle}\n{endLabel}\n</div>\n{error}\n{endWrapper}\n{hint}";
    /**
     * @var string the template for radio buttons in horizontal layout
     */
    public $horizontalRadioTemplate = "{beginWrapper}\n<div class=\"radio\">\n{beginLabel}\n{input}\n{labelTitle}\n{endLabel}\n</div>\n{error}\n{endWrapper}\n{hint}";
    /**
     * @var string the template for inline checkboxLists
     */
    public $inlineCheckboxListTemplate = "{label}\n{beginWrapper}\n{input}\n{error}\n{endWrapper}\n{hint}";
    /**
     * @var string the template for inline radioLists
     */
    public $inlineRadioListTemplate = "{beginWrapper}\n{input}\n{error}\n{endWrapper}\n{hint}";
    /**
     * @var boolean whether to render the error. Default is `true` except for layout `inline`.
     */
    public $enableError = true;
    /**
     * @var boolean whether to render the label. Default is `true`.
     */
    public $enableLabel = true;

    /**
     * @var array the HTML attributes (name-value pairs) for the field container tag.
     * The values will be HTML-encoded using [[Html::encode()]].
     * If a value is null, the corresponding attribute will not be rendered.
     * The following special options are recognized:
     *
     * - tag: the tag name of the container element. Defaults to "div".
     *
     * If you set a custom `id` for the container element, you may need to adjust the [[$selectors]] accordingly.
     *
     * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public $options = ['class' => 'tag textInput'];
    /**
     * @var string the template that is used to arrange the label, the input field, the error message and the hint text.
     * The following tokens will be replaced when [[render()]] is called: `{label}`, `{input}`, `{error}` and `{hint}`.
     */
    public $template = "{input}\n{hint}\n{error}";
    /**
     * @var array the default options for the input tags. The parameter passed to individual input methods
     * (e.g. [[textInput()]]) will be merged with this property when rendering the input tag.
     *
     * If you set a custom `id` for the input element, you may need to adjust the [[$selectors]] accordingly.
     *
     * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public $inputOptions = ['class' => 'form-control'];
    /**
     * @var array the default options for the error tags. The parameter passed to [[error()]] will be
     * merged with this property when rendering the error tag.
     * The following special options are recognized:
     *
     * - tag: the tag name of the container element. Defaults to "div".
     * - encode: whether to encode the error output. Defaults to true.
     *
     * If you set a custom `id` for the error element, you may need to adjust the [[$selectors]] accordingly.
     *
     * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public $errorOptions = ['class' => 'help-block'];
    /**
     * @var array the default options for the label tags. The parameter passed to [[label()]] will be
     * merged with this property when rendering the label tag.
     * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public $labelOptions = ['class' => 'control-label'];
    /**
     * @var array the default options for the hint tags. The parameter passed to [[hint()]] will be
     * merged with this property when rendering the hint tag.
     * The following special options are recognized:
     *
     * - tag: the tag name of the container element. Defaults to "div".
     *
     * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public $hintOptions = ['class' => 'hint-block'];

    /**
     * @var string content to be placed before input
     */
    public $contentBeforeInput = '';
    /**
     * @var string content to be placed after input
     */
    public $contentAfterInput = '';
    /**
     * @var array addon options for text and password inputs. The following settings can be configured:
     * - prepend: array the prepend addon configuration
     * - content: string the prepend addon content
     * - asButton: boolean whether the addon is a button or button group. Defaults to false.
     * - options: array the HTML attributes to be added to the container.
     * - append: array the append addon configuration
     * - content: string/array the append addon content
     * - asButton: boolean whether the addon is a button or button group. Defaults to false.
     * - options: array the HTML attributes to be added to the container.
     * - groupOptions: array HTML options for the input group
     * - contentBefore: string content placed before addon
     * - contentAfter: string content placed after addon
     */
    public $addon = [];
    /**
    * @var boolean is it a static input
    */
    protected $_isStatic = false;
    /**
     * @var array the settings for the active field layout
     */
    protected $_settings = [
        'input' => '{input}',
        'error' => '{error}',
        'hint' => '{hint}',
        'showLabels' => true,
        'showErrors' => true,
    ];





    /**
     * @inheritdoc
     */
    public function __construct($config = [])
    {
        $layoutConfig = $this->createLayoutConfig($config);
        $config = ArrayHelper::merge($layoutConfig, $config);
        parent::__construct($config);
    }


    /**
     * @inheritdoc
     */
    public function render($content = null)
    {
        if ($content === null) {
            if (!isset($this->parts['{beginWrapper}'])) {
                $options = $this->wrapperOptions;
                $tag = ArrayHelper::remove($options, 'tag', 'div');
                $this->parts['{beginWrapper}'] = Html::beginTag($tag, $options);
                $this->parts['{endWrapper}'] = Html::endTag($tag);
            }
            if ($this->enableLabel === false) {
                $this->parts['{label}'] = '';
                $this->parts['{beginLabel}'] = '';
                $this->parts['{labelTitle}'] = '';
                $this->parts['{endLabel}'] = '';
            } elseif (!isset($this->parts['{beginLabel}'])) {
                $this->renderLabelParts();
            }
            if ($this->enableError === false) {
                $this->parts['{error}'] = '';
            }
            if ($this->inputTemplate) {
                $input = isset($this->parts['{input}']) ?
                    $this->parts['{input}'] : Html::activeTextInput($this->model, $this->attribute, $this->inputOptions);

                $this->parts['{input}'] = strtr($this->inputTemplate, ['{input}' => $input]);
            }
        }

        $this->buildTemplate();

        return parent::render($content);
    }


    /**
     * @inheritdoc
     */
    public function checkbox($options = [], $enclosedByLabel = true)
    {
        if ($enclosedByLabel) {
            if (!isset($options['template'])) {
                $this->template = $this->form->layout === 'horizontal' ?
                    $this->horizontalCheckboxTemplate : $this->checkboxTemplate;
            } else {
                $this->template = $options['template'];
                unset($options['template']);
            }
            if (isset($options['label'])) {
                $this->parts['{labelTitle}'] = $options['label'];
            }
            if ($this->form->layout === 'horizontal') {
                Html::addCssClass($this->wrapperOptions, $this->horizontalCssClasses['offset']);
            }
            $this->labelOptions['class'] = null;
        }

        return parent::checkbox($options, false);
    }

    /**
     * @inheritdoc
     */
    public function radio($options = [], $enclosedByLabel = true)
    {
        if ($enclosedByLabel) {
            if (!isset($options['template'])) {
                $this->template = $this->form->layout === 'horizontal' ?
                    $this->horizontalRadioTemplate : $this->radioTemplate;
            } else {
                $this->template = $options['template'];
                unset($options['template']);
            }
            if (isset($options['label'])) {
                $this->parts['{labelTitle}'] = $options['label'];
            }
            if ($this->form->layout === 'horizontal') {
                Html::addCssClass($this->wrapperOptions, $this->horizontalCssClasses['offset']);
            }
            $this->labelOptions['class'] = null;
        }

        return parent::radio($options, false);
    }

    /**
     * @inheritdoc
     */
    public function checkboxList($items, $options = [])
    {
        if ($this->inline) {
            if (!isset($options['template'])) {
                $this->template = $this->inlineCheckboxListTemplate;
            } else {
                $this->template = $options['template'];
                unset($options['template']);
            }
            if (!isset($options['itemOptions'])) {
                $options['itemOptions'] = [
                    'labelOptions' => ['class' => 'checkbox-inline'],
                ];
            }
        }  elseif (!isset($options['item'])) {
            $options['item'] = function ($index, $label, $name, $checked, $value) {
                return '<div class="checkbox">' . Html::checkbox($name, $checked, ['label' => $label, 'value' => $value]) . '</div>';
            };
        }
        parent::checkboxList($items, $options);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function radioList($items, $options = [])
    {
        if ($this->inline) {
            if (!isset($options['template'])) {
                $this->template = $this->inlineRadioListTemplate;
            } else {
                $this->template = $options['template'];
                unset($options['template']);
            }
            if (!isset($options['itemOptions'])) {
                $options['itemOptions'] = [
                    'labelOptions' => ['class' => 'radio-inline'],
                ];
            }
        }  elseif (!isset($options['item'])) {
            $options['item'] = function ($index, $label, $name, $checked, $value) {
                return '<div class="cell">' . Html::radio($name, $checked, ['label' => $label, 'value' => $value]) . '</div>';
            };
        }
        parent::radioList($items, $options);
        return $this;
    }

    /**
     * Renders Bootstrap static form control.
     * @param array $options the tag options in terms of name-value pairs. These will be rendered as
     * the attributes of the resulting tag. There are also a special options:
     *
     * - encode: boolean, whether value should be HTML-encoded or not.
     *
     * @return $this the field object itself
     * @since 2.0.5
     * @see http://getbootstrap.com/css/#forms-controls-static
     */
    public function staticControl($options = [])
    {
        $this->adjustLabelFor($options);
        $this->parts['{input}'] = Html::activeStaticControl($this->model, $this->attribute, $options);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function label($label = null, $options = [])
    {
        if (is_bool($label)) {
            $this->enableLabel = $label;
            if ($label === false && $this->form->layout === 'horizontal') {
                Html::addCssClass($this->wrapperOptions, $this->horizontalCssClasses['offset']);
            }
        } else {
            $this->enableLabel = true;
            $this->renderLabelParts($label, $options);
            parent::label($label, $options);
        }
        return $this;
    }

    /**
     * @param boolean $value whether to render a inline list
     * @return $this the field object itself
     * Make sure you call this method before [[checkboxList()]] or [[radioList()]] to have any effect.
     */
    public function inline($value = true)
    {
        $this->inline = (bool) $value;
        return $this;
    }

    /**
     * @param array $instanceConfig the configuration passed to this instance's constructor
     * @return array the layout specific default configuration for this instance
     */
    protected function createLayoutConfig($instanceConfig)
    {
        $config = [
            'hintOptions' => [
                'tag' => 'p',
                'class' => 'help-block',
            ],
            'errorOptions' => [
                'tag' => 'p',
                'class' => 'help-block help-block-error',
            ],
            'inputOptions' => [
                'class' => 'form-control',
            ],
        ];

        $layout = $instanceConfig['form']->layout;

        if ($layout === 'horizontal') {
            $config['template'] = "{label}\n{beginWrapper}\n{input}\n{error}\n{endWrapper}\n{hint}";
            $cssClasses = [
                'offset' => 'col-sm-offset-3',
                'label' => 'col-sm-3',
                'wrapper' => 'col-sm-6',
                'error' => '',
                'hint' => 'col-sm-3',
            ];
            if (isset($instanceConfig['horizontalCssClasses'])) {
                $cssClasses = ArrayHelper::merge($cssClasses, $instanceConfig['horizontalCssClasses']);
            }
            $config['horizontalCssClasses'] = $cssClasses;
            $config['wrapperOptions'] = ['class' => $cssClasses['wrapper']];
            $config['labelOptions'] = ['class' => 'control-label ' . $cssClasses['label']];
            $config['errorOptions'] = ['class' => 'help-block help-block-error ' . $cssClasses['error']];
            $config['hintOptions'] = ['class' => 'help-block ' . $cssClasses['hint']];
        } elseif ($layout === 'inline') {
            $config['labelOptions'] = ['class' => 'sr-only'];
            $config['enableError'] = false;
        }

        return $config;
    }

    /**
     * @param string|null $label the label or null to use model label
     * @param array $options the tag options
     */
    protected function renderLabelParts($label = null, $options = [])
    {
        $options = array_merge($this->labelOptions, $options);
        if ($label === null) {
            if (isset($options['label'])) {
                $label = $options['label'];
                unset($options['label']);
            } else {
                $attribute = Html::getAttributeName($this->attribute);
                $label = Html::encode($this->model->getAttributeLabel($attribute));
            }
        }
        if (!isset($options['for'])) {
            $options['for'] = Html::getInputId($this->model, $this->attribute);
        }
        $this->parts['{beginLabel}'] = Html::beginTag('label', $options);
        $this->parts['{endLabel}'] = Html::endTag('label');
        if (!isset($this->parts['{labelTitle}'])) {
            $this->parts['{labelTitle}'] = $label;
        }
    }

    /* ================================= Newly added for addons ========================================== */

    /**
    * Parses and returns addon content
    *
    * @param string|array $addon the addon parameter
    *
    * @return string
    */
    public static function getPrependAddonContent($addon)
    {
        if (!is_array($addon)) {
            return $addon;
        }
        $content = ArrayHelper::getValue($addon, 'content', '');
        $options = ArrayHelper::getValue($addon, 'options', []);
        if (ArrayHelper::getValue($addon, 'asButton', false) == true) {
            Html::addCssClass($options, 'input-group-btn');
            return Html::tag('span', $content, $options);
        } else {
            Html::addCssClass($options, 'icon-left');
            return Html::tag('span', $content, $options);
        }
    }

    /**
    * Parses and returns addon content
    *
    * @param string|array $addon the addon parameter
    *
    * @return string
    */
    public static function getAppendAddonContent($addon)
    {
        if (!is_array($addon)) {
            return $addon;
        }
        $content = ArrayHelper::getValue($addon, 'content', '');
        $options = ArrayHelper::getValue($addon, 'options', []);
        if (ArrayHelper::getValue($addon, 'asButton', false) == true) {
            Html::addCssClass($options, 'input-group-btn');
            return Html::tag('span', $content, $options);
        } else {
            Html::addCssClass($options, 'icon-right');
            return Html::tag('span', $content, $options);
        }
    }

    /**
     * Builds the field layout parts
     *
     * @param bool $showLabels whether to show labels
     * @param bool $showErrors whether to show errors
     */
    protected function buildLayoutParts($showLabels, $showErrors)
    {
        // if (!$showErrors) {
        //     $this->_settings['error'] = '';
        // }
        // if ($this->skipFormLayout) {
        //     $this->mergeSettings($showLabels, $showErrors);
        //     return;
        // }
        $inputDivClass = '';
        // $errorDivClass = '';
        // if ($this->form->hasInputCss()) {
        //     $offsetDivClass = $this->form->getOffsetCss();
        //     $inputDivClass = ($this->_offset) ? $offsetDivClass : $this->form->getInputCss();
        //     if ($showLabels === false || $showLabels === ActiveForm::SCREEN_READER) {
        //         $size = ArrayHelper::getValue($this->form->formConfig, 'deviceSize', ActiveForm::SIZE_MEDIUM);
        //         $errorDivClass = "col-{$size}-{$this->form->fullSpan}";
        //         $inputDivClass = $errorDivClass;
        //     } elseif ($this->form->hasOffsetCss()) {
        //         $errorDivClass = $offsetDivClass;
        //     }
        // }
        $this->setLayoutContainer('input', $inputDivClass);
        // $this->setLayoutContainer('error', $errorDivClass, $showErrors);
        // $this->setLayoutContainer('hint', $errorDivClass);
        // $this->mergeSettings($showLabels, $showErrors);
    }

    /**
     * Sets the layout element container
     *
     * @param string $type the layout element type
     * @param string $css the css class for the container
     * @param bool   $chk whether to create the container for the layout element
     */
    protected function setLayoutContainer($type, $css = '', $chk = true)
    {
        if (!empty($css) && $chk) {
            $this->_settings[$type] = "<div class='{$css}'>{{$type}}</div>";
        }
    }

    /**
     * Builds the final template based on the bootstrap form type, display settings for label, error, and hint, and
     * content before and after label, input, error, and hint
     */
    protected function buildTemplate()
    {
        $showLabels = $showErrors = $input = $error = null;
        extract($this->_settings);
        if ($this->_isStatic && $this->showErrors !== true) {
            $showErrors = false;
        }
        // $showLabels = $showLabels && $this->hasLabels();
        // $this->buildLayoutParts($showLabels, $showErrors);
        extract($this->_settings);
        // if (!empty($this->_multiselect)) {
        //     $input = str_replace('{input}', $this->_multiselect, $input);
        // }
        // if ($this->_isHintSpecial && $this->getHintData('iconBesideInput') && $this->getHintData('showIcon')) {
        //     $help = str_replace('{help}', $this->getHintIcon(), $this->getHintData('inputTemplate'));
        //     $input = str_replace('{input}', $help, $input);
        // }
        $newInput = $this->contentBeforeInput . $this->generateAddon() . $this->contentAfterInput;
        // $newError = "{$this->contentBeforeError}{error}{$this->contentAfterError}";
        $this->template = strtr($this->template, [
           // '{label}' => $showLabels ? "{$this->contentBeforeLabel}{label}{$this->contentAfterLabel}" : "",
           '{input}' => str_replace('{input}', $newInput, $input),
           // '{error}' => $showErrors ? str_replace('{error}', $newError, $error) : '',
       ]);
    }

    /**
     * Generates the addon markup
     *
     * @return string
     */
    protected function generateAddon()
    {
        if (empty($this->addon)) {
            return '{input}';
        }
        $addon = $this->addon;
        $prepend = static::getPrependAddonContent(ArrayHelper::getValue($addon, 'prepend', ''));
        $append = static::getAppendAddonContent(ArrayHelper::getValue($addon, 'append', ''));
        $content = $prepend . '{input}' . $append;
        // $group = ArrayHelper::getValue($addon, 'groupOptions', []);
        // Html::addCssClass($group, 'input-group');
        $contentBefore = ArrayHelper::getValue($addon, 'contentBefore', '');
        $contentAfter = ArrayHelper::getValue($addon, 'contentAfter', '');
        // $content = Html::tag('div', $contentBefore . $content . $contentAfter, $group);
        $content = $contentBefore . $content . $contentAfter;
        return $content;
    }


}
