<?php
/** Hidden anti-abuse fields shared by every public form. $formType = quote|proposal|contact|apply */
?>
<input type="hidden" name="form" value="<?= e($formType) ?>">
<input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
<div style="position:absolute;left:-9999px" aria-hidden="true"><label>Leave empty<input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>
