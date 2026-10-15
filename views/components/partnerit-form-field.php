<?php
$fieldName = (string) ($name ?? '');
$fieldId = 'brochure-' . $fieldName;
$fieldType = in_array($type ?? 'text', ['text', 'email', 'tel', 'textarea', 'checkbox'], true) ? ($type ?? 'text') : 'text';
$fieldRequired = (bool) ($required ?? false);
$fieldLabel = (string) ($label ?? '');
?>
<div class="partnerit-form-field<?= $fieldType === 'checkbox' ? ' partnerit-form-field--check' : '' ?><?= !empty($wide) ? ' partnerit-form-field--wide' : '' ?>">
    <?php if ($fieldType === 'checkbox'): ?>
    <label class="partnerit-form-field__check f-body-3" for="<?= e($fieldId) ?>">
        <input id="<?= e($fieldId) ?>" type="checkbox" name="<?= e($fieldName) ?>" value="1"<?= $fieldRequired ? ' required' : '' ?> aria-describedby="<?= e($fieldId) ?>-error">
        <span><?= e($fieldLabel) ?></span>
    </label>
    <?php else: ?>
    <label class="partnerit-form-field__label f-body-3 --semibold" for="<?= e($fieldId) ?>"><?= e($fieldLabel) ?><?= $fieldRequired ? ' (필수)' : ' (선택)' ?></label>
    <?php if ($fieldType === 'textarea'): ?>
    <textarea class="partnerit-form-field__input f-body-3" id="<?= e($fieldId) ?>" name="<?= e($fieldName) ?>" rows="5" maxlength="<?= (int) ($maxlength ?? 2000) ?>" placeholder="<?= e($placeholder ?? '') ?>" aria-describedby="<?= e($fieldId) ?>-error"></textarea>
    <?php else: ?>
    <input class="partnerit-form-field__input f-body-3" id="<?= e($fieldId) ?>" name="<?= e($fieldName) ?>" type="<?= e($fieldType) ?>" maxlength="<?= (int) ($maxlength ?? 100) ?>" autocomplete="<?= e($autocomplete ?? 'off') ?>" placeholder="<?= e($placeholder ?? '') ?>"<?= $fieldRequired ? ' required' : '' ?> aria-describedby="<?= e($fieldId) ?>-error">
    <?php endif; ?>
    <?php endif; ?>
    <span class="partnerit-form-field__error f-body-4" id="<?= e($fieldId) ?>-error" data-field-error="<?= e($fieldName) ?>"></span>
</div>
